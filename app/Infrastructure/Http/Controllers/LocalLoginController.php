<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Toby\Infrastructure\Http\Requests\LocalLoginRequest;

class LocalLoginController extends Controller
{
    public function __invoke(LocalLoginRequest $request): RedirectResponse
    {
        $isAuthenticated = Auth::attempt([
            "email" => $request->input("email"),
            "password" => $request->input("password"),
        ]);

        if (!$isAuthenticated) {
            return redirect()
                ->route("login.local")
                ->withErrors([
                    "password" => __("Invalid credentials."),
                ]);
        }

        return redirect()->route("dashboard");
    }
}
