<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Support\ServiceProvider;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Eloquent\Observers\UserObserver;
use Toby\Eloquent\Observers\VacationRequestObserver;
use Toby\Eloquent\Observers\YearPeriodObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        YearPeriod::observe(YearPeriodObserver::class);
        VacationRequest::observe(VacationRequestObserver::class);
    }
}
