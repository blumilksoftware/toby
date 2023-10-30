<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Infrastructure\Http\Controllers\AnnualSummaryController;
use Toby\Infrastructure\Http\Controllers\AssignedBenefitController;
use Toby\Infrastructure\Http\Controllers\BenefitController;
use Toby\Infrastructure\Http\Controllers\BenefitsReportController;
use Toby\Infrastructure\Http\Controllers\DashboardController;
use Toby\Infrastructure\Http\Controllers\EmployeesMilestonesController;
use Toby\Infrastructure\Http\Controllers\EquipmentController;
use Toby\Infrastructure\Http\Controllers\EquipmentLabelController;
use Toby\Infrastructure\Http\Controllers\GoogleController;
use Toby\Infrastructure\Http\Controllers\HolidayController;
use Toby\Infrastructure\Http\Controllers\KeysController;
use Toby\Infrastructure\Http\Controllers\LocalLoginController;
use Toby\Infrastructure\Http\Controllers\LoginController;
use Toby\Infrastructure\Http\Controllers\LogoutController;
use Toby\Infrastructure\Http\Controllers\MonthlyUsageController;
use Toby\Infrastructure\Http\Controllers\PermissionController;
use Toby\Infrastructure\Http\Controllers\ResumeController;
use Toby\Infrastructure\Http\Controllers\SelectYearPeriodController;
use Toby\Infrastructure\Http\Controllers\TechnologyController;
use Toby\Infrastructure\Http\Controllers\TimesheetController;
use Toby\Infrastructure\Http\Controllers\UserController;
use Toby\Infrastructure\Http\Controllers\VacationCalendarController;
use Toby\Infrastructure\Http\Controllers\VacationLimitController;
use Toby\Infrastructure\Http\Controllers\VacationRequestController;
use Toby\Infrastructure\Http\Middleware\CheckIfLocalEnvironment;
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

    Route::resource("equipment-items", EquipmentController::class)
        ->except("show")
        ->whereNumber("equipmentItem");
    Route::get("/equipment-items/me", [EquipmentController::class, "indexForEmployee"])
        ->name("equipment-items.indexForEmployee");
    Route::get("/equipment-items/download", [EquipmentController::class, "downloadExcel"])
        ->name("equipment-items.download");

    Route::resource("equipment-labels", EquipmentLabelController::class)
        ->only(["index", "store", "destroy"])
        ->whereNumber("equipmentLabels");

    Route::get("/users/{user}/permissions", [PermissionController::class, "show"])
        ->whereNumber("user");
    Route::patch("/users/{user}/permissions", [PermissionController::class, "update"])
        ->whereNumber("user");

    Route::resource("benefits", BenefitController::class)
        ->only(["index", "store", "destroy"])
        ->whereNumber("benefit");

    Route::get("/assigned-benefits", [AssignedBenefitController::class, "index"])
        ->name("assigned-benefits.index");

    Route::put("/assigned-benefits", [AssignedBenefitController::class, "update"])
        ->name("assigned-benefits.update");

    Route::get("/benefits-reports", [BenefitsReportController::class, "index"])
        ->name("benefits-reports.index");

    Route::post("/benefits-reports", [BenefitsReportController::class, "store"])
        ->name("benefits-reports.store");

    Route::delete("/benefits-reports/{benefitsReport}", [BenefitsReportController::class, "destroy"])
        ->name("benefits-reports.delete")
        ->whereNumber("report");

    Route::get("/benefits-reports/{benefitsReport}", [BenefitsReportController::class, "show"])
        ->name("benefits-reports.show")
        ->whereNumber("report");

    Route::get("/benefits-reports/{benefitsReport}/download", [BenefitsReportController::class, "download"])
        ->name("benefits-reports.download")
        ->whereNumber("report");

    Route::resource("holidays", HolidayController::class)
        ->except("show")
        ->whereNumber("holiday");

    Route::resource("resumes", ResumeController::class)
        ->whereNumber("resume");
    Route::resource("technologies", TechnologyController::class)
        ->only(["index", "store", "destroy"])
        ->whereNumber("technology");

    Route::get("/keys", [KeysController::class, "index"]);
    Route::post("/keys", [KeysController::class, "store"]);
    Route::delete("/keys/{key}", [KeysController::class, "destroy"]);
    Route::post("/keys/{key}/take", [KeysController::class, "take"]);
    Route::post("/keys/{key}/give", [KeysController::class, "give"]);
    Route::post("/keys/{key}/leave-in-the-office", [KeysController::class, "leaveInTheOffice"]);

    Route::get("/employees-milestones", [EmployeesMilestonesController::class, "index"])
        ->name("employees-milestones");

    Route::post("/year-periods/{yearPeriod}/select", SelectYearPeriodController::class)
        ->whereNumber("yearPeriod")
        ->name("year-periods.select");

    Route::get("/calendar/{month?}", [VacationCalendarController::class, "index"])
        ->name("calendar");

    Route::prefix("/vacation")->as("vacation.")->group(function (): void {
        Route::get("/limits", [VacationLimitController::class, "edit"])
            ->name("limits");
        Route::post("/limits/{limit}/take-from-last-year", [VacationLimitController::class, "takeFromLastYear"])
            ->name("limits.take");

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
        Route::post("/requests/{vacationRequest}/accept-as-technical", [VacationRequestController::class, "acceptAsTechnical"])
            ->whereNumber("vacationRequest")
            ->name("requests.accept-as-technical");
        Route::post("/requests/{vacationRequest}/accept-as-administrative", [VacationRequestController::class, "acceptAsAdministrative"])
            ->whereNumber("vacationRequest")
            ->name("requests.accept-as-administrative");

        Route::get("/monthly-usage", MonthlyUsageController::class)
            ->name("monthly-usage");
        Route::get("/annual-summary", AnnualSummaryController::class)
            ->name("annual-summary");
    });
});

Route::middleware("guest")->group(function (): void {
    Route::get("login", LoginController::class)
        ->name("login");

    Route::middleware(CheckIfLocalEnvironment::class)->group(function (): void {
        Route::get("login/local", fn() => inertia("LocalLogin"))
            ->name("login.local");
        Route::post("login/local", LocalLoginController::class)
            ->name("login.local.post");
    });

    Route::get("login/google/start", [GoogleController::class, "redirect"])
        ->name("login.google.start");
    Route::get("login/google/end", [GoogleController::class, "callback"])
        ->name("login.google.end");
});
