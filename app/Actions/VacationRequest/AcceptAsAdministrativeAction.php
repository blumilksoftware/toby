<?php

declare(strict_types=1);

namespace Toby\Actions\VacationRequest;

use Toby\Domain\VacationRequestStateManager;
use Toby\Models\User;
use Toby\Models\VacationRequest;

class AcceptAsAdministrativeAction
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
        protected ApproveAction $approveAction,
    ) {}

    public function execute(VacationRequest $vacationRequest, User $user): void
    {
        $this->stateManager->acceptAsAdministrative($vacationRequest, $user);

        $this->approveAction->execute($vacationRequest);
    }
}
