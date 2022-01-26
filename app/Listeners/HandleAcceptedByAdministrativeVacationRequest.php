<?php

declare(strict_types=1);

namespace Toby\Listeners;

use Toby\Events\VacationRequestAcceptedByAdministrative;
use Toby\Helpers\VacationRequestStateManager;

class HandleAcceptedByAdministrativeVacationRequest
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
    ) {
    }

    public function handle(VacationRequestAcceptedByAdministrative $event): void
    {
        $this->stateManager->approve($event->vacationRequest);
    }
}
