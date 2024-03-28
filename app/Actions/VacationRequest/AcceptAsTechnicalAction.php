<?php

declare(strict_types=1);

namespace Toby\Actions\VacationRequest;

use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Models\User;
use Toby\Models\VacationRequest;

class AcceptAsTechnicalAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected VacationTypeConfigRetriever $configRetriever,
        protected WaitForAdminApprovalAction $waitForAdminApprovalAction,
        protected ApproveAction $approveAction,
    ) {}

    public function execute(VacationRequest $vacationRequest, User $user): void
    {
        $this->stateManager->acceptAsTechnical($vacationRequest, $user);

        if ($this->configRetriever->needsAdministrativeApproval($vacationRequest->type)) {
            $this->waitForAdminApprovalAction->execute($vacationRequest);

            return;
        }

        $this->approveAction->execute($vacationRequest);
    }
}
