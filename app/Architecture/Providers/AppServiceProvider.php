<?php

declare(strict_types=1);

namespace Toby\Architecture\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Toby\Infrastructure\Slack\Channels\SlackApiChannel;

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
        Carbon::macro("toDisplayString", fn(): string => $this->translatedFormat("d.m.Y"));
        Carbon::macro("toDisplayDateTimeString", fn(): string => $this->translatedFormat("d.m.Y H:i:s"));
    }
}
