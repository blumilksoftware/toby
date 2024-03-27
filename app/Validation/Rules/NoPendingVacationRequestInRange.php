<?php

declare(strict_types=1);

namespace Toby\Validation\Rules;

use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Models\VacationRequest;

class NoPendingVacationRequestInRange implements VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): bool
    {
        return !$vacationRequest
            ->user
            ->vacationRequests()
            ->overlapsWith($vacationRequest)
            ->states(VacationRequestStatesRetriever::pendingStates())
            ->exists();
    }

    public function errorMessage(): string
    {
        return __("You have a pending request in this date range.");
    }
}
