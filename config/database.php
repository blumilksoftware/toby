<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [
    "default" => env("DB_CONNECTION", "mysql"),
    "connections" => [
        "pgsql" => [
            "driver" => "pgsql",
            "url" => env("DATABASE_URL"),
            "host" => env("DB_HOST", "127.0.0.1"),
            "port" => env("DB_PORT", "5432"),
            "database" => env("DB_DATABASE", "forge"),
            "username" => env("DB_USERNAME", "forge"),
            "password" => env("DB_PASSWORD", ""),
            "charset" => "utf8",
            "prefix" => "",
            "prefix_indexes" => true,
            "search_path" => "public",
            "sslmode" => "prefer",
        ],
    ],
    "migrations" => "migrations",
    "redis" => [
        "client" => env("REDIS_CLIENT", "phpredis"),
        "options" => [
            "cluster" => env("REDIS_CLUSTER", "redis"),
            "prefix" => env("REDIS_PREFIX", Str::slug(env("APP_NAME", "laravel"), "_") . "_database_"),
        ],
        "default" => [
            "scheme" => "tls",
            "url" => env("REDIS_URL"),
            "host" => env("REDIS_HOST", "127.0.0.1"),
            "password" => env("REDIS_PASSWORD"),
            "port" => env("REDIS_PORT", "6379"),
            "database" => env("REDIS_DB", "0"),
        ],
        "cache" => [
            "scheme" => "tls",
            "url" => env("REDIS_URL"),
            "host" => env("REDIS_HOST", "127.0.0.1"),
            "password" => env("REDIS_PASSWORD"),
            "port" => env("REDIS_PORT", "6379"),
            "database" => env("REDIS_CACHE_DB", "1"),
        ],
    ],
];
