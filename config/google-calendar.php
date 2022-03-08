<?php

declare(strict_types=1);

return [
    "default_auth_profile" => env("GOOGLE_CALENDAR_AUTH_PROFILE", "service_account"),
    "auth_profiles" => [
        "service_account" => [
            "credentials_json" => base_path("google-credentials.json"),
        ],
    ],
    "calendar_id" => env("GOOGLE_CALENDAR_ID"),
    "user_to_impersonate" => env("GOOGLE_CALENDAR_IMPERSONATE"),
];
