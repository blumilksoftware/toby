<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\YearPeriod;
use Toby\Infrastructure\Http\Requests\TakeDaysFromLastYearRequest;
use Toby\Infrastructure\Http\Requests\VacationLimitRequest;
use Toby\Infrastructure\Http\Resources\UserResource;

class VacationLimitController extends Controller
{
    public function edit(YearPeriodRetriever $yearPeriodRetriever, UserVacationStatsRetriever $statsRetriever): Response
    {
        $this->authorize("manageVacationLimits");

        $yearPeriod = $yearPeriodRetriever->selected();
        $previousYearPeriod = YearPeriod::findByYear($yearPeriod->year - 1);

        $limits = $yearPeriod
            ->vacationLimits()
            ->with("user.profile")
            ->has("user")
            ->get()
            ->sortBy(
                fn(
                    VacationLimit $limit,
                ): string => "{$limit->user->profile->last_name} {$limit->user->profile->first_name}",
            )
            ->values();

        $limitsResource = $limits->map(fn(VacationLimit $limit): array => [
            "id" => $limit->id,
            "user" => new UserResource($limit->user),
            "hasVacation" => $limit->hasVacation(),
            "days" => $limit->days,
            "limit" => $limit->limit,
            "fromPreviousYear" => $limit->from_previous_year,
            "toNextYear" => $limit->to_next_year,
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
        $this->authorize("manageVacationLimits");

        $data = $request->data();

        foreach ($request->vacationLimits() as $limit) {
            $limit->update($data[$limit->id]);
        }

        return redirect()
            ->back()
            ->with("success", __("Vacation limits updated."));
    }

    public function takeFromLastYear(
        TakeDaysFromLastYearRequest $request,
        VacationLimit $limit,
    ): RedirectResponse {
        $this->authorize("manageVacationLimits");

        $days = $request->getDays();
        $yearPeriod = $limit->yearPeriod;
        $previousYearPeriod = YearPeriod::findByYear($yearPeriod->year - 1);

        if ($previousYearPeriod) {
            $previousLimit = $limit->user->vacationLimits()->whereBelongsTo($previousYearPeriod)->first();

            $previousLimit->update([
                "to_next_year" => $days,
            ]);
        }

        $limit->update([
            "from_previous_year" => $days,
        ]);

        return redirect()
            ->back()
            ->with("success", __("Days moved."));
    }
}
