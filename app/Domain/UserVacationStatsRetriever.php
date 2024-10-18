<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\Vacation;

class UserVacationStatsRetriever
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {}

    public function getUsedVacationDays(User $user, ?int $year = null): int
    {
        return $user
            ->vacations()
            ->whereYear("date", $year)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestStatesRetriever::successStates()),
            )
            ->count();
    }

    public function getUsedVacationDaysByMonth(User $user, ?int $year = null): Collection
    {
        return $user->vacations()
            ->whereYear("date", $year)
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

    public function getPendingVacationDays(User $user, ?int $year = null): int
    {
        return $user
            ->vacations()
            ->whereYear("date", $year)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestStatesRetriever::pendingStates()),
            )
            ->count();
    }

    public function getOtherApprovedVacationDays(User $user, ?int $year = null): int
    {
        return $user
            ->vacations()
            ->whereYear("date", $year)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getNotLimitableVacationTypes())
                    ->whereNot("type", VacationType::RemoteWork)
                    ->whereNot("type", VacationType::Delegation)
                    ->states(VacationRequestStatesRetriever::successStates()),
            )
            ->count();
    }

    public function getRemoteWorkDays(User $user, ?int $year = null): int
    {
        return $user
            ->vacations()
            ->whereYear("date", $year)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->where("type", VacationType::RemoteWork)
                    ->states(VacationRequestStatesRetriever::successStates()),
            )
            ->count();
    }

    public function getRemainingVacationDays(User $user, ?int $year = null): int
    {
        $limit = $this->getVacationDaysLimit($user, $year);
        $used = $this->getUsedVacationDays($user, $year);
        $pending = $this->getPendingVacationDays($user, $year);

        return $limit - $used - $pending;
    }

    public function getVacationDaysLimit(User $user, ?int $year = null): int
    {
        return $user->vacationLimits()
            ->where("year", $year ?? Carbon::today()->year)
            ->cache("vacations:{$user->id}")
            ->first()?->limit ?? 0;
    }

    public function hasVacationDaysLimit(User $user, ?int $year = null): bool
    {
        return $user->vacationLimits()
            ->where("year", $year ?? Carbon::today()->year)
            ->cache("vacations:{$user->id}")
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
