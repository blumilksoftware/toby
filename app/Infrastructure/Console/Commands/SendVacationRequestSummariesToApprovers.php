<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestsSummaryNotification;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class SendVacationRequestSummariesToApprovers extends Command
{
    protected $signature = "toby:send-vacation-request-reminders";
    protected $description = "Sends vacation request reminders to approvers if they didn't approve";

    public function handle(): void
    {
        $users = User::query()
            ->whereIn("role", [Role::AdministrativeApprover, Role::TechnicalApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $vacationRequests = VacationRequest::query()
                ->states(VacationRequestStatesRetriever::waitingForUserActionStates($user))
                ->get();

            if ($vacationRequests->isNotEmpty()) {
                $user->notify(new VacationRequestsSummaryNotification(Carbon::today(), $vacationRequests));
            }
        }
    }
}
