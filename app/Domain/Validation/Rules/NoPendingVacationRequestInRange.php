<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Domain\Enums\VacationRequestState;
use Toby\Eloquent\Models\VacationRequest;

class NoPendingVacationRequestInRange implements VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): bool
    {
        return !$vacationRequest
            ->user
            ->vacationRequests()
            ->overlapsWith($vacationRequest)
            ->states(VacationRequestState::pendingStates())
            ->exists();
    }

    public function errorMessage(): string
    {
        return __("You have pending vacation request in this range.");
    }
}
