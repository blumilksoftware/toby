<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Toby\Domain\Enums\Role;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\Notifications\VacationRequestWaitsForApprovalNotification;
use Toby\Domain\States\VacationRequest\WaitingForAdministrative;
use Toby\Domain\States\VacationRequest\WaitingForTechnical;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Domain\WorkDaysCalculator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class SendVacationRequestRemindersToApprovers extends Command
{
    public const REMINDER_INTERVAL = 3;

    protected $signature = "toby:send-vacation-request-reminders";
    protected $description = "Sends vacation request reminders to approvers if they didn't approve";

    public function handle(VacationTypeConfigRetriever $configRetriever, WorkDaysCalculator $daysCalculator): void
    {
        $vacationRequests = VacationRequest::query()
            ->type(VacationType::all()->filter(fn(VacationType $type) => $configRetriever->isVacation($type))->all())
            ->get();

        /** @var VacationRequest $vacationRequest */
        foreach ($vacationRequests as $vacationRequest) {
            if (!$this->shouldNotify($vacationRequest, $daysCalculator)) {
                continue;
            }

            if ($vacationRequest->state->equals(WaitingForTechnical::class)) {
                $this->notifyTechnicalApprovers($vacationRequest);
            }

            if ($vacationRequest->state->equals(WaitingForAdministrative::class)) {
                $this->notifyAdminApprovers($vacationRequest);
            }
        }
    }

    protected function shouldNotify(VacationRequest $vacationRequest, WorkDaysCalculator $daysCalculator): bool
    {
        $days = $daysCalculator
            ->calculateDays($vacationRequest->updated_at->addDay(), Carbon::today())
            ->count();

        return $days >= static::REMINDER_INTERVAL && ($days % static::REMINDER_INTERVAL === 0);
    }

    protected function notifyAdminApprovers(VacationRequest $vacationRequest): void
    {
        $users = User::query()
            ->whereIn("role", [Role::AdministrativeApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestWaitsForApprovalNotification($vacationRequest, $user));
        }
    }

    protected function notifyTechnicalApprovers(VacationRequest $vacationRequest): void
    {
        $users = User::query()
            ->whereIn("role", [Role::TechnicalApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestWaitsForApprovalNotification($vacationRequest, $user));
        }
    }
}
