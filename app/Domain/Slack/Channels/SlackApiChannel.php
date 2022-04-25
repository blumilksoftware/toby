<?php

declare(strict_types=1);

namespace Toby\Domain\Slack\Channels;

use Illuminate\Http\Client\Response;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SlackApiChannel
{
    public function send($notifiable, Notification $notification): Response
    {
        $baseUrl = config("services.slack.url");
        $url = "{$baseUrl}/chat.postMessage";
        $channel = $notifiable->routeNotificationFor("slack", $notification);

        return Http::withToken(config("services.slack.client_token"))
            ->post($url, [
                "channel" => $channel,
                "text" => $notification->toSlack($notifiable),
            ]);
    }
}
