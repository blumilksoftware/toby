<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Http\Controllers\AnnualSummaryController;
use Toby\Http\Controllers\AssignedBenefitController;
use Toby\Http\Controllers\BenefitController;
use Toby\Http\Controllers\BenefitsReportController;
use Toby\Http\Controllers\DashboardController;
use Toby\Http\Controllers\EmployeesMilestonesController;
use Toby\Http\Controllers\EquipmentController;
use Toby\Http\Controllers\EquipmentLabelController;
use Toby\Http\Controllers\GoogleController;
use Toby\Http\Controllers\HolidayController;
use Toby\Http\Controllers\KeysController;
use Toby\Http\Controllers\LocalLoginController;
use Toby\Http\Controllers\LoginController;
use Toby\Http\Controllers\LogoutController;
use Toby\Http\Controllers\MonthlyUsageController;
use Toby\Http\Controllers\OvertimeRequestController;
use Toby\Http\Controllers\OvertimeTimesheetController;
use Toby\Http\Controllers\PermissionController;
use Toby\Http\Controllers\ResumeController;
use Toby\Http\Controllers\TechnologyController;
use Toby\Http\Controllers\TimesheetController;
use Toby\Http\Controllers\UserController;
use Toby\Http\Controllers\UserHistoryController;
use Toby\Http\Controllers\VacationCalendarController;
use Toby\Http\Controllers\VacationLimitController;
use Toby\Http\Controllers\VacationRequestController;
use Toby\Http\Middleware\CheckIfLocalEnvironment;
use Toby\Http\Middleware\TrackUserLastActivity;

Route::middleware(["auth", TrackUserLastActivity::class])->group(function (): void {
    Route::get("/", [DashboardController::class, "index"])
        ->name("dashboard");
    Route::post("/logout", LogoutController::class);

    Route::resource("users", UserController::class)
        ->except("show")
        ->withTrashed()
        ->whereNumber("user");
    Route::get("/users/{user}", [UserController::class, "show"])
        ->withTrashed()
        ->whereNumber("user");
    Route::post("/users/{user}/restore", [UserController::class, "restore"])
        ->whereNumber("user")
        ->withTrashed();
    Route::get("/users/{user}/permissions", [PermissionController::class, "show"])
        ->withTrashed()
        ->whereNumber("user");
    Route::patch("/users/{user}/permissions", [PermissionController::class, "update"])
        ->withTrashed()
        ->whereNumber("user");
    Route::get("/users/{user}/history", [UserHistoryController::class, "index"])
        ->withTrashed()
        ->whereNumber("user")
        ->name("users.history");
    Route::get("/users/{user}/history/create", [UserHistoryController::class, "create"])
        ->withTrashed()
        ->whereNumber("user");
    Route::post("/users/{user}/history", [UserHistoryController::class, "store"])
        ->withTrashed()
        ->whereNumber("user");
    Route::get("/users/history/{history}", [UserHistoryController::class, "edit"])
        ->withTrashed()
        ->whereNumber("history");
    Route::put("/users/history/{history}", [UserHistoryController::class, "update"])
        ->withTrashed()
        ->whereNumber("history");
    Route::delete("/users/history/{history}", [UserHistoryController::class, "destroy"])
        ->withTrashed()
        ->whereNumber("history");

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

    Route::get("/calendar/{month?}/{year?}", [VacationCalendarController::class, "index"])
        ->name("calendar");

    Route::prefix("/vacation")->as("vacation.")->group(function (): void {
        Route::get("/limits", [VacationLimitController::class, "edit"])
            ->name("limits");
        Route::post("/limits/take-from-last-year", [VacationLimitController::class, "takeFromLastYear"])
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
    Route::prefix("/overtime")->as("overtime.")->group(function (): void {
        Route::get("/requests", [OvertimeRequestController::class, "indexForApprovers"])
            ->name("requests.indexForApprovers");
        Route::post("/requests", [OvertimeRequestController::class, "store"])
            ->name("requests.store");
        Route::get("/requests/me", [OvertimeRequestController::class, "index"])
            ->name("requests.index");
        Route::get("/requests/create", [OvertimeRequestController::class, "create"])
            ->name("requests.create");
        Route::get("/requests/{overtimeRequest}", [OvertimeRequestController::class, "show"])
            ->whereNumber("overtimeRequest")
            ->name("requests.show");
        Route::post("/requests/{overtimeRequest}/reject", [OvertimeRequestController::class, "reject"])
            ->whereNumber("overtimeRequest")
            ->name("requests.reject");
        Route::post("/requests/{overtimeRequest}/cancel", [OvertimeRequestController::class, "cancel"])
            ->whereNumber("overtimeRequest")
            ->name("requests.cancel");
        Route::post("/requests/{overtimeRequest}/settle", [OvertimeRequestController::class, "settle"])
            ->whereNumber("overtimeRequest")
            ->name("requests.settle");
        Route::post("/requests/{overtimeRequest}/accept-as-technical", [OvertimeRequestController::class, "acceptAsTechnical"])
            ->whereNumber("overtimeRequest")
            ->name("requests.accept-as-technical");
        Route::get("/timesheet/{month}", OvertimeTimesheetController::class)
            ->name("overtime-timesheet");
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
