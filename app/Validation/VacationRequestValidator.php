<?php

declare(strict_types=1);

namespace Toby\Validation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Validation\ValidationException;
use Toby\Models\VacationRequest;
use Toby\Validation\Rules\DoesNotExceedLimitRule;
use Toby\Validation\Rules\MinimumOneVacationDayRule;
use Toby\Validation\Rules\NoApprovedVacationRequestsInRange;
use Toby\Validation\Rules\NoPendingVacationRequestInRange;
use Toby\Validation\Rules\VacationRangeIsInTheSameYearRule;
use Toby\Validation\Rules\VacationRequestRule;
use Toby\Validation\Rules\VacationTypeCanBeSelected;

class VacationRequestValidator
{
    protected array $rules = [
        VacationRangeIsInTheSameYearRule::class,
        MinimumOneVacationDayRule::class,
        VacationTypeCanBeSelected::class,
        DoesNotExceedLimitRule::class,
        NoPendingVacationRequestInRange::class,
        NoApprovedVacationRequestsInRange::class,
    ];

    public function __construct(
        protected Container $container,
    ) {}

    /**
     * @throws ValidationException
     */
    public function validate(VacationRequest $vacationRequest): void
    {
        foreach ($this->rules as $rule) {
            $rule = $this->makeRule($rule);

            if (!$rule->check($vacationRequest)) {
                throw ValidationException::withMessages([
                    "vacationRequest" => $rule->errorMessage(),
                ]);
            }
        }
    }

    protected function makeRule(string $class): VacationRequestRule
    {
        return $this->container->make($class);
    }
}
