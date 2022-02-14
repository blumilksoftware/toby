<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationDaysCalculator;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class DoesNotExceedLimitRule implements VacationRequestRule
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
        protected VacationDaysCalculator $vacationDaysCalculator,
    ) {
    }

    public function check(VacationRequest $vacationRequest): bool
    {
        if (!$this->configRetriever->hasLimit($vacationRequest->type)) {
            return true;
        }

        $limit = $this->getUserVacationLimit($vacationRequest->user, $vacationRequest->yearPeriod);
        $vacationDays = $this->getVacationDaysWithLimit($vacationRequest->user, $vacationRequest->yearPeriod);
        $estimatedDays = $this->vacationDaysCalculator->calculateDays($vacationRequest->yearPeriod, $vacationRequest->from, $vacationRequest->to)->count();

        return $limit >= ($vacationDays + $estimatedDays);
    }

    public function errorMessage(): string
    {
        return __("Vacation limit has been exceeded.");
    }

    protected function getUserVacationLimit(User $user, YearPeriod $yearPeriod): int
    {
        return $user->vacationLimits()->where("year_period_id", $yearPeriod->id)->first()->days ?? 0;
    }

    protected function getVacationDaysWithLimit(User $user, YearPeriod $yearPeriod): int
    {
        return $user->vacations()
            ->where("year_period_id", $yearPeriod->id)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query
            ->whereIn("type", $this->getLimitableVacationTypes())
            ->noStates(VacationRequestState::failedStates()),
            )
            ->count();
    }

    protected function getLimitableVacationTypes(): Collection
    {
        $types = new Collection(VacationType::cases());

        return $types->filter(fn(VacationType $type) => $this->configRetriever->hasLimit($type));
    }
}
