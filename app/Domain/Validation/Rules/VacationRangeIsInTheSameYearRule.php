<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Eloquent\Models\VacationRequest;

class VacationRangeIsInTheSameYearRule extends VacationRequestRule
{
    public function passes(VacationRequest $vacationRequest): bool
    {
        return $vacationRequest->from->isSameYear($vacationRequest->to);
    }

    public function errorMessage(): string
    {
        return __("The vacation request cannot be created at the turn of the year.");
    }
}
