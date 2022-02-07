<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Illuminate\Validation\ValidationException;
use Toby\Eloquent\Models\VacationRequest;

abstract class VacationRequestRule
{
    public function check(VacationRequest $vacationRequest): void
    {
        if (! $this->passes($vacationRequest)) {
            throw ValidationException::withMessages(["vacationRequest" => $this->errorMessage()]);
        }
    }

    public abstract function passes(VacationRequest $vacationRequest): bool;
    public abstract function errorMessage(): string;
}
