<?php

declare(strict_types=1);

return [
    "name" => env("APP_NAME", "Toby HR application"),
    "env" => env("APP_ENV", "production"),
    "debug" => (bool)env("APP_DEBUG", false),
    "url" => env("APP_URL", "http://localhost"),
    "asset_url" => env("ASSET_URL"),
    "timezone" => "Europe/Warsaw",
    "locale" => "pl",
    "fallback_locale" => "en",
    "faker_locale" => "pl_PL",
    "key" => env("APP_KEY"),
    "cipher" => "AES-256-CBC",
    "providers" => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        Toby\Architecture\Providers\AppServiceProvider::class,
        Toby\Architecture\Providers\AuthServiceProvider::class,
        Toby\Architecture\Providers\EventServiceProvider::class,
        Toby\Architecture\Providers\RouteServiceProvider::class,
        Toby\Architecture\Providers\TelescopeServiceProvider::class,
        Toby\Architecture\Providers\ObserverServiceProvider::class,
    ],
];
