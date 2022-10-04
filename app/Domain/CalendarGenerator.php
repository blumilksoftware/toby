<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\YearPeriod;

class CalendarGenerator
{
    public function __construct(
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {}

    public function generate(Carbon $month): array
    {
        $period = CarbonPeriod::create($month->copy()->startOfMonth(), $month->copy()->endOfMonth());
        $yearPeriod = YearPeriod::findByYear($month->year);

        $holidays = $yearPeriod->holidays()->pluck("date");

        return $this->generateCalendar($period, $holidays);
    }

    protected function generateCalendar(CarbonPeriod $period, Collection $holidays): array
    {
        $calendar = [];
        $vacations = $this->getVacationsForPeriod($period);
        $pendingVacations = $this->getPendingVacationsForPeriod($period);

        foreach ($period as $day) {
            $vacationsForDay = $vacations[$day->toDateString()] ?? new Collection();
            $pendingVacationsForDay = $pendingVacations[$day->toDateString()] ?? new Collection();

            $calendar[] = [
                "date" => $day->toDateString(),
                "dayOfMonth" => $day->translatedFormat("j"),
                "dayOfWeek" => $day->translatedFormat("D"),
                "isToday" => $day->isToday(),
                "isWeekend" => $day->isWeekend(),
                "isHoliday" => $holidays->contains($day),
                "vacations" => $vacationsForDay->pluck("user_id"),
                "pendingVacations" => $pendingVacationsForDay->pluck("user_id"),
                "vacationTypes" => $vacationsForDay->pluck("vacationRequest.type", "user_id"),
                "vacationPendingTypes" => $pendingVacationsForDay->pluck("vacationRequest.type", "user_id"),
            ];
        }

        return $calendar;
    }

    protected function getVacationsForPeriod(CarbonPeriod $period): Collection
    {
        return Vacation::query()
            ->whereBetween("date", [$period->start, $period->end])
            ->approved()
            ->with("vacationRequest")
            ->get()
            ->groupBy(fn(Vacation $vacation): string => $vacation->date->toDateString());
    }

    protected function getPendingVacationsForPeriod(CarbonPeriod $period): Collection
    {
        return Vacation::query()
            ->whereBetween("date", [$period->start, $period->end])
            ->pending()
            ->with("vacationRequest")
            ->get()
            ->groupBy(fn(Vacation $vacation): string => $vacation->date->toDateString());
    }
}
