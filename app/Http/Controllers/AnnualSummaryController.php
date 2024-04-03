<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Resources\SimpleVacationRequestResource;
use Toby\Models\Holiday;
use Toby\Models\Vacation;

class AnnualSummaryController extends Controller
{
    public function __invoke(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response
    {
        $yearPeriod = $yearPeriodRetriever->selected();

        $holidays = $yearPeriod->holidays()
            ->get();

        $vacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->approved()
            ->get();

        $pendingVacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->pending()
            ->get();

        return inertia("AnnualSummary", [
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
