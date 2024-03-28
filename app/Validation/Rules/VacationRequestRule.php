<?php

declare(strict_types=1);

namespace Toby\Validation\Rules;

use Toby\Models\VacationRequest;

interface VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): bool;

    public function errorMessage(): string;
}
