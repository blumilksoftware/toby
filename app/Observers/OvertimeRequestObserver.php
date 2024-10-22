<?php

declare(strict_types=1);

namespace Toby\Observers;

use Laragear\CacheQuery\Facades\CacheQuery;
use Toby\Models\OvertimeRequest;

class OvertimeRequestObserver
{
    public function creating(OvertimeRequest $overtime): void
    {
        $count = OvertimeRequest::query()
            ->whereYear("from", $overtime->from)
            ->count();

        $number = $count + 1;

        $overtime->name = "N/{$number}/{$overtime->from->year}";
    }

    public function updating(OvertimeRequest $overtime): void
    {
        CacheQuery::forget("overtime:{$overtime->user->id}");
    }
}
