<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class UnavailableDaysRetriever
{
    public function __construct(
        protected UserVacationStatsRetriever $vacationStatsRetriever,
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getUnavailableDays(User $user, YearPeriod $yearPeriod, ?VacationType $vacationType = null): array
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

        return $unavailableDays->map(fn(Carbon $date): string => $date->toDateString())->toArray();
    }
}
