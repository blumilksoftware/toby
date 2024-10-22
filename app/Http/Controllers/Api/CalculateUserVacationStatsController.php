<?php

declare(strict_types=1);

namespace Toby\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Http\Controllers\Controller;
use Toby\Http\Requests\Api\CalculateVacationStatsRequest;
use Toby\Models\User;

class CalculateUserVacationStatsController extends Controller
{
    public function __invoke(
        CalculateVacationStatsRequest $request,
        UserVacationStatsRetriever $vacationStatsRetriever,
    ): JsonResponse {
        /** @var User $user */
        $user = User::query()->find($request->get("user"));
        $year = $request->getYear();

        $limit = $vacationStatsRetriever->getVacationDaysLimit($user, $year);
        $used = $vacationStatsRetriever->getUsedVacationDays($user, $year);
        $pending = $vacationStatsRetriever->getPendingVacationDays($user, $year);
        $other = $vacationStatsRetriever->getOtherApprovedVacationDays($user, $year);
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
