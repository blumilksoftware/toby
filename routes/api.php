<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Http\Controllers\Api\CalculateVacationDaysController;
use Toby\Infrastructure\Http\Requests\Api\CalculateVacationStatsRequest;

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("calculate-vacation-days", CalculateVacationDaysController::class);
    Route::post("calculate-vacations-stats", function (CalculateVacationStatsRequest $request, UserVacationStatsRetriever $vacationStatsRetriever) {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));
        $yearPeriod = YearPeriod::current();

        $limit = $vacationStatsRetriever->getVacationDaysLimit($user, $yearPeriod);
        $used = $vacationStatsRetriever->getUsedVacationDays($user, $yearPeriod);
        $pending = $vacationStatsRetriever->getPendingVacationDays($user, $yearPeriod);
        $other = $vacationStatsRetriever->getOtherApprovedVacationDays($user, $yearPeriod);

        return [
            "limit" => $limit,
            "remaining" => $limit - $used - $pending,
            "used" => $used,
            "pending" => $pending,
            "other" => $other,
        ];
    });
});
