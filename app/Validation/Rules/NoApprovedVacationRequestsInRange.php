<?php

declare(strict_types=1);

namespace Toby\Validation\Rules;

use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Models\VacationRequest;

class NoApprovedVacationRequestsInRange implements VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): bool
    {
        return !$vacationRequest
            ->user
            ->vacationRequests()
            ->overlapsWith($vacationRequest)
            ->states(VacationRequestStatesRetriever::successStates())
            ->exists();
    }

    public function errorMessage(): string
    {
        return __("You have an approved request in this date range.");
    }
}
