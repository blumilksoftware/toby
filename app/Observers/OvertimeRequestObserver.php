<?php

declare(strict_types=1);

namespace Toby\Observers;

use Toby\Models\OvertimeRequest;

class OvertimeRequestObserver
{
    public function creating(OvertimeRequest $overtime): void
    {
        $count = $overtime->yearPeriod->overtimeRequests()->count();
        $number = $count + 1;

        $overtime->name = "{$number}/{$overtime->yearPeriod->year}";
    }
}
