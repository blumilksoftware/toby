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
            "auth" => fn() => [
                "user" => [
                    "name" => "Chelsea Hagon",
                    "email" => "chelseahagon@example.com",
                    "role" => "Human Resources Manager",
                    "imageUrl" =>
                        "https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
                ],
            ],
            "flash" => fn() => [
                "success" => $request->session()->get("success"),
                "error" => $request->session()->get("error"),
            ],
        ]);
    }
}
