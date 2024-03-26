<?php

declare(strict_types=1);

namespace Toby\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Toby\Slack\Channels\SlackApiChannel;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Notification::resolved(function (ChannelManager $service): void {
            $service->extend("slack", fn(Application $app): SlackApiChannel => $app->make(SlackApiChannel::class));
        });
    }

    public function boot(): void
    {
        Model::preventLazyLoading(!app()->isProduction());
        Carbon::macro("toDisplayString", fn(): string => $this->translatedFormat("d.m.Y"));
        Carbon::macro("toDisplayDateTimeString", fn(): string => $this->translatedFormat("d.m.Y H:i:s"));

        RateLimiter::for(
            "api",
            fn(Request $request): Limit => Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip()),
        );
    }
}
