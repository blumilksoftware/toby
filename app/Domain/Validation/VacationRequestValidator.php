<?php

declare(strict_types=1);

namespace Toby\Domain\Validation;

use Illuminate\Pipeline\Pipeline;
use Toby\Domain\Validation\Rules\ApprovedVacationDaysInSameRange;
use Toby\Domain\Validation\Rules\DoesNotExceedLimitRule;
use Toby\Domain\Validation\Rules\MinimumOneVacationDayRule;
use Toby\Domain\Validation\Rules\PendingVacationRequestInSameRange;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestValidator
{
    protected array $rules = [
        MinimumOneVacationDayRule::class,
        DoesNotExceedLimitRule::class,
        PendingVacationRequestInSameRange::class,
        ApprovedVacationDaysInSameRange::class,
    ];

    public function __construct(
        protected Pipeline $pipeline,
    ) {
    }

    public function validate(VacationRequest $vacationRequest): void
    {
        $this->pipeline
            ->send($vacationRequest)
            ->through($this->rules)
            ->via("check");
    }
}
