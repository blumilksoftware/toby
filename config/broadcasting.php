<?php

declare(strict_types=1);

return [
    "default" => env("BROADCAST_DRIVER", "null"),
    "connections" => [
        "log" => [
            "driver" => "log",
        ],
        "null" => [
            "driver" => "null",
        ],
    ],
];
