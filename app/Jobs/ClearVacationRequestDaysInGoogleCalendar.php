<?php

declare(strict_types=1);

namespace Toby\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\GoogleCalendar\Event;
use Toby\Models\VacationRequest;

class ClearVacationRequestDaysInGoogleCalendar implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
    ) {}

    public function handle(): void
    {
        if (!config("services.google.calendar_enabled")) {
            return;
        }

        foreach ($this->vacationRequest->event_ids ?? [] as $eventId) {
            $calendarEvent = Event::find($eventId);

            if ($calendarEvent->googleEvent->getStatus() !== "cancelled") {
                $calendarEvent->delete();
            }
        }

        $this->vacationRequest->update([
            "event_ids" => null,
        ]);
    }
}
