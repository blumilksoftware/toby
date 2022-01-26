<?php

declare(strict_types=1);

namespace Toby\Helpers;

use Illuminate\Contracts\Pipeline\Pipeline;
use Toby\Models\VacationRequest;

class VacationRequestValidator
{
    protected array $rules = [

    ];

    public function __construct(protected Pipeline $pipeline)
    {
    }

    public function validate(VacationRequest $vacationRequest): void
    {
        $this->pipeline
            ->send($vacationRequest)
            ->through($this->rules)
            ->via("check")
            ->then(fn(VacationRequest $vacationRequest) => $vacationRequest);
    }
}