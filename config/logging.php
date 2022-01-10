<?php

declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;

return [
    "default" => env("LOG_CHANNEL", "stack"),
    "deprecations" => env("LOG_DEPRECATIONS_CHANNEL", "null"),

    "channels" => [
        "stack" => [
            "driver" => "stack",
            "channels" => ["single"],
            "ignore_exceptions" => false,
        ],
        "single" => [
            "driver" => "single",
            "path" => storage_path("logs/laravel.log"),
            "level" => env("LOG_LEVEL", "debug"),
        ],
        "daily" => [
            "driver" => "daily",
            "path" => storage_path("logs/laravel.log"),
            "level" => env("LOG_LEVEL", "debug"),
            "days" => 14,
        ],
        "stderr" => [
            "driver" => "monolog",
            "level" => env("LOG_LEVEL", "debug"),
            "handler" => StreamHandler::class,
            "formatter" => env("LOG_STDERR_FORMATTER"),
            "with" => [
                "stream" => "php://stderr",
            ],
        ],
        "syslog" => [
            "driver" => "syslog",
            "level" => env("LOG_LEVEL", "debug"),
        ],
        "errorlog" => [
            "driver" => "errorlog",
            "level" => env("LOG_LEVEL", "debug"),
        ],
        "null" => [
            "driver" => "monolog",
            "handler" => NullHandler::class,
        ],
        "emergency" => [
            "path" => storage_path("logs/laravel.log"),
        ],
    ],
];
