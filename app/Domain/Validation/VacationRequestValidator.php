<?php

declare(strict_types=1);

namespace Toby\Domain\Validation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Validation\ValidationException;
use Toby\Domain\Validation\Rules\DoesNotExceedLimitRule;
use Toby\Domain\Validation\Rules\MinimumOneVacationDayRule;
use Toby\Domain\Validation\Rules\NoApprovedVacationRequestsInRange;
use Toby\Domain\Validation\Rules\NoPendingVacationRequestInRange;
use Toby\Domain\Validation\Rules\VacationRangeIsInTheSameYearRule;
use Toby\Domain\Validation\Rules\VacationRequestRule;
use Toby\Domain\Validation\Rules\VacationTypeCanBeSelected;
use Toby\Eloquent\Models\VacationRequest;

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
