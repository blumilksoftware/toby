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

        if (!app()->environment(["local", "testing"]) && in_array($response->status(), [500, 503, 404, 403], true)) {
            return Inertia::render("Error", [
                "status" => $response->status(),
            ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        if ($response->status() === 419) {
            return back()->with([
                "message" => "The page expired, please try again.",
            ]);
        }

        return $response;
    }
}
