<?php

declare(strict_types=1);

namespace Toby\Helpers\Rules;

use Closure;
use Toby\Models\VacationRequest;

class PendingVacationRequestInSameRange
{
    public function check(VacationRequest $vacationRequest, Closure $next)
    {
        return $next($vacationRequest);
    }
}