<?php

declare(strict_types=1);

namespace Toby\Domain;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\YearPeriod;

class CalendarGenerator
{
    public function __construct(
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {
    }

    public function generate(YearPeriod $yearPeriod, string $month): array
    {
        $date = CarbonImmutable::create($yearPeriod->year, $this->monthNameToNumber($month));
        $period = CarbonPeriod::create($date->startOfMonth(), $date->endOfMonth());
        $holidays = $yearPeriod->holidays()->pluck("date");

        return $this->generateCalendar($period, $holidays);
    }

    protected function monthNameToNumber($name): int
    {
        return match ($name) {
            default => CarbonInterface::JANUARY,
            "february" => CarbonInterface::FEBRUARY,
            "march" => CarbonInterface::MARCH,
            "april" => CarbonInterface::APRIL,
            "may" => CarbonInterface::MAY,
            "june" => CarbonInterface::JUNE,
            "july" => CarbonInterface::JULY,
            "august" => CarbonInterface::AUGUST,
            "september" => CarbonInterface::SEPTEMBER,
            "october" => CarbonInterface::OCTOBER,
            "november" => CarbonInterface::NOVEMBER,
            "december" => CarbonInterface::DECEMBER,
        };
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
                "vacations" => $vacationsForDay->pluck("user_id"),
            ];
        }

        return $calendar;
    }

    protected function getVacationsForPeriod(CarbonPeriod $period): Collection
    {
        return Vacation::query()
            ->whereBetween("date", [$period->start, $period->end])
            ->whereRelation("vacationRequest", fn(Builder $query) => $query->states(VacationRequestStatesRetriever::successStates()))
            ->get()
            ->groupBy(fn(Vacation $vacation) => $vacation->date->toDateString());
    }
}
