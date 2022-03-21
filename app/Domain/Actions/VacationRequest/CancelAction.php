<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestStatusChangedNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Jobs\ClearVacationRequestDaysInGoogleCalendar;

class CancelAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
    ) {}

    public function execute(VacationRequest $vacationRequest, User $user): void
    {
        $this->stateManager->cancel($vacationRequest, $user);

        ClearVacationRequestDaysInGoogleCalendar::dispatch($vacationRequest);

        $this->notify($vacationRequest);
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
