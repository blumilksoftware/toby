<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [
    "default" => env("CACHE_DRIVER", "file"),
    "stores" => [
        "array" => [
            "driver" => "array",
            "serialize" => false,
        ],
        "database" => [
            "driver" => "database",
            "table" => "cache",
            "connection" => null,
            "lock_connection" => null,
        ],
        "file" => [
            "driver" => "file",
            "path" => storage_path("framework/cache/data"),
        ],
    ],
    "prefix" => env("CACHE_PREFIX", Str::slug(env("APP_NAME", "laravel"), "_") . "_cache"),
];
