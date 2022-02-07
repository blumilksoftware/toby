<?php

declare(strict_types=1);

namespace Toby\Domain\Validation\Rules;

use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Models\VacationRequest;

class DoesNotExceedLimitRule implements VacationRequestRule
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
    ) {
    }

    public function check(VacationRequest $vacationRequest): bool
    {
        return true;
    }

    public function errorMessage(): string
    {
        return __("You have exceeded your vacation limit.");
    }
}
