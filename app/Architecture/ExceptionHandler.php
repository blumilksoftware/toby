<?php

declare(strict_types=1);

namespace Toby\Architecture;

use Illuminate\Foundation\Exceptions\Handler;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionHandler extends Handler
{
    protected $dontFlash = [
        "current_password",
        "password",
        "password_confirmation",
    ];

    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);

        if (app()->environment("production") && in_array($response->status(), [500, 503, 429, 419, 404, 403, 401], true)) {
            return Inertia::render("Error", [
                "status" => $response->status(),
            ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        return $response;
    }
}
