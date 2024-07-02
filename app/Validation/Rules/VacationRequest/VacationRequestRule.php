<?php

declare(strict_types=1);

namespace Toby\Validation\Rules\VacationRequest;

use Toby\Models\VacationRequest;

interface VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): bool;

    public function errorMessage(): string;
}
