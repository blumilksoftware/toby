<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request as LaravelRequest;
use Inertia\Inertia;
use Sentry\State\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (Throwable $exception): void {
            if (app()->bound(HubInterface::class)) {
                app(HubInterface::class)->captureException($exception);
            }
        });

        $exceptions->respond(function (Response $response, Throwable $exception, LaravelRequest $request) {
            if (!app()->environment(["local", "testing"]) && in_array($response->getStatusCode(), [
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::HTTP_SERVICE_UNAVAILABLE,
                Response::HTTP_TOO_MANY_REQUESTS,
                Response::HTTP_NOT_FOUND,
                Response::HTTP_FORBIDDEN,
                Response::HTTP_UNAUTHORIZED,
            ], strict: true)) {
                return Inertia::render("Error", ["status" => $response->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            } elseif ($response->getStatusCode() === 419) {
                return back()->with([
                    "message" => "The page expired, please try again.",
                ]);
            }

            return $response;
        });
    })
    ->create();
