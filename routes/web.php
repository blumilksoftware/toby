<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Controllers\GoogleController;
use Toby\Http\Controllers\LogoutController;
use Toby\Http\Controllers\UserController;
use Toby\Http\Controllers\VacationLimitController;
use Toby\Models\YearPeriod;

Route::middleware("auth")->group(function (): void {
    Route::get("/", fn() => inertia("Dashboard"))->name("dashboard");
    Route::post("/logout", LogoutController::class);

    Route::resource("users", UserController::class);
    Route::post("users/{user}/restore", [UserController::class, "restore"])->withTrashed();

    Route::get("/vacation-days", [VacationLimitController::class, "edit"])->name("vacation.days");
    Route::put("/vacation-days", [VacationLimitController::class, "update"]);

    Route::post("year-periods/{yearPeriod}/select", function (Request $request, YearPeriod $yearPeriod) {
        $request->session()->put(YearPeriodRetriever::SESSION_KEY, $yearPeriod->id);

        return redirect()->back();
    })->name("year-periods.select");
});

Route::middleware("guest")->group(function (): void {
    Route::get("login", fn() => inertia("Login"))->name("login");
    Route::get("login/google/start", [GoogleController::class, "redirect"])
        ->name("login.google.start");
    Route::get("login/google/end", [GoogleController::class, "callback"])
        ->name("login.google.end");
});
