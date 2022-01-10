<?php

declare(strict_types=1);

return [
    "default" => env("QUEUE_CONNECTION", "sync"),
    "connections" => [
        "sync" => [
            "driver" => "sync",
        ],
    ],
    "failed" => [
        "driver" => env("QUEUE_FAILED_DRIVER", "database-uuids"),
        "database" => env("DB_CONNECTION", "mysql"),
        "table" => "failed_jobs",
    ],
];
