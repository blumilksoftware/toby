<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Toby\Models\User;
use Toby\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::observe(UserObserver::class);

        Carbon::macro("toDisplayString", fn() => $this->translatedFormat("j F Y"));
    }
}
