<?php

declare(strict_types=1);

namespace Toby\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            "flash" => fn() => [
                "success" => $request->session()->get("success"),
                "error" => $request->session()->get("error"),
            ],
        ]);
    }
}
