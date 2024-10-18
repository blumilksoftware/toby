<?php

declare(strict_types=1);

namespace Toby\Validation\Rules\VacationRequest;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Domain\WorkDaysCalculator;
use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\VacationRequest;

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

        $limit = $this->getUserVacationLimit($vacationRequest->user, $vacationRequest->from->year);
        $vacationDays = $this->getVacationDaysWithLimit($vacationRequest->user, $vacationRequest->from->year);
        $estimatedDays = $this->workDaysCalculator
            ->calculateDays($vacationRequest->from, $vacationRequest->to, $vacationRequest->type)
            ->count();

        return $limit >= ($vacationDays + $estimatedDays);
    }

    public function errorMessage(): string
    {
        return __("Vacation limit has been exceeded.");
    }

    protected function getUserVacationLimit(User $user, int $year): int
    {
        return $user->vacationLimits()
            ->where("year", $year)
            ->first()
            ?->limit ?? 0;
    }

    protected function getVacationDaysWithLimit(User $user, int $year): int
    {
        return $user->vacations()
            ->whereYear("date", $year)
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
