<?php

declare(strict_types=1);

namespace Toby\Listeners;

use Toby\Events\VacationRequestAcceptedByTechnical;
use Toby\Helpers\VacationRequestStateManager;
use Toby\Helpers\VacationTypeConfigRetriever;

class HandleAcceptedByTechnicalVacationRequest
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
        protected VacationRequestStateManager $stateManager,
    ) {
    }

    public function handle(VacationRequestAcceptedByTechnical $event): void
    {
        $vacationRequest = $event->vacationRequest;

        if ($this->configRetriever->needsAdministrativeApproval($vacationRequest->type)) {
            $this->stateManager->waitForAdministrative($vacationRequest);

            return;
        }

        $this->stateManager->approve($vacationRequest);
    }
}
