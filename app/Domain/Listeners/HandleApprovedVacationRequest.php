<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestApproved;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Infrastructure\Jobs\SendVacationRequestDaysToGoogleCalendar;

class HandleApprovedVacationRequest
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {
    }

    public function handle(VacationRequestApproved $event): void
    {
        SendVacationRequestDaysToGoogleCalendar::dispatch($event->vacationRequest);
    }
}
