<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestCreated;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;

class HandleCreatedVacationRequest
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
        protected VacationRequestStateManager $stateManager,
    ) {
    }

    public function handle(VacationRequestCreated $event): void
    {
        $vacationRequest = $event->vacationRequest;

        if ($vacationRequest->hasFlowSkipped()) {
            $this->stateManager->approve($vacationRequest);

            return;
        }

        if ($this->configRetriever->needsTechnicalApproval($vacationRequest->type)) {
            $this->stateManager->waitForTechnical($vacationRequest);

            return;
        }

        if ($this->configRetriever->needsAdministrativeApproval($vacationRequest->type)) {
            $this->stateManager->waitForAdministrative($vacationRequest);

            return;
        }

        $this->stateManager->approve($vacationRequest);
    }
}
