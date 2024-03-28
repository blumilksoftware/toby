<?php

declare(strict_types=1);

namespace Toby\Exceptions;

use Illuminate\Foundation\Exceptions\Handler;
use Inertia\Inertia;
use Sentry\State\HubInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionHandler extends Handler
{
    protected $dontFlash = [
        "current_password",
        "password",
        "password_confirmation",
    ];
    protected array $handleByInertia = [
        Response::HTTP_INTERNAL_SERVER_ERROR,
        Response::HTTP_SERVICE_UNAVAILABLE,
        Response::HTTP_TOO_MANY_REQUESTS,
        419, // CSRF
        Response::HTTP_NOT_FOUND,
        Response::HTTP_FORBIDDEN,
        Response::HTTP_UNAUTHORIZED,
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $exception): void {
            if (app()->bound(HubInterface::class)) {
                app(HubInterface::class)->captureException($exception);
            }
        });
    }

    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);

        if (!app()->environment("production")) {
            return $response;
        }

        if ($response->status() === Response::HTTP_METHOD_NOT_ALLOWED) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if (in_array($response->status(), $this->handleByInertia, true)) {
            return Inertia::render("Error", [
                "status" => $response->status(),
            ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        return $response;
    }
}
