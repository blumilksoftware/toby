<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Domain\WorkDaysCalculator;
use Toby\Eloquent\Models\VacationRequest;

class MinimumOneVacationDayRule implements VacationRequestRule
{
    public function __construct(
        protected WorkDaysCalculator $workDaysCalculator,
    ) {}

    public function check(VacationRequest $vacationRequest): bool
    {
        return $this->workDaysCalculator
            ->calculateDays($vacationRequest->from, $vacationRequest->to)
            ->isNotEmpty();
    }

    public function errorMessage(): string
    {
        return __("The request must be for at least one day.");
    }
}
