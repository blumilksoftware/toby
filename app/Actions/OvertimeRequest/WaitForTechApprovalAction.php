<?php

declare(strict_types=1);

namespace Toby\Actions\OvertimeRequest;

use Toby\Actions\VacationRequest\ApproveAction;
use Toby\Domain\OvertimeRequestStateManager;
use Toby\Models\OvertimeRequest;

class WaitForTechApprovalAction
{
    public function __construct(
        protected OvertimeRequestStateManager $stateManager,
        protected ApproveAction $approveAction,
    ) {}

    public function execute(OvertimeRequest $overtimeRequest): void
    {
        $this->stateManager->waitForTechnical($overtimeRequest);
    }
}
