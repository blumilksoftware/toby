<?php

declare(strict_types=1);

namespace Toby\Slack\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Toby\Domain\Notifications\Notifiable;

class SlackApiChannel
{
    public function send(Notifiable $notifiable, Notification $notification): void
    {
        if (!config("services.slack.enabled")) {
            return;
        }

        $baseUrl = $this->getBaseUrl();
        $url = "{$baseUrl}/chat.postMessage";
        $channel = $notifiable->routeNotificationFor("slack", $notification);

        $message = $notification->toSlack($notifiable);

        Http::withToken($this->getClientToken())
            ->post($url, array_merge($message->getPayload(), [
                "channel" => $channel,
            ]));
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
