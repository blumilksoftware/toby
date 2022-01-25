<?php

declare(strict_types=1);

namespace Toby\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;
use Toby\Models\YearPeriod;

class YearPeriodExists implements Rule
{
    public function passes($attribute, $value): bool
    {
        $yearPeriod = YearPeriod::findByYear(Carbon::create($value)->year);

        return $yearPeriod !== null;
    }

    public function message(): string
    {
        return "The year period for given year doesn't exist.";
    }
}
