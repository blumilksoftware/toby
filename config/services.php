<?php

declare(strict_types=1);

return [
    "google" => [
        "calendar_enabled" => env("GOOGLE_CALENDAR_ENABLED", true),
        "client_id" => env("GOOGLE_CLIENT_ID"),
        "client_secret" => env("GOOGLE_CLIENT_SECRET"),
        "redirect" => env("GOOGLE_REDIRECT"),
    ],
    "slack" => [
        "enabled" => env("SLACK_ENABLED", true),
        "url" => "https://slack.com/api",
        "client_token" => env("SLACK_CLIENT_TOKEN"),
        "default_channel" => env("SLACK_DEFAULT_CHANNEL"),
    ],
];
