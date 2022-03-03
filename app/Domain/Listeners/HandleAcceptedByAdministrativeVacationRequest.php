<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestAcceptedByAdministrative;
use Toby\Domain\VacationRequestStateManager;

class HandleAcceptedByAdministrativeVacationRequest
{
    public function __construct(
        protected VacationRequestStateManager $stateManager,
    ) {}

    public function handle(VacationRequestAcceptedByAdministrative $event): void
    {
        $this->stateManager->approve($event->vacationRequest);
    }
}
