<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Support\ServiceProvider;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Observers\UserObserver;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }
}
