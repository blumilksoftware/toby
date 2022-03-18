<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestWaitsForAdminApprovalNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

class WaitForAdminApprovalAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected VacationTypeConfigRetriever $configRetriever,
        protected ApproveAction $approveAction,
    ) {}

    public function execute(VacationRequest $vacationRequest): void
    {
        $this->stateManager->waitForAdministrative($vacationRequest);

        $this->waitForAdminApprovers($vacationRequest);
    }

    protected function waitForAdminApprovers(VacationRequest $vacationRequest): void
    {
        $users = User::query()
            ->whereIn("role", [Role::AdministrativeApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestWaitsForAdminApprovalNotification($vacationRequest, $user));
        }
    }
}
