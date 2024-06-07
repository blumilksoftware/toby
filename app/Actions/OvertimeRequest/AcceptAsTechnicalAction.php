<?php

declare(strict_types=1);

namespace Toby\Actions\OvertimeRequest;

use Toby\Domain\OvertimeRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;

class AcceptAsTechnicalAction
{
    public function __construct(
        protected OvertimeRequestStateManager $stateManager,
        protected VacationTypeConfigRetriever $configRetriever,
        protected ApproveAction $approveAction,
    ) {}

    public function execute(OvertimeRequest $overtimeRequest, User $user): void
    {
        $this->stateManager->acceptAsTechnical($overtimeRequest, $user);

        $this->approveAction->execute($overtimeRequest);
    }
}
