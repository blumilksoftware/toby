<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Http\Resources\VacationLimitResource;
use Toby\Http\Resources\YearPeriodResource;
use Toby\Models\YearPeriod;

class VacationDaysController extends Controller
{
    public function edit(Request $request): Response
    {
        $year = $request->query("year", Carbon::now()->year);

        /** @var YearPeriod $yearPeriod */
        $yearPeriod = YearPeriod::query()->where("year", $year)->firstOrFail();
        $previousYearPeriod = YearPeriod::query()->where("year", $year - 1)->first();
        $nextYearPeriod = YearPeriod::query()->where("year", $year + 1)->first();


        return inertia("VacationDays", [
            "vacationLimits" => VacationLimitResource::collection($yearPeriod->vacationLimits()->with("user")->get()),
            "yearPeriods" => [
                "prev" => $previousYearPeriod ? new YearPeriodResource($previousYearPeriod) : null,
                "current" => new YearPeriodResource($yearPeriod),
                "next" => $nextYearPeriod ? new YearPeriodResource($nextYearPeriod) : null,
            ],
        ]);
    }

    public function update(Request $request)
    {
        dump($request->get("items"));
    }
}
