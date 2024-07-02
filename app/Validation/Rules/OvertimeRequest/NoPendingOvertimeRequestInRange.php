<?php

declare(strict_types=1);

namespace Toby\Validation\Rules\OvertimeRequest;

use Toby\Domain\OvertimeRequestStatesRetriever;
use Toby\Models\OvertimeRequest;

class NoPendingOvertimeRequestInRange implements OvertimeRequestRule
{
    public function check(OvertimeRequest $overtimeRequest): bool
    {
        return !$overtimeRequest
            ->user
            ->overtimeRequests()
            ->overlapsWith($overtimeRequest)
            ->states(OvertimeRequestStatesRetriever::pendingStates())
            ->exists();
    }

    public function errorMessage(): string
    {
        return __("You have a pending request in this date range.");
    }
}
