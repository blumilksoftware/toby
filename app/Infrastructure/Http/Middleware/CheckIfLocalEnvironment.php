<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class CheckIfLocalEnvironment extends Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!app()->environment("local")) {
            return redirect()->back();
        }

        return $next($request);
    }
}
