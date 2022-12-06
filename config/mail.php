<?php

declare(strict_types=1);

return [
    "default" => env("MAIL_MAILER", "smtp"),
    "mailers" => [
        "smtp" => [
            "transport" => "smtp",
            "host" => env("MAIL_HOST", "smtp.mailgun.org"),
            "port" => env("MAIL_PORT", 587),
            "encryption" => env("MAIL_ENCRYPTION", "tls"),
            "username" => env("MAIL_USERNAME"),
            "password" => env("MAIL_PASSWORD"),
            "timeout" => null,
            "auth_mode" => null,
        ],
        "log" => [
            "transport" => "log",
            "channel" => env("MAIL_LOG_CHANNEL"),
        ],
        "array" => [
            "transport" => "array",
        ],
        "failover" => [
            "transport" => "failover",
            "mailers" => [
                "smtp",
                "log",
            ],
        ],
    ],
    "from" => [
        "address" => env("MAIL_FROM_ADDRESS", "hello@example.com"),
        "name" => env("MAIL_FROM_NAME", "Example"),
    ],
    "markdown" => [
        "theme" => "mail",
        "paths" => [
            resource_path("views/vendor/mail"),
        ],
    ],
    "database_backup" => [
        "notify_on_failure" => env("MAIL_DATABASE_BACKUP_NOTIFY_ON_FAILURE", default: false),
        "notification_email" => env("MAIL_DATABASE_BACKUP_NOTIFICATION_EMAIL"),
    ],
];
