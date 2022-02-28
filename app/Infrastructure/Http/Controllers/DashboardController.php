<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Resources\AbsenceResource;
use Toby\Infrastructure\Http\Resources\HolidayResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;

class DashboardController extends Controller
{
    public function __construct(
        protected VacationTypeConfigRetriever $configRetriever,
        protected YearPeriodRetriever $yearPeriodRetriever,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $absences = Vacation::query()
            ->with(["user", "vacationRequest"])
            ->whereDate("date", Carbon::now())
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query->states(VacationRequestState::successStates()),
            )
            ->get();

        $vacationRequests = VacationRequest::query()
            ->latest("updated_at")
            ->limit(3)
            ->get();

        $holidays = Holiday::query()
            ->whereDate("date", ">=", Carbon::now())
            ->latest()
            ->limit(3)
            ->get();

        $limit = $request->user()
            ->vacationLimits()
            ->where("year_period_id", $this->yearPeriodRetriever->current()->id)
            ->first()
            ->days ?? 0;

        $used = $request->user()
            ->vacations()
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestState::successStates()),
            )
            ->count();

        $pending = $request->user()
            ->vacations()
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query
                    ->whereIn("type", $this->getLimitableVacationTypes())
                    ->states(VacationRequestState::pendingStates()),
            )
            ->count();

        $other = $request->user()
            ->vacations()
            ->whereRelation(
                "vacationRequest",
                fn(Builder $query) => $query
                    ->whereIn("type", $this->getNotLimitableVacationTypes())
                    ->states(VacationRequestState::successStates()),
            )
            ->count();

        return inertia("Dashboard", [
            "absences" => AbsenceResource::collection($absences),
            "vacationRequests" => VacationRequestResource::collection($vacationRequests),
            "holidays" => HolidayResource::collection($holidays),
            "stats" => [
                "limit" => $limit,
                "remaining" => $limit - $used - $pending,
                "used" => $used,
                "pending" => $pending,
                "other" => $other,
            ],
        ]);
    }

    protected function getLimitableVacationTypes(): Collection
    {
        $types = new Collection(VacationType::cases());

        return $types->filter(fn(VacationType $type) => $this->configRetriever->hasLimit($type));
    }

    protected function getNotLimitableVacationTypes(): Collection
    {
        $types = new Collection(VacationType::cases());

        return $types->filter(fn(VacationType $type) => !$this->configRetriever->hasLimit($type));
    }
}
