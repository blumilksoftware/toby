<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Infrastructure\Http\Controllers\AnnualSummaryController;
use Toby\Infrastructure\Http\Controllers\DashboardController;
use Toby\Infrastructure\Http\Controllers\GoogleController;
use Toby\Infrastructure\Http\Controllers\HolidayController;
use Toby\Infrastructure\Http\Controllers\KeysController;
use Toby\Infrastructure\Http\Controllers\LogoutController;
use Toby\Infrastructure\Http\Controllers\MonthlyUsageController;
use Toby\Infrastructure\Http\Controllers\SelectYearPeriodController;
use Toby\Infrastructure\Http\Controllers\TimesheetController;
use Toby\Infrastructure\Http\Controllers\UserController;
use Toby\Infrastructure\Http\Controllers\VacationCalendarController;
use Toby\Infrastructure\Http\Controllers\VacationLimitController;
use Toby\Infrastructure\Http\Controllers\VacationRequestController;
use Toby\Infrastructure\Http\Middleware\TrackUserLastActivity;

Route::middleware(["auth", TrackUserLastActivity::class])->group(function (): void {
    Route::get("/", DashboardController::class)
        ->name("dashboard");
    Route::post("/logout", LogoutController::class);

    Route::resource("users", UserController::class)
        ->except("show")
        ->whereNumber("user");
    Route::post("/users/{user}/restore", [UserController::class, "restore"])
        ->whereNumber("user")
        ->withTrashed();

    Route::resource("holidays", HolidayController::class)
        ->except("show")
        ->whereNumber("holiday");

    Route::post("year-periods/{yearPeriod}/select", SelectYearPeriodController::class)
        ->whereNumber("yearPeriod")
        ->name("year-periods.select");
    Route::resource("keys", KeysController::class);

    Route::prefix("/vacation")->as("vacation.")->group(function (): void {
        Route::get("/limits", [VacationLimitController::class, "edit"])
            ->name("limits");
        Route::get("/calendar/{month?}", [VacationCalendarController::class, "index"])
            ->name("calendar");
        Route::get("/timesheet/{month}", TimesheetController::class)
            ->name("timesheet");

        Route::get("/limits", [VacationLimitController::class, "edit"])
            ->name("limits");
        Route::put("/limits", [VacationLimitController::class, "update"]);

        Route::get("/requests", [VacationRequestController::class, "indexForApprovers"])
            ->name("requests.indexForApprovers");
        Route::get("/requests/me", [VacationRequestController::class, "index"])
            ->name("requests.index");
        Route::get("/requests/create", [VacationRequestController::class, "create"])
            ->name("requests.create");
        Route::post("/requests", [VacationRequestController::class, "store"])
            ->name("requests.store");

        Route::get("/requests/{vacationRequest}", [VacationRequestController::class, "show"])
            ->whereNumber("vacationRequest")
            ->name("requests.show");
        Route::get("/requests/{vacationRequest}/download", [VacationRequestController::class, "download"])
            ->whereNumber("vacationRequest")
            ->name("requests.download");
        Route::post("/requests/{vacationRequest}/reject", [VacationRequestController::class, "reject"])
            ->whereNumber("vacationRequest")
            ->name("requests.reject");
        Route::post("/requests/{vacationRequest}/cancel", [VacationRequestController::class, "cancel"])
            ->whereNumber("vacationRequest")
            ->name("requests.cancel");
        Route::post("/requests/{vacationRequest}/accept-as-technical", [VacationRequestController::class, "acceptAsTechnical"], )
            ->whereNumber("vacationRequest")
            ->name("requests.accept-as-technical");
        Route::post("/requests/{vacationRequest}/accept-as-administrative", [VacationRequestController::class, "acceptAsAdministrative"], )
            ->whereNumber("vacationRequest")
            ->name("requests.accept-as-administrative");

        Route::get("/monthly-usage", MonthlyUsageController::class)
            ->name("monthly-usage");
        Route::get("/annual-summary", AnnualSummaryController::class)
            ->name("annual-summary");
    });
});

Route::middleware("guest")->group(function (): void {
    Route::get("login", fn() => inertia("Login"))
        ->name("login");
    Route::get("login/google/start", [GoogleController::class, "redirect"])
        ->name("login.google.start");
    Route::get("login/google/end", [GoogleController::class, "callback"])
        ->name("login.google.end");
});
