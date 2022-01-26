<?php

declare(strict_types=1);

namespace Toby\Helpers;

use Illuminate\Pipeline\Pipeline;
use Toby\Helpers\Rules\ApprovedVacationDaysInSameRange;
use Toby\Helpers\Rules\DoesNotExceedLimitRule;
use Toby\Helpers\Rules\MinimumOneVacationDayRule;
use Toby\Helpers\Rules\PendingVacationRequestInSameRange;
use Toby\Models\VacationRequest;

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
