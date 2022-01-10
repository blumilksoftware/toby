<?php

declare(strict_types=1);

$app = new Illuminate\Foundation\Application(
    $_ENV["APP_BASE_PATH"] ?? dirname(__DIR__),
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    Toby\Http\Kernel::class,
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Toby\Console\Kernel::class,
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Toby\Exceptions\Handler::class,
);

return $app;
