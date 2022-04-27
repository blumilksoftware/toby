<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Infrastructure\Http\Controllers\Api\CalculateUserUnavailableDaysController;
use Toby\Infrastructure\Http\Controllers\Api\CalculateUserVacationStatsController;
use Toby\Infrastructure\Http\Controllers\Api\CalculateVacationDaysController;
use Toby\Infrastructure\Http\Controllers\Api\GetAvailableVacationTypesController;
use Toby\Infrastructure\Slack\Controller as SlackController;

Route::post("slack", [SlackController::class, "getResponse"]);

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("vacation/calculate-days", CalculateVacationDaysController::class);
    Route::post("vacation/calculate-stats", CalculateUserVacationStatsController::class);
    Route::post("vacation/calculate-unavailable-days", CalculateUserUnavailableDaysController::class);
    Route::post("vacation/get-available-vacation-types", GetAvailableVacationTypesController::class);
});
