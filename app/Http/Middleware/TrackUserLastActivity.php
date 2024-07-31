<?php

declare(strict_types=1);

namespace Toby\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TrackUserLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        $request->user()?->update([
            "last_active_at" => Carbon::now(),
        ]);

        return $next($request);
    }
}
