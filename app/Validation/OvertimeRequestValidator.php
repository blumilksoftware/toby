<?php

declare(strict_types=1);

namespace Toby\Validation;

use Illuminate\Contracts\Container\Container;
use Illuminate\Validation\ValidationException;
use Toby\Models\OvertimeRequest;
use Toby\Validation\Rules\OvertimeRequest\NoPendingOvertimeRequestInRange;
use Toby\Validation\Rules\OvertimeRequest\OvertimeRequestRule;

class OvertimeRequestValidator
{
    protected array $rules = [
        NoPendingOvertimeRequestInRange::class,
    ];

    public function __construct(
        protected Container $container,
    ) {}

    /**
     * @throws ValidationException
     */
    public function validate(OvertimeRequest $overtimeRequest): void
    {
        foreach ($this->rules as $rule) {
            $rule = $this->makeRule($rule);

            if (!$rule->check($overtimeRequest)) {
                throw ValidationException::withMessages([
                    "overtimeRequest" => $rule->errorMessage(),
                ]);
            }
        }
    }

    protected function makeRule(string $class): OvertimeRequestRule
    {
        return $this->container->make($class);
    }
}
