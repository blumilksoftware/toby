<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Controllers\Controller;
use Toby\Infrastructure\Http\Requests\Api\CalculateVacationStatsRequest;

class CalculateUserVacationStatsController extends Controller
{
    public function __invoke(
        CalculateVacationStatsRequest $request,
        UserVacationStatsRetriever $vacationStatsRetriever,
        YearPeriodRetriever $yearPeriodRetriever,
    ): JsonResponse {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));
        $yearPeriod = $yearPeriodRetriever->selected();

        $limit = $vacationStatsRetriever->getVacationDaysLimit($user, $yearPeriod);
        $used = $vacationStatsRetriever->getUsedVacationDays($user, $yearPeriod);
        $pending = $vacationStatsRetriever->getPendingVacationDays($user, $yearPeriod);
        $other = $vacationStatsRetriever->getOtherApprovedVacationDays($user, $yearPeriod);
        $remaining = $limit - $used - $pending;

        return new JsonResponse([
            "limit" => $limit,
            "remaining" => $remaining,
            "used" => $used,
            "pending" => $pending,
            "other" => $other,
        ]);
    }
}
