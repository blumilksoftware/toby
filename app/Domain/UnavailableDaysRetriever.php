<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class UnavailableDaysRetriever
{
    public function __construct(
        protected UserVacationStatsRetriever $vacationStatsRetriever,
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getUnavailableDays(User $user, YearPeriod $yearPeriod, ?VacationType $vacationType = null): Collection
    {
        $unavailableDays = $user->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query->noStates(VacationRequestStatesRetriever::failedStates()),
            )
            ->pluck("date");

        if (!$vacationType || !$this->configRetriever->isDuringNonWorkDays($vacationType)) {
            $unavailableDays->push(...$yearPeriod->weekends());
            $unavailableDays->push(...$yearPeriod->holidays()->pluck("date"));
        }

        return $unavailableDays
            ->unique()
            ->sort()
            ->values();
    }
}
