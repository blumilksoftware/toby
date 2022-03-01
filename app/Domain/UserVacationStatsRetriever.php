<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Eloquent\Models\User;
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
            ->where("year_period_id", $yearPeriod->id)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestState::successStates()),
            )
            ->count();
    }

    public function getPendingVacationDays(User $user, YearPeriod $yearPeriod): int
    {
        return $user
            ->vacations()
            ->where("year_period_id", $yearPeriod->id)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestState::pendingStates()),
            )
            ->count();
    }

    public function getOtherApprovedVacationDays(User $user, YearPeriod $yearPeriod): int
    {
        return $user
            ->vacations()
            ->where("year_period_id", $yearPeriod->id)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query
                    ->whereIn("type", $this->getNotLimitableVacationTypes())
                    ->states(VacationRequestState::successStates()),
            )
            ->count();
    }

    public function getRemainingVacationDays(User $user, YearPeriod $yearPeriod): int
    {
        return $this->getVacationDaysLimit($user, $yearPeriod) - $this->getUsedVacationDays($user, $yearPeriod);
    }

    public function getVacationDaysLimit(User $user, YearPeriod $yearPeriod): int
    {
        $limit = $user->vacationLimits()
            ->where("year_period_id", $yearPeriod->id)
            ->first()
            ->days;

        return $limit ?? 0;
    }

    protected function getLimitableVacationTypes(): Collection
    {
        $types = new Collection(VacationType::cases());

        return $types->filter(fn(VacationType $type) => $this->configRetriever->hasLimit($type));
    }

    protected function getNotLimitableVacationTypes(): Collection
    {
        $types = new Collection(VacationType::cases());

        return $types->filter(fn(VacationType $type) => !$this->configRetriever->hasLimit($type));
    }
}
