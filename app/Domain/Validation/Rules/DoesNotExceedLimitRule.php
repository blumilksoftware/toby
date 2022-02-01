<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Closure;
use Toby\Eloquent\Models\VacationRequest;

class DoesNotExceedLimitRule implements VacationRequestRule
{
    public function check(VacationRequest $vacationRequest, Closure $next)
    {
        return $next($vacationRequest);
    }
}
