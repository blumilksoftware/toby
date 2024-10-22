<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Http\Resources\SimpleVacationRequestResource;
use Toby\Models\Holiday;
use Toby\Models\Vacation;

class AnnualSummaryController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $year = $request->integer("year", Carbon::now()->year);

        $holidays = Holiday::query()
            ->whereYear("date", $year)
            ->get();

        $vacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereYear("date", $year)
            ->approved()
            ->get();

        $pendingVacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereYear("date", $year)
            ->pending()
            ->get();

        return inertia("AnnualSummary", [
            "year" => $year,
            "holidays" => $holidays->mapWithKeys(
                fn(Holiday $holiday): array => [$holiday->date->toDateString() => $holiday->name],
            ),
            "vacations" => $vacations->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest),
                ],
            ),
            "pendingVacations" => $pendingVacations->mapWithKeys(
                fn(Vacation $vacation): array => [
                    $vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest),
                ],
            ),
        ]);
    }
}
