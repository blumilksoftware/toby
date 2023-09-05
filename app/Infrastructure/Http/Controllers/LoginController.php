<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Foundation\Application;
use Inertia\Response;

class LoginController extends Controller
{
    public function __invoke(Application $app): Response
    {
        return inertia("Login", [
            "showLocalLoginButton" => $app->environment("local"),
        ]);
    }
}
