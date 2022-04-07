<?php

declare(strict_types=1);

return [
    "default" => env("QUEUE_CONNECTION", "redis"),
    "connections" => [
        "sync" => [
            "driver" => "sync",
        ],
        "redis" => [
            "driver" => "redis",
            "connection" => "default",
            "queue" => env("REDIS_QUEUE", "default"),
            "retry_after" => 90,
            "block_for" => null,
            "after_commit" => false,
        ],
    ],
    "failed" => [
        "driver" => env("QUEUE_FAILED_DRIVER", "database-uuids"),
        "database" => env("DB_CONNECTION", "mysql"),
        "table" => "failed_jobs",
    ],
];
