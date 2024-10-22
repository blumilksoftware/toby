<?php

declare(strict_types=1);

namespace Toby\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Toby\Domain\UnavailableDaysRetriever;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Http\Controllers\Controller;
use Toby\Http\Requests\Api\CalculateUserUnavailableDaysRequest;
use Toby\Models\User;

class CalculateUserUnavailableDaysController extends Controller
{
    public function __invoke(
        CalculateUserUnavailableDaysRequest $request,
        UserVacationStatsRetriever $vacationStatsRetriever,
        UnavailableDaysRetriever $unavailableDaysRetriever,
    ): JsonResponse {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));
        $year = $request->getYear();

        $unavailableDays = $unavailableDaysRetriever->getUnavailableDays($user, $year, $request->vacationType())
            ->map(fn(Carbon $date): string => $date->toDateString())
            ->toArray();

        return new JsonResponse($unavailableDays);
    }
}
