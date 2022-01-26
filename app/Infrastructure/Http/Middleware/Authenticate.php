<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): string
    {
        return route("login");
    }
}
