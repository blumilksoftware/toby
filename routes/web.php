<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Http\Controllers\GoogleController;
use Toby\Http\Controllers\LogoutController;
use Toby\Http\Controllers\UserController;
use Toby\Http\Controllers\VacationDaysController;

Route::middleware("auth")->group(function (): void {
    Route::get("/", fn() => inertia("Dashboard"))->name("dashboard");
    Route::post("/logout", LogoutController::class);

    Route::resource("users", UserController::class);
    Route::post("users/{user}/restore", [UserController::class, "restore"])->withTrashed();

    Route::get("/vacation-days", [VacationDaysController::class, "edit"])->name("vacation.days");
    Route::put("/vacation-days", [VacationDaysController::class, "update"]);
});

Route::middleware("guest")->group(function (): void {
    Route::get("login", fn() => inertia("Login"))->name("login");
    Route::get("login/google/start", [GoogleController::class, "redirect"])
        ->name("login.google.start");
    Route::get("login/google/end", [GoogleController::class, "callback"])
        ->name("login.google.end");
});
