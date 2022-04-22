<?php

declare(strict_types=1);

return [
    "google" => [
        "client_id" => env("GOOGLE_CLIENT_ID"),
        "client_secret" => env("GOOGLE_CLIENT_SECRET"),
        "redirect" => env("GOOGLE_REDIRECT"),
    ],
    "slack" => [
        "url" => "https://slack.com/api",
        "client_token" => env("SLACK_CLIENT_TOKEN"),
        "default_channel" => env("SLACK_DEFAULT_CHANNEL"),
    ],
];
