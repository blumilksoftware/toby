<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Support\ServiceProvider;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Models\VacationLimit;
use Toby\Models\VacationRequest;
use Toby\Observers\OvertimeRequestObserver;
use Toby\Observers\UserObserver;
use Toby\Observers\VacationLimitObserver;
use Toby\Observers\VacationRequestObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        VacationRequest::observe(VacationRequestObserver::class);
        VacationLimit::observe(VacationLimitObserver::class);
        OvertimeRequest::observe(OvertimeRequestObserver::class);
    }
}
