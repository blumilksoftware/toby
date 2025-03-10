<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Http\Resources\SimpleVacationRequestResource;
use Toby\Models\Holiday;
use Toby\Models\Vacation;

class CalendarGenerator
{
    public function generate(Carbon $month): array
    {
        $period = CarbonPeriod::create($month->copy()->startOfMonth(), $month->copy()->endOfMonth());

        $holidays = Holiday::query()
            ->whereYear("date", $month->year)
            ->pluck("date");

        return $this->generateCalendar($period, $holidays);
    }

    protected function generateCalendar(CarbonPeriod $period, Collection $holidays): array
    {
        $calendar = [];
        $vacations = $this->getVacationsForPeriod($period);

        foreach ($period as $day) {
            $vacationsForDay = $vacations[$day->toDateString()] ?? new Collection();

            $calendar[] = [
                "date" => $day->toDateString(),
                "dayOfMonth" => $day->translatedFormat("j"),
                "dayOfWeek" => $day->translatedFormat("D"),
                "isToday" => $day->isToday(),
                "isWeekend" => $day->isWeekend(),
                "isHoliday" => $holidays->contains($day),
                "vacations" => $vacationsForDay
                    ->mapWithKeys(fn(Vacation $vacation): array => [$vacation->user_id => new SimpleVacationRequestResource($vacation->vacationRequest)]),
            ];
        }

        return $calendar;
    }

    protected function getVacationsForPeriod(CarbonPeriod $period): Collection
    {
        return Vacation::query()
            ->whereBetween("date", [$period->start, $period->end])
            ->approved()
            ->orWhere(function ($query): void {
                $query->pending();
            })
            ->with("vacationRequest.vacations")
            ->get()
            ->groupBy(fn(Vacation $vacation): string => $vacation->date->toDateString());
    }
}
