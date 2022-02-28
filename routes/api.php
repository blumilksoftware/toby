<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Toby\Infrastructure\Http\Controllers\Api\CalculateVacationDaysController;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("calculate-vacation-days", CalculateVacationDaysController::class);
    Route::get("get-vacations-info", function (Request $request) {
        return [
            "pending" => 4,
            "used" => 10,
            "other" => 12,
        ];
    });
});
