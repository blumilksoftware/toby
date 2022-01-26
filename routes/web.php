<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Infrastructure\Http\Controllers\GoogleController;
use Toby\Infrastructure\Http\Controllers\HolidayController;
use Toby\Infrastructure\Http\Controllers\LogoutController;
use Toby\Infrastructure\Http\Controllers\SelectYearPeriodController;
use Toby\Infrastructure\Http\Controllers\UserController;
use Toby\Infrastructure\Http\Controllers\VacationLimitController;

Route::middleware("auth")->group(function (): void {
    Route::get("/", fn() => inertia("Dashboard"))->name("dashboard");
    Route::post("/logout", LogoutController::class);

    Route::resource("users", UserController::class);
    Route::post("/users/{user}/restore", [UserController::class, "restore"])->withTrashed();

    Route::resource("holidays", HolidayController::class);

    Route::get("/vacation-limits", [VacationLimitController::class, "edit"])->name("vacation.limits");
    Route::put("/vacation-limits", [VacationLimitController::class, "update"]);

    Route::post("year-periods/{yearPeriod}/select", SelectYearPeriodController::class)->name("year-periods.select");
});

Route::middleware("guest")->group(function (): void {
    Route::get("login", fn() => inertia("Login"))->name("login");
    Route::get("login/google/start", [GoogleController::class, "redirect"])
        ->name("login.google.start");
    Route::get("login/google/end", [GoogleController::class, "callback"])
        ->name("login.google.end");
});
