<?php

declare(strict_types=1);

namespace Toby\Observers;

use Laragear\CacheQuery\Facades\CacheQuery;
use Toby\Models\OvertimeRequest;

class OvertimeRequestObserver
{
    public function creating(OvertimeRequest $overtime): void
    {
        $count = $overtime->yearPeriod->overtimeRequests()->count();
        $number = $count + 1;

        $overtime->name = "N/{$number}/{$overtime->yearPeriod->year}";
    }

    public function updating(OvertimeRequest $overtime): void
    {
        CacheQuery::forget("overtime:{$overtime->user->id}");
    }
}
