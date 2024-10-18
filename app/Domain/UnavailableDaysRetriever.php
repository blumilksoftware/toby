<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Enums\VacationType;
use Toby\Models\Holiday;
use Toby\Models\User;

class UnavailableDaysRetriever
{
    public function __construct(
        protected UserVacationStatsRetriever $vacationStatsRetriever,
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getUnavailableDays(User $user, ?int $year = null, ?VacationType $vacationType = null): Collection
    {
        $year ??= Carbon::now()->year;

        $unavailableDays = $user->vacations()
            ->whereYear("date", $year)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query->noStates(VacationRequestStatesRetriever::failedStates()),
            )
            ->pluck("date");

        if (!$vacationType || !$this->configRetriever->isDuringNonWorkDays($vacationType)) {
            $unavailableDays->push(...$this->getWeekends($year));
            $unavailableDays->push(...$this->getHolidays($year));
        }

        return $unavailableDays
            ->unique()
            ->sort()
            ->values();
    }

    protected function getWeekends(int $year): Collection
    {
        $start = Carbon::create($year);
        $end = Carbon::create($year)->endOfYear();

        $weekends = new Collection();

        while ($start->lessThanOrEqualTo($end)) {
            if ($start->isWeekend()) {
                $weekends->push($start->copy());
            }

            $start->addDay();
        }

        return $weekends;
    }

    protected function getHolidays(int $year): Collection
    {
        return Holiday::query()
            ->whereYear("date", $year)
            ->pluck("date");
    }
}
