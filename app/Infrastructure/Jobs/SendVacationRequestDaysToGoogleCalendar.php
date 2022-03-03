<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\GoogleCalendar\Event;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\VacationRequest;

class SendVacationRequestDaysToGoogleCalendar implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function __construct(
        protected VacationRequest $vacationRequest,
    ) {}

    public function handle(): void
    {
        $vacations = $this->vacationRequest->vacations()
            ->whereNull("event_id")
            ->get();

        /** @var Vacation $vacation */
        foreach ($vacations as $vacation) {
            $event = Event::create([
                "name" => "{$this->vacationRequest->type->label()} - {$this->vacationRequest->user->fullName}",
                "startDate" => $vacation->date,
                "endDate" => $vacation->date,
            ]);

            $vacation->update([
                "event_id" => $event->id,
            ]);
        }
    }
}
