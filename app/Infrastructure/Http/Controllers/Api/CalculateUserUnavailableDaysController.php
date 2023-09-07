<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Toby\Domain\UnavailableDaysRetriever;
use Toby\Domain\UserVacationStatsRetriever;
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
        UnavailableDaysRetriever $unavailableDaysRetriever,
    ): JsonResponse {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));
        $yearPeriod = $yearPeriodRetriever->selected();

        $unavailableDays = $unavailableDaysRetriever->getUnavailableDays(
            $user,
            $yearPeriod,
            $request->vacationType(),
        );

        return new JsonResponse($unavailableDays);
    }
}
