<?php

declare(strict_types=1);
use Barryvdh\DomPDF\ServiceProvider;
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Notifications\NotificationServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Toby\Architecture\Providers\AppServiceProvider;
use Toby\Architecture\Providers\AuthServiceProvider as ApplicationAuthServiceProvider;
use Toby\Architecture\Providers\EventServiceProvider;
use Toby\Architecture\Providers\ObserverServiceProvider;
use Toby\Architecture\Providers\RouteServiceProvider;
use Toby\Architecture\Providers\TelescopeServiceProvider;

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
        AuthServiceProvider::class,
        BroadcastServiceProvider::class,
        BusServiceProvider::class,
        CacheServiceProvider::class,
        ConsoleSupportServiceProvider::class,
        CookieServiceProvider::class,
        DatabaseServiceProvider::class,
        EncryptionServiceProvider::class,
        FilesystemServiceProvider::class,
        FoundationServiceProvider::class,
        HashServiceProvider::class,
        MailServiceProvider::class,
        NotificationServiceProvider::class,
        PaginationServiceProvider::class,
        PipelineServiceProvider::class,
        QueueServiceProvider::class,
        RedisServiceProvider::class,
        PasswordResetServiceProvider::class,
        SessionServiceProvider::class,
        TranslationServiceProvider::class,
        ValidationServiceProvider::class,
        ViewServiceProvider::class,
        ServiceProvider::class,
        AppServiceProvider::class,
        ApplicationAuthServiceProvider::class,
        EventServiceProvider::class,
        RouteServiceProvider::class,
        TelescopeServiceProvider::class,
        ObserverServiceProvider::class,
    ],
];
