<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Spatie\Permission\Models\Permission;
use Toby\Domain\Events\VacationRequestChanged;
use Toby\Domain\Notifications\VacationRequestWaitsForApprovalNotification;
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

        if ($this->configRetriever->isVacation($vacationRequest->type)) {
            $this->notifyAuthorizedUsers($vacationRequest);
        }

        event(new VacationRequestChanged($vacationRequest));
    }

    protected function notifyAuthorizedUsers(VacationRequest $vacationRequest): void
    {
        $users = Permission::findByName("receiveVacationRequestWaitsForApprovalNotification")
            ->users()
            ->with("permissions")
            ->get();

        $users = $users->filter(fn(User $user): bool => $user->can("acceptAsAdminApprover", $vacationRequest));

        foreach ($users as $user) {
            $user->notify(new VacationRequestWaitsForApprovalNotification($vacationRequest, $user));
        }
    }
}
