<?php

declare(strict_types=1);

namespace Toby\Domain\Listeners;

use Toby\Domain\Events\VacationRequestCanceled;
use Toby\Infrastructure\Jobs\ClearVacationRequestDaysInGoogleCalendar;

class HandleCanceledVacationRequest
{
    public function handle(VacationRequestCanceled $event): void
    {
        ClearVacationRequestDaysInGoogleCalendar::dispatch($event->vacationRequest);
    }
}
