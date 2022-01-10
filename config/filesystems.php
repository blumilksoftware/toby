<?php

declare(strict_types=1);

return [
    "default" => env("FILESYSTEM_DRIVER", "local"),
    "disks" => [
        "local" => [
            "driver" => "local",
            "root" => storage_path("app"),
        ],
        "public" => [
            "driver" => "local",
            "root" => storage_path("app/public"),
            "url" => env("APP_URL") . "/storage",
            "visibility" => "public",
        ],
    ],
    "links" => [
        public_path("storage") => storage_path("app/public"),
    ],
];
