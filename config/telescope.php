<?php

declare(strict_types=1);

use Laravel\Telescope\Http\Middleware\Authorize;
use Laravel\Telescope\Watchers\BatchWatcher;
use Laravel\Telescope\Watchers\CacheWatcher;
use Laravel\Telescope\Watchers\ClientRequestWatcher;
use Laravel\Telescope\Watchers\CommandWatcher;
use Laravel\Telescope\Watchers\DumpWatcher;
use Laravel\Telescope\Watchers\EventWatcher;
use Laravel\Telescope\Watchers\ExceptionWatcher;
use Laravel\Telescope\Watchers\GateWatcher;
use Laravel\Telescope\Watchers\JobWatcher;
use Laravel\Telescope\Watchers\LogWatcher;
use Laravel\Telescope\Watchers\MailWatcher;
use Laravel\Telescope\Watchers\ModelWatcher;
use Laravel\Telescope\Watchers\NotificationWatcher;
use Laravel\Telescope\Watchers\QueryWatcher;
use Laravel\Telescope\Watchers\RedisWatcher;
use Laravel\Telescope\Watchers\RequestWatcher;
use Laravel\Telescope\Watchers\ScheduleWatcher;
use Laravel\Telescope\Watchers\ViewWatcher;

return [
    "domain" => env("TELESCOPE_DOMAIN"),
    "path" => env("TELESCOPE_PATH", "telescope"),
    "driver" => env("TELESCOPE_DRIVER", "database"),
    "storage" => [
        "database" => [
            "connection" => env("DB_CONNECTION", "mysql"),
            "chunk" => 1000,
        ],
    ],
    "enabled" => env("TELESCOPE_ENABLED", true),
    "middleware" => [
        "web",
        Authorize::class,
    ],
    "only_paths" => [],
    "ignore_paths" => [
        "nova-api*",
    ],
    "ignore_commands" => [],
    "watchers" => [
        BatchWatcher::class => env("TELESCOPE_BATCH_WATCHER", true),
        CacheWatcher::class => env("TELESCOPE_CACHE_WATCHER", true),
        ClientRequestWatcher::class => env("TELESCOPE_CLIENT_REQUEST_WATCHER", true),

        CommandWatcher::class => [
            "enabled" => env("TELESCOPE_COMMAND_WATCHER", true),
            "ignore" => [],
        ],

        DumpWatcher::class => env("TELESCOPE_DUMP_WATCHER", true),

        EventWatcher::class => [
            "enabled" => env("TELESCOPE_EVENT_WATCHER", true),
            "ignore" => [],
        ],

        ExceptionWatcher::class => env("TELESCOPE_EXCEPTION_WATCHER", true),

        GateWatcher::class => [
            "enabled" => env("TELESCOPE_GATE_WATCHER", true),
            "ignore_abilities" => [],
            "ignore_packages" => true,
        ],

        JobWatcher::class => env("TELESCOPE_JOB_WATCHER", true),
        LogWatcher::class => env("TELESCOPE_LOG_WATCHER", true),
        MailWatcher::class => env("TELESCOPE_MAIL_WATCHER", true),

        ModelWatcher::class => [
            "enabled" => env("TELESCOPE_MODEL_WATCHER", true),
            "events" => ["eloquent.*"],
            "hydrations" => true,
        ],

        NotificationWatcher::class => env("TELESCOPE_NOTIFICATION_WATCHER", true),

        QueryWatcher::class => [
            "enabled" => env("TELESCOPE_QUERY_WATCHER", true),
            "ignore_packages" => true,
            "slow" => 100,
        ],

        RedisWatcher::class => env("TELESCOPE_REDIS_WATCHER", true),

        RequestWatcher::class => [
            "enabled" => env("TELESCOPE_REQUEST_WATCHER", true),
            "size_limit" => env("TELESCOPE_RESPONSE_SIZE_LIMIT", 64),
            "ignore_status_codes" => [],
        ],

        ScheduleWatcher::class => env("TELESCOPE_SCHEDULE_WATCHER", true),
        ViewWatcher::class => env("TELESCOPE_VIEW_WATCHER", true),
    ],
];
