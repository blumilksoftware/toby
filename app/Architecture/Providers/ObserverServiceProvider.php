<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Support\ServiceProvider;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Observers\UserObserver;
use Toby\Eloquent\Observers\VacationLimitObserver;
use Toby\Eloquent\Observers\VacationRequestObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
        VacationRequest::observe(VacationRequestObserver::class);
        VacationLimit::observe(VacationLimitObserver::class);
    }
}
