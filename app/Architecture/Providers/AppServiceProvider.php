<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Toby\Domain\Slack\SlackApiChannel;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Notification::resolved(function (ChannelManager $service): void {
            $service->extend("slack", fn(Application $app) => $app->make(SlackApiChannel::class));
        });
    }

    public function boot(): void
    {
        Carbon::macro("toDisplayString", fn() => $this->translatedFormat("d.m.Y"));
    }
}
