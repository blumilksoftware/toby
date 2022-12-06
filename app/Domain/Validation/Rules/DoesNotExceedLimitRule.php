<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Domain\WorkDaysCalculator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class DoesNotExceedLimitRule implements VacationRequestRule
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
        protected WorkDaysCalculator $workDaysCalculator,
    ) {}

    public function check(VacationRequest $vacationRequest): bool
    {
        if (!$this->configRetriever->hasLimit($vacationRequest->type)) {
            return true;
        }

        $limit = $this->getUserVacationLimit($vacationRequest->user, $vacationRequest->yearPeriod);
        $vacationDays = $this->getVacationDaysWithLimit($vacationRequest->user, $vacationRequest->yearPeriod);
        $estimatedDays = $this->workDaysCalculator
            ->calculateDays($vacationRequest->from, $vacationRequest->to)
            ->count();

        return $limit >= ($vacationDays + $estimatedDays);
    }

    public function errorMessage(): string
    {
        return __("Vacation limit has been exceeded.");
    }

    protected function getUserVacationLimit(User $user, YearPeriod $yearPeriod): int
    {
        return $user->vacationLimits()
            ->whereBelongsTo($yearPeriod)
            ->first()
            ?->limit ?? 0;
    }

    protected function getVacationDaysWithLimit(User $user, YearPeriod $yearPeriod): int
    {
        return $user->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->noStates(VacationRequestStatesRetriever::failedStates()),
            )
            ->count();
    }

    protected function getLimitableVacationTypes(): Collection
    {
        $types = VacationType::all();

        return $types->filter(fn(VacationType $type): bool => $this->configRetriever->hasLimit($type));
    }
}
