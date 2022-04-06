<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\Vacation;
use Toby\Infrastructure\Http\Resources\SimpleVacationRequestResource;

class AnnualSummaryController extends Controller
{
    public function __invoke(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response {
        $yearPeriod = $yearPeriodRetriever->selected();

        $startDate = Carbon::createFromDate($yearPeriod->year)->startOfYear()->startOfWeek();
        $endDate = Carbon::createFromDate($yearPeriod->year)->endOfYear()->endOfWeek();

        $holidays = $yearPeriod->holidays()
            ->whereBetween("date", [$startDate, $endDate])
            ->get();

        $vacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBetween("date", [$startDate, $endDate])
            ->approved()
            ->get();

        $pendingVacations = $request->user()
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBetween("date", [$startDate, $endDate])
            ->pending()
            ->get();

        return inertia("AnnualSummary", [
            "holidays" => $holidays->mapWithKeys(fn(Holiday $holiday) => [$holiday->date->toDateString() => $holiday->name]),
            "vacations" => $vacations->mapWithKeys(fn(Vacation $vacation) => [$vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest)]),
            "pendingVacations" => $pendingVacations->mapWithKeys(fn(Vacation $vacation) => [$vacation->date->toDateString() => new SimpleVacationRequestResource($vacation->vacationRequest)]),
        ]);
    }
}
