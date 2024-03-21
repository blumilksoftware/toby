<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Toby\Infrastructure\Http\Controllers\Api\CalculateUserUnavailableDaysController;
use Toby\Infrastructure\Http\Controllers\Api\CalculateUserVacationStatsController;
use Toby\Infrastructure\Http\Controllers\Api\CalculateVacationDaysController;
use Toby\Infrastructure\Http\Controllers\Api\GetAvailableVacationTypesController;
use Toby\Infrastructure\Slack\SlackActionController;
use Toby\Infrastructure\Slack\SlackCommandController;

Route::post("slack/commands", [SlackCommandController::class, "getResponse"]);
Route::post("slack/actions", [SlackActionController::class, "handleVacationRequestAction"]);

Route::get("last-update", function (): JsonResponse {
    return new JsonResponse([
        "lastUpdate" => Cache::get("last_update", Carbon::now()->toIso8601String()),
    ]);
});

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("vacation/calculate-days", CalculateVacationDaysController::class);
    Route::post("vacation/calculate-stats", CalculateUserVacationStatsController::class);
    Route::post("vacation/calculate-unavailable-days", CalculateUserUnavailableDaysController::class);
    Route::post("vacation/get-available-vacation-types", GetAvailableVacationTypesController::class);
});
