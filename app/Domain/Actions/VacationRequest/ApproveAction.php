<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestApprovedNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Jobs\SendVacationRequestDaysToGoogleCalendar;

class ApproveAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
    ) {}

    public function execute(VacationRequest $vacationRequest, ?User $user = null): void
    {
        $this->stateManager->approve($vacationRequest, $user);

        SendVacationRequestDaysToGoogleCalendar::dispatch($vacationRequest);

        $this->notify($vacationRequest);
    }

    protected function notify(VacationRequest $vacationRequest): void
    {
        $users = User::query()
            ->whereIn("role", [Role::TechnicalApprover, Role::AdministrativeApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestApprovedNotification($vacationRequest, $user));
        }

        $vacationRequest->user->notify(new VacationRequestApprovedNotification($vacationRequest, $vacationRequest->user));
    }
}
