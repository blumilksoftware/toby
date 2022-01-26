<?php

declare(strict_types=1);

namespace Toby\Helpers\Rules;

use Closure;
use Toby\Models\VacationRequest;

interface VacationRequestRule
{
    public function check(VacationRequest $vacationRequest, Closure $next);
}
