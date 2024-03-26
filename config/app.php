<?php

declare(strict_types=1);

return [
    "name" => env("APP_NAME", "Toby HR application"),
    "env" => env("APP_ENV", "production"),
    "debug" => (bool)env("APP_DEBUG", false),
    "url" => env("APP_URL", "https://toby.blumilk.localhost"),
    "timezone" => "Europe/Warsaw",
    "locale" => "pl",
    "fallback_locale" => "en",
    "faker_locale" => "pl_PL",
    "cipher" => "AES-256-CBC",
    "key" => env("APP_KEY"),
    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
];
