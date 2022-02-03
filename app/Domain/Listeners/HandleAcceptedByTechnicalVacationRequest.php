<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestAcceptedByTechnical;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationTypeConfigRetriever;

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
