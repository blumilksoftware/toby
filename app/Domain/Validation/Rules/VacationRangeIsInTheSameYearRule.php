<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Eloquent\Models\VacationRequest;

class VacationRangeIsInTheSameYearRule implements VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): bool
    {
        return $vacationRequest->from->isSameYear($vacationRequest->to);
    }

    public function errorMessage(): string
    {
        return __("The request cannot be created at the turn of the year.");
    }
}
