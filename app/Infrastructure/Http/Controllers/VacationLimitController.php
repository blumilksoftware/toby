<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Http\Requests\VacationLimitRequest;
use Toby\Infrastructure\Http\Resources\UserResource;

class VacationLimitController extends Controller
{
    public function edit(YearPeriodRetriever $yearPeriodRetriever, UserVacationStatsRetriever $statsRetriever): Response
    {
        $yearPeriod = $yearPeriodRetriever->selected();
        $previousYearPeriod = YearPeriod::findByYear($yearPeriod->year - 1);

        $limits = $yearPeriod
            ->vacationLimits()
            ->with("user")
            ->orderByUserField("last_name")
            ->orderByUserField("first_name")
            ->get();

        $limitsResource = $limits->map(fn(VacationLimit $limit) => [
            "id" => $limit->id,
            "user" => new UserResource($limit->user),
            "hasVacation" => $limit->hasVacation(),
            "days" => $limit->days,
            "remainingLastYear" => $previousYearPeriod
                ? $statsRetriever->getRemainingVacationDays($limit->user, $previousYearPeriod)
                : 0,
        ]);

        return inertia("VacationLimits", [
            "limits" => $limitsResource,
        ]);
    }

    public function update(VacationLimitRequest $request): RedirectResponse
    {
        $data = $request->data();

        foreach ($request->vacationLimits() as $limit) {
            $limit->update($data[$limit->id]);
        }

        return redirect()
            ->back()
            ->with("success", __("Vacation limits have been updated."));
    }
}
