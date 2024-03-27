<?php

declare(strict_types=1);

namespace Toby\Actions\VacationRequest;

use Spatie\Permission\Models\Permission;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Events\VacationRequestChanged;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Notifications\VacationRequestWaitsForApprovalNotification;

class WaitForTechApprovalAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected VacationTypeConfigRetriever $configRetriever,
        protected ApproveAction $approveAction,
    ) {}

    public function execute(VacationRequest $vacationRequest): void
    {
        $this->stateManager->waitForTechnical($vacationRequest);

        if ($this->configRetriever->isVacation($vacationRequest->type)) {
            $this->notifyTechApprovers($vacationRequest);
        }

        event(new VacationRequestChanged($vacationRequest));
    }

    protected function notifyTechApprovers(VacationRequest $vacationRequest): void
    {
        $users = Permission::findByName("receiveVacationRequestWaitsForApprovalNotification")
            ->users()
            ->with("permissions")
            ->get();

        $users = $users->filter(fn(User $user): bool => $user->can("acceptAsTechApprover", $vacationRequest));

        foreach ($users as $user) {
            $user->notify(new VacationRequestWaitsForApprovalNotification($vacationRequest, $user));
        }
    }
}
