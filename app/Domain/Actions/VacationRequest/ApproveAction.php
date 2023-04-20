<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Toby\Domain\Enums\Role;
use Toby\Domain\Events\VacationRequestChanged;
use Toby\Domain\Notifications\VacationRequestStatusChangedNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Jobs\SendVacationRequestDaysToGoogleCalendar;

class ApproveAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function execute(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->stateManager->approve($vacationRequest, $user);

        if ($this->configRetriever->isVacation($vacationRequest->type)) {
            SendVacationRequestDaysToGoogleCalendar::dispatch($vacationRequest);

            $this->notify($vacationRequest);
        }

        event(new VacationRequestChanged($vacationRequest));

    }

    protected function notify(VacationRequest $vacationRequest): void
    {
        $users = User::query()
            ->where("id", "!=", $vacationRequest->user->id)
            ->whereIn("role", [Role::TechnicalApprover, Role::AdministrativeApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $user));
        }

        $vacationRequest->user->notify(new VacationRequestStatusChangedNotification($vacationRequest, $vacationRequest->user));
    }
}
