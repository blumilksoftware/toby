<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\YearPeriod;

class UserVacationStatsRetriever
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getUsedVacationDays(User $user, YearPeriod $yearPeriod): int
    {
        return $user
            ->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestStatesRetriever::successStates()),
            )
            ->count();
    }

    public function getUsedVacationDaysByMonth(User $user, YearPeriod $yearPeriod): Collection
    {
        return $user->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestStatesRetriever::successStates()),
            )
            ->get()
            ->groupBy(fn(Vacation $vacation): string => strtolower($vacation->date->englishMonth))
            ->map(fn(Collection $items): int => $items->count());
    }

    public function getPendingVacationDays(User $user, YearPeriod $yearPeriod): int
    {
        return $user
            ->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestStatesRetriever::pendingStates()),
            )
            ->count();
    }

    public function getOtherApprovedVacationDays(User $user, YearPeriod $yearPeriod): int
    {
        return $user
            ->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getNotLimitableVacationTypes())
                    ->whereNot("type", VacationType::RemoteWork)
                    ->states(VacationRequestStatesRetriever::successStates()),
            )
            ->count();
    }

    public function getRemoteWorkDays(User $user, YearPeriod $yearPeriod): int
    {
        return $user
            ->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->where("type", VacationType::RemoteWork)
                    ->states(VacationRequestStatesRetriever::successStates()),
            )
            ->count();
    }

    public function getRemainingVacationDays(User $user, YearPeriod $yearPeriod): int
    {
        $limit = $this->getVacationDaysLimit($user, $yearPeriod);
        $used = $this->getUsedVacationDays($user, $yearPeriod);
        $pending = $this->getPendingVacationDays($user, $yearPeriod);

        return $limit - $used - $pending;
    }

    public function getVacationDaysLimit(User $user, YearPeriod $yearPeriod): int
    {
        return $user->vacationLimits()
            ->whereBelongsTo($yearPeriod)
            ->first()?->limit ?? 0;
    }

    public function hasVacationDaysLimit(User $user, YearPeriod $yearPeriod): bool
    {
        return $user->vacationLimits()
            ->whereBelongsTo($yearPeriod)
            ->first()?->hasVacation() ?? false;
    }

    protected function getLimitableVacationTypes(): Collection
    {
        $types = VacationType::all();

        return $types->filter(fn(VacationType $type): bool => $this->configRetriever->hasLimit($type));
    }

    protected function getNotLimitableVacationTypes(): Collection
    {
        $types = VacationType::all();

        return $types->filter(fn(VacationType $type): bool => !$this->configRetriever->hasLimit($type));
    }
}
