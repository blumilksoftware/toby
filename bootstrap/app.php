<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Toby\Http\Middleware\HandleInertiaRequests;
use Toby\Http\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(HandleInertiaRequests::class);
        $middleware->alias([
            "guest" => RedirectIfAuthenticated::class,
        ]);
        $middleware->trustProxies(
            at: "*",
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB,
        );
        $middleware->statefulApi();
    })
    ->withExceptions()
    ->create();
