<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\GoogleCalendar\Event;
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
        $days = $this->vacationRequest
            ->vacations()
            ->orderBy("date")
            ->pluck("date");

        $this->vacationRequest->event_ids = new Collection();
        $ranges = $this->prepareRanges($days);

        foreach ($ranges as $range) {
            $text = "{$this->vacationRequest->type->label()} - {$this->vacationRequest->user->profile->fullName} [{$this->vacationRequest->name}]";

            $event = Event::create([
                "name" => $text,
                "startDate" => Carbon::create($range["from"]),
                "endDate" => Carbon::create($range["to"])->addDay(),
            ]);

            $this->vacationRequest->event_ids->add($event->id);
        }

        $this->vacationRequest->save();
    }

    protected function prepareRanges(Collection $days): array
    {
        $ranges = [];
        $index = 0;
        $first = $days->shift();

        $ranges[$index] = [
            "from" => $first,
            "to" => $first,
        ];

        foreach ($days as $day) {
            if ($day->diffInDays($ranges[$index]["to"]) > 1) {
                $index++;

                $ranges[$index] = [
                    "from" => $day,
                    "to" => $day,
                ];

                continue;
            }

            if ($day->isAfter($ranges[$index]["to"])) {
                $ranges[$index]["to"] = $day;
            }
        }

        return $ranges;
    }
}
