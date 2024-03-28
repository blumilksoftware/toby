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
            "table" => "password_reset_tokens",
            "expire" => 60,
            "throttle" => 60,
        ],
    ],
    "password_timeout" => 10800,
    "local_email_for_login_via_google" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
];
