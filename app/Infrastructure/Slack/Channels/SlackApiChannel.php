<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Slack\Channels;

use Illuminate\Http\Client\Response;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Toby\Domain\Notifications\Notifiable;

class SlackApiChannel
{
    public function send(Notifiable $notifiable, Notification $notification): Response
    {
        $baseUrl = $this->getBaseUrl();
        $url = "{$baseUrl}/chat.postMessage";
        $channel = $notifiable->routeNotificationFor("slack", $notification);

        return Http::withToken($this->getClientToken())
            ->post($url, [
                "channel" => $channel,
                "text" => $notification->toSlack($notifiable),
            ]);
    }

    protected function getClientToken(): string
    {
        return config("services.slack.client_token");
    }

    protected function getBaseUrl(): string
    {
        return config("services.slack.url");
    }
}
