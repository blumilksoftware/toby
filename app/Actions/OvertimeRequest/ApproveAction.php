<?php

declare(strict_types=1);

namespace Toby\Actions\OvertimeRequest;

use Toby\Domain\OvertimeRequestStateManager;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;

class ApproveAction
{
    public function __construct(
        protected OvertimeRequestStateManager $stateManager,
    ) {}

    public function execute(OvertimeRequest $overtimeRequest, ?User $user = null): void
    {
        $this->stateManager->approve($overtimeRequest, $user);
    }
}
