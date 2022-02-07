<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Domain\VacationDaysCalculator;
use Toby\Eloquent\Models\VacationRequest;

class MinimumOneVacationDayRule extends VacationRequestRule
{
    public function __construct(protected VacationDaysCalculator $vacationDaysCalculator)
    {
    }

    public function passes(VacationRequest $vacationRequest): bool
    {
        return $this->vacationDaysCalculator
            ->calculateDays($vacationRequest->yearPeriod, $vacationRequest->from, $vacationRequest->to)
            ->isNotEmpty();
    }

    public function errorMessage(): string
    {
        return __("Vacation needs minimum one day.");
    }
}
