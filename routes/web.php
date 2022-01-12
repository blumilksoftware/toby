<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Toby\Http\Controllers\GoogleController;

Route::middleware("auth")->group(function (): void {
    Route::get("/", fn() => inertia("Dashboard"))->name("dashboard");
    Route::get("/logout", function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect()->route("login");
    });
});

Route::middleware("guest")->group(function(): void {
    Route::get("login", fn() => inertia("Login"))->name("login");
    Route::get("login/google/start", [GoogleController::class, "redirect"])
        ->name("login.google.start");
    Route::get("login/google/end", [GoogleController::class, "callback"])
        ->name("login.google.end");
});