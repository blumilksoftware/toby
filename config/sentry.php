<?php

declare(strict_types=1);

return [
    "dsn" => env("SENTRY_LARAVEL_DSN", env("SENTRY_DSN")),
    "release" => env("SENTRY_RELEASE"),
    "environment" => env("SENTRY_ENVIRONMENT"),
    "breadcrumbs" => [
        "logs" => true,
        "cache" => true,
        "livewire" => true,
        "sql_queries" => true,
        "sql_bindings" => true,
        "queue_info" => true,
        "command_info" => true,
        "http_client_requests" => true,
    ],
    "tracing" => [
        "queue_job_transactions" => env("SENTRY_TRACE_QUEUE_ENABLED", false),
        "queue_jobs" => true,
        "sql_queries" => true,
        "sql_origin" => true,
        "views" => true,
        "livewire" => true,
        "http_client_requests" => true,
        "redis_commands" => env("SENTRY_TRACE_REDIS_COMMANDS", false),
        "redis_origin" => true,
        "default_integrations" => true,
        "missing_routes" => false,
    ],
    "send_default_pii" => env("SENTRY_SEND_DEFAULT_PII", false),
    "traces_sample_rate" => env("SENTRY_TRACES_SAMPLE_RATE") === null ? null : (float)env("SENTRY_TRACES_SAMPLE_RATE"),
    "profiles_sample_rate" => env("SENTRY_PROFILES_SAMPLE_RATE") === null ? null : (float)env("SENTRY_PROFILES_SAMPLE_RATE"),
];
