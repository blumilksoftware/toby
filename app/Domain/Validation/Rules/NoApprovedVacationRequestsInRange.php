<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Domain\Enums\VacationRequestState;
use Toby\Eloquent\Models\VacationRequest;

class NoApprovedVacationRequestsInRange implements VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): bool
    {
        return !$vacationRequest
            ->user
            ->vacationRequests()
            ->overlapsWith($vacationRequest)
            ->states(VacationRequestState::successStates())
            ->exists();
    }

    public function errorMessage(): string
    {
        return __("You have approved vacation request in this range.");
    }
}
