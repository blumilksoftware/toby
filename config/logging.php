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
            "replace_placeholders" => true,
        ],
        "daily" => [
            "driver" => "daily",
            "path" => storage_path("logs/laravel.log"),
            "level" => env("LOG_LEVEL", "debug"),
            "days" => 14,
            "replace_placeholders" => true,
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
            "facility" => LOG_USER,
            "replace_placeholders" => true,
        ],
        "errorlog" => [
            "driver" => "errorlog",
            "level" => env("LOG_LEVEL", "debug"),
            "replace_placeholders" => true,
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
