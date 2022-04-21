<?php

declare(strict_types=1);

namespace Toby\Domain\Actions\VacationRequest;

use Toby\Domain\Enums\Role;
use Toby\Domain\Notifications\VacationRequestWaitsForApprovalNotification;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;

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
    }

    protected function notifyTechApprovers(VacationRequest $vacationRequest): void
    {
        $users = User::query()
            ->whereIn("role", [Role::TechnicalApprover, Role::Administrator])
            ->get();

        foreach ($users as $user) {
            $user->notify(new VacationRequestWaitsForApprovalNotification($vacationRequest, $user));
        }
    }
}
