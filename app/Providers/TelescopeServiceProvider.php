<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as BaseTelescopeServiceProvider;

class TelescopeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment("local")) {
            $this->app->register(BaseTelescopeServiceProvider::class);
            $this->app->register(TelescopeApplicationServiceProvider::class);
        }
    }
}
