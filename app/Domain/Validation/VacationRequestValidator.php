<?php

declare(strict_types=1);

namespace Toby\Domain\Validation;

use Illuminate\Pipeline\Pipeline;
use Toby\Domain\Validation\Rules\NoApprovedVacationRequestsInRange;
use Toby\Domain\Validation\Rules\DoesNotExceedLimitRule;
use Toby\Domain\Validation\Rules\MinimumOneVacationDayRule;
use Toby\Domain\Validation\Rules\NoPendingVacationRequestInRange;
use Toby\Domain\Validation\Rules\VacationRangeIsInTheSameYearRule;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestValidator
{
    protected array $rules = [
        VacationRangeIsInTheSameYearRule::class,
        MinimumOneVacationDayRule::class,
        DoesNotExceedLimitRule::class,
        NoPendingVacationRequestInRange::class,
        NoApprovedVacationRequestsInRange::class,
    ];

    public function __construct(
        protected Pipeline $pipeline,
    ) {
    }

    public function validate(VacationRequest $vacationRequest): void
    {
        foreach ($this->rules as $rule) {
            app($rule)->check($vacationRequest);
        }
    }
}
