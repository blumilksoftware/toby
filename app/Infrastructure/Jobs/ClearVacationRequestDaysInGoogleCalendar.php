<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\GoogleCalendar\Event;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\VacationRequest;

class ClearVacationRequestDaysInGoogleCalendar implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
    ) {
    }

    public function handle(): void
    {
        $vacations = $this->vacationRequest->vacations()
            ->whereNotNull("event_id")
            ->get();

        /** @var Vacation $vacation */
        foreach ($vacations as $vacation) {
            Event::find($vacation->event_id)->delete();

            $vacation->update([
                "event_id" => null,
            ]);
        }
    }
}
