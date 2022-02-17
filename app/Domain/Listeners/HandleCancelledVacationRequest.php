<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestCancelled;
use Toby\Infrastructure\Jobs\ClearVacationRequestDaysInGoogleCalendar;

class HandleCancelledVacationRequest
{
    public function handle(VacationRequestCancelled $event): void
    {
        ClearVacationRequestDaysInGoogleCalendar::dispatch($event->vacationRequest);
    }
}
