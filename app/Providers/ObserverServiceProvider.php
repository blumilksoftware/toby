<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Support\ServiceProvider;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Models\YearPeriod;
use Toby\Observers\UserObserver;
use Toby\Observers\VacationRequestObserver;
use Toby\Observers\YearPeriodObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        YearPeriod::observe(YearPeriodObserver::class);
        VacationRequest::observe(VacationRequestObserver::class);
    }
}
