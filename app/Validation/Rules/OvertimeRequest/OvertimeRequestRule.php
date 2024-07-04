<?php

declare(strict_types=1);

namespace Toby\Validation\Rules\OvertimeRequest;

use Toby\Models\OvertimeRequest;

interface OvertimeRequestRule
{
    public function check(OvertimeRequest $overtimeRequest): bool;

    public function errorMessage(): string;
}
