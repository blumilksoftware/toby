<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Toby\Eloquent\Models\VacationRequest;

class VacationRequestObserver
{
    public function creating(VacationRequest $vacationRequest): void
    {
        $count = $vacationRequest->yearPeriod->vacationRequests()->count();
        $number = $count + 1;

        $vacationRequest->name = "{$number}/{$vacationRequest->yearPeriod->year}";
    }
}
