<?php

declare(strict_types=1);

use Toby\Models\User;

return [
    "defaults" => [
        "guard" => "web",
        "passwords" => "users",
    ],
    "guards" => [
        "web" => [
            "driver" => "session",
            "provider" => "users",
        ],
    ],
    "providers" => [
        "users" => [
            "driver" => "eloquent",
            "model" => User::class,
        ],
    ],
    "passwords" => [
        "users" => [
            "provider" => "users",
            "table" => "password_resets",
            "expire" => 60,
            "throttle" => 60,
        ],
    ],
    "password_timeout" => 10800,
];
