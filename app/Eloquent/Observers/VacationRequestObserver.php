<?php

declare(strict_types=1);

namespace Toby\Eloquent\Observers;

use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Events\Dispatcher;
use Toby\Eloquent\Models\VacationRequest;

class VacationRequestObserver
{
    public function __construct(
        protected Auth $auth,
        protected Dispatcher $dispatcher,
    ) {
    }

    public function creating(VacationRequest $vacationRequest): void
    {
        $year = $vacationRequest->from->year;

        $vacationRequestNumber = $vacationRequest->user->vacationRequests()
            ->whereYear("from", $year)
            ->count() + 1;

        $vacationRequest->name = "{$vacationRequestNumber}/${year}";
    }
}
