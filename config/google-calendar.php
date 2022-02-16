<?php

declare(strict_types=1);

return [
    "default_auth_profile" => env("GOOGLE_CALENDAR_AUTH_PROFILE", "service_account"),
    "auth_profiles" => [
        "service_account" => [
            "credentials_json" => storage_path("app/google-calendar/service-account-credentials.json"),
        ],
        "oauth" => [
            "credentials_json" => storage_path("app/google-calendar/oauth-credentials.json"),
            "token_json" => storage_path("app/google-calendar/oauth-token.json"),
        ],
    ],

    "calendar_id" => env("GOOGLE_CALENDAR_ID"),
    "user_to_impersonate" => env("GOOGLE_CALENDAR_IMPERSONATE"),
];
