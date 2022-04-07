<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Controllers\Controller;
use Toby\Infrastructure\Http\Requests\Api\CalculateUserUnavailableDaysRequest;

class CalculateUserUnavailableDaysController extends Controller
{
    public function __invoke(
        CalculateUserUnavailableDaysRequest $request,
        UserVacationStatsRetriever $vacationStatsRetriever,
        YearPeriodRetriever $yearPeriodRetriever,
    ): JsonResponse {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));
        $yearPeriod = $yearPeriodRetriever->selected();

        $holidays = $yearPeriod->holidays()->pluck("date");
        $vacationDays = $user->vacations()
            ->whereBelongsTo($yearPeriod)
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query): Builder => $query->noStates(VacationRequestStatesRetriever::failedStates()),
            )
            ->pluck("date");

        return new JsonResponse([
            ...$holidays->map(fn(Carbon $date): string => $date->toDateString()),
            ...$vacationDays->map(fn(Carbon $date): string => $date->toDateString()),
        ]);
    }
}
