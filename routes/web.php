<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Infrastructure\Http\Controllers\DashboardController;
use Toby\Infrastructure\Http\Controllers\GoogleController;
use Toby\Infrastructure\Http\Controllers\HolidayController;
use Toby\Infrastructure\Http\Controllers\LogoutController;
use Toby\Infrastructure\Http\Controllers\MonthlyUsageController;
use Toby\Infrastructure\Http\Controllers\SelectYearPeriodController;
use Toby\Infrastructure\Http\Controllers\TimesheetController;
use Toby\Infrastructure\Http\Controllers\UserController;
use Toby\Infrastructure\Http\Controllers\VacationCalendarController;
use Toby\Infrastructure\Http\Controllers\VacationLimitController;
use Toby\Infrastructure\Http\Controllers\VacationRequestController;

Route::middleware("auth")->group(function (): void {
    Route::get("/", DashboardController::class)
        ->name("dashboard");
    Route::post("/logout", LogoutController::class);

    Route::resource("users", UserController::class);
    Route::post("/users/{user}/restore", [UserController::class, "restore"])
        ->withTrashed();

    Route::resource("holidays", HolidayController::class);
    Route::post("year-periods/{yearPeriod}/select", SelectYearPeriodController::class)
        ->name("year-periods.select");

    Route::prefix("/vacation")->group(function (): void {
        Route::get("/limits", [VacationLimitController::class, "edit"])
            ->name("vacation.limits");
        Route::get("/calendar/{month?}", [VacationCalendarController::class, "index"])
            ->name("vacation.calendar");
        Route::get("/timesheet/{month}", TimesheetController::class)
            ->name("timesheet");

        Route::get("/limits", [VacationLimitController::class, "edit"])
            ->name("vacation.limits");
        Route::put("/limits", [VacationLimitController::class, "update"]);

        Route::get("/requests", [VacationRequestController::class, "indexForApprovers"])
            ->name("vacation.requests.indexForApprovers");
        Route::get("/requests/me", [VacationRequestController::class, "index"])
            ->name("vacation.requests.index");
        Route::get("/requests/create", [VacationRequestController::class, "create"])
            ->name("vacation.requests.create");
        Route::post("/requests", [VacationRequestController::class, "store"])
            ->name("vacation.requests.store");
        Route::get("/requests/{vacationRequest}", [VacationRequestController::class, "show"])
            ->name("vacation.requests.show");
        Route::get("/requests/{vacationRequest}/download", [VacationRequestController::class, "download"])
            ->name("vacation.requests.download");
        Route::post("/requests/{vacationRequest}/reject", [VacationRequestController::class, "reject"])
            ->name("vacation.requests.reject");
        Route::post("/requests/{vacationRequest}/cancel", [VacationRequestController::class, "cancel"])
            ->name("vacation.requests.cancel");
        Route::post("/requests/{vacationRequest}/accept-as-technical", [VacationRequestController::class, "acceptAsTechnical"], )
            ->name("vacation.requests.accept-as-technical");
        Route::post("/requests/{vacationRequest}/accept-as-administrative", [VacationRequestController::class, "acceptAsAdministrative"], )
            ->name("vacation.requests.accept-as-administrative");

        Route::get("/monthly-usage", MonthlyUsageController::class)
            ->name("vacation.monthly-usage");
    });

    Route::get("/test", fn() => inertia("Test"));

});

Route::middleware("guest")->group(function (): void {
    Route::get("login", fn() => inertia("Login"))
        ->name("login");
    Route::get("login/google/start", [GoogleController::class, "redirect"])
        ->name("login.google.start");
    Route::get("login/google/end", [GoogleController::class, "callback"])
        ->name("login.google.end");
});
