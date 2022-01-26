<?php

declare(strict_types=1);

namespace Toby\Listeners;

use Toby\Events\VacationRequestCreated;
use Toby\Helpers\VacationRequestStateManager;
use Toby\Helpers\VacationTypeConfigRetriever;

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
