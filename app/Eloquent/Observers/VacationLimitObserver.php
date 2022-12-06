<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Toby\Eloquent\Models\VacationLimit;

class VacationLimitObserver
{
    public function saving(VacationLimit $vacationLimit): void
    {
        $vacationLimit->limit = $vacationLimit->from_previous_year + $vacationLimit->days - $vacationLimit->to_next_year;
    }
}
