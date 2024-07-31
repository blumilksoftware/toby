<?php

declare(strict_types=1);

namespace Toby\Observers;

use Laragear\CacheQuery\Facades\CacheQuery;
use Toby\Models\VacationRequest;

class VacationRequestObserver
{
    public function creating(VacationRequest $vacationRequest): void
    {
        $count = $vacationRequest->yearPeriod->vacationRequests()->count();
        $number = $count + 1;

        $vacationRequest->name = "{$number}/{$vacationRequest->yearPeriod->year}";
    }

    public function updating(VacationRequest $vacationRequest): void
    {
        CacheQuery::forget("vacations:{$vacationRequest->user->id}");
    }
}
