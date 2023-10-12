<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Toby\Eloquent\Models\YearPeriod;

class YearPeriodExists implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $yearPeriod = YearPeriod::findByYear(Carbon::create($value)->year);

        if ($yearPeriod === null) {
            $fail(__("The year period for given year does not exist."));
        }
    }
}
