<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Http\Controllers\Api\CalculateOvertimeHoursController;
use Toby\Http\Controllers\Api\CalculateUserUnavailableDaysController;
use Toby\Http\Controllers\Api\CalculateUserVacationStatsController;
use Toby\Http\Controllers\Api\CalculateVacationDaysController;
use Toby\Http\Controllers\Api\GetAvailableVacationTypesController;
use Toby\Http\Controllers\Api\LastUpdateController;
use Toby\Http\Controllers\DashboardController;
use Toby\Slack\SlackActionController;
use Toby\Slack\SlackCommandController;

Route::post("slack/commands", [SlackCommandController::class, "getResponse"]);
Route::post("slack/actions", [SlackActionController::class, "handleAction"]);

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("vacation/calculate-days", CalculateVacationDaysController::class);
    Route::post("vacation/calculate-stats", CalculateUserVacationStatsController::class);
    Route::post("vacation/calculate-unavailable-days", CalculateUserUnavailableDaysController::class);
    Route::post("vacation/get-available-vacation-types", GetAvailableVacationTypesController::class);
    Route::post("overtime/calculate-hours", CalculateOvertimeHoursController::class);

    Route::get("dashboard/calendar/{year}", [DashboardController::class, "loadCalendar"]);
    Route::get("last-update", LastUpdateController::class);
});
