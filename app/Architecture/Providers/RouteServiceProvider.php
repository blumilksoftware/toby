<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            Route::prefix("api")
                ->middleware("api")
                ->group(base_path("routes/api.php"));

            Route::middleware("web")
                ->group(base_path("routes/web.php"));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for("api", fn(Request $request) => Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip()));
    }
}
