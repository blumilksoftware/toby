<?php

declare(strict_types=1);

return [
    "stateful" => explode(",", env("SANCTUM_STATEFUL_DOMAINS", sprintf(
        "%s%s",
        "localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1",
        env("APP_URL") ? "," . parse_url(env("APP_URL"), PHP_URL_HOST) : "",
    ))),
    "guard" => ["web"],
    "expiration" => null,
    "middleware" => [
        "verify_csrf_token" => Toby\Http\Middleware\VerifyCsrfToken::class,
        "encrypt_cookies" => Toby\Http\Middleware\EncryptCookies::class,
    ],
];
