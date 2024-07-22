<?php

declare(strict_types=1);

namespace Toby\Actions\OvertimeRequest;

use Spatie\Permission\Models\Permission;
use Toby\Domain\OvertimeRequestStateManager;
use Toby\Models\OvertimeRequest;
use Toby\Notifications\OvertimeRequestsWaitsForApprovalNotification;

class WaitForTechApprovalAction
{
    public function __construct(
        protected OvertimeRequestStateManager $stateManager,
        protected ApproveAction $approveAction,
    ) {}

    public function execute(OvertimeRequest $overtimeRequest): void
    {
        $this->stateManager->waitForTechnical($overtimeRequest);

        $this->notifyAuthorizedUsers($overtimeRequest);
    }

    protected function notifyAuthorizedUsers(OvertimeRequest $overtimeRequest): void
    {
        $users = Permission::findByName("manageOvertimeAsTechnicalApprover")
            ->users()
            ->with("permissions")
            ->get();

        foreach ($users as $user) {
            $user->notify(new OvertimeRequestsWaitsForApprovalNotification($overtimeRequest, $user));
        }
    }
}
