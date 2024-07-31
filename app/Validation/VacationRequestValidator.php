<?php

declare(strict_types=1);

namespace Toby\Validation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Validation\ValidationException;
use Toby\Models\VacationRequest;
use Toby\Validation\Rules\VacationRequest\DoesNotExceedLimitRule;
use Toby\Validation\Rules\VacationRequest\MinimumOneVacationDayRule;
use Toby\Validation\Rules\VacationRequest\NoApprovedVacationRequestsInRange;
use Toby\Validation\Rules\VacationRequest\NoPendingVacationRequestInRange;
use Toby\Validation\Rules\VacationRequest\VacationRangeIsInTheSameYearRule;
use Toby\Validation\Rules\VacationRequest\VacationRequestRule;
use Toby\Validation\Rules\VacationRequest\VacationTypeCanBeSelected;

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
