<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as LaravelResponse;
use Inertia\Response;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationDaysCalculator;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\Validation\VacationRequestValidator;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Requests\VacationRequestRequest;
use Toby\Infrastructure\Http\Resources\VacationRequestActivityResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;

class VacationRequestController extends Controller
{
    public function index(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response
    {
        $status = $request->get("status", "all");

        $vacationRequests = $request->user()
            ->vacationRequests()
            ->with("vacations")
            ->where("year_period_id", $yearPeriodRetriever->selected()->id)
            ->latest()
            ->states(VacationRequestState::filterByStatus($status))
            ->paginate();

        return inertia("VacationRequest/Index", [
            "requests" => VacationRequestResource::collection($vacationRequests),
            "filters" => [
                "status" => $status,
            ],
        ]);
    }

    public function show(VacationRequest $vacationRequest): Response
    {
        return inertia("VacationRequest/Show", [
            "request" => new VacationRequestResource($vacationRequest),
            "activities" => VacationRequestActivityResource::collection($vacationRequest->activities),
        ]);
    }

    public function download(VacationRequest $vacationRequest): LaravelResponse
    {
        $pdf = PDF::loadView("pdf.vacation-request", [
            "vacationRequest" => $vacationRequest,
        ]);

        return $pdf->stream();
    }

    public function create(): Response
    {
        return inertia("VacationRequest/Create", [
            "vacationTypes" => VacationType::casesToSelect(),
        ]);
    }

    public function store(
        VacationRequestRequest $request,
        VacationRequestValidator $vacationRequestValidator,
        VacationRequestStateManager $stateManager,
        VacationDaysCalculator $vacationDaysCalculator,
    ): RedirectResponse {
        /** @var VacationRequest $vacationRequest */
        $vacationRequest = $request->user()->vacationRequests()->make($request->data());
        $vacationRequestValidator->validate($vacationRequest);

        $vacationRequest->save();

        $days = $vacationDaysCalculator->calculateDays(
            $vacationRequest->yearPeriod,
            $vacationRequest->from,
            $vacationRequest->to,
        );

        foreach ($days as $day) {
            $vacationRequest->vacations()->create([
                "date" => $day,
                "user_id" => $vacationRequest->user->id,
                "year_period_id" => $vacationRequest->yearPeriod->id,
            ]);
        }

        $stateManager->markAsCreated($vacationRequest);

        return redirect()
            ->route("vacation.requests.show", $vacationRequest)
            ->with("success", __("Vacation request has been created."));
    }

    public function reject(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->reject($vacationRequest);

        return redirect()->back()
            ->with("success", __("Vacation request has been rejected."));
    }

    public function cancel(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->cancel($vacationRequest);

        return redirect()->back()
            ->with("success", __("Vacation request has been cancelled."));
    }

    public function acceptAsTechnical(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->acceptAsTechnical($vacationRequest);

        return redirect()->back()
            ->with("success", __("Vacation request has been accepted."));
    }

    public function acceptAsAdministrative(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->acceptAsAdministrative($vacationRequest);

        return redirect()->back()
            ->with("success", __("Vacation request has been accepted."));
    }
}
