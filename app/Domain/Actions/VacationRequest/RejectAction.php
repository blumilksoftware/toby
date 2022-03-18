<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestRejectedNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class RejectAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
    ) {}

    public function execute(VacationRequest $vacationRequest, User $user): void
    {
        $this->stateManager->reject($vacationRequest, $user);

        $this->notify($vacationRequest);
    }

    protected function notify(VacationRequest $vacationRequest): void
    {
        $users = User::query()
            ->whereIn("role", [Role::TechnicalApprover, Role::AdministrativeApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestRejectedNotification($vacationRequest, $user));
        }

        $vacationRequest->user->notify(new VacationRequestRejectedNotification($vacationRequest, $vacationRequest->user));
    }
}
