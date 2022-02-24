<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as LaravelResponse;
use Inertia\Response;
use Toby\Domain\Enums\Role;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\States\VacationRequest\AcceptedByAdministrative;
use Toby\Domain\States\VacationRequest\AcceptedByTechnical;
use Toby\Domain\States\VacationRequest\Cancelled;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\VacationDaysCalculator;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\Validation\VacationRequestValidator;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Requests\VacationRequestRequest;
use Toby\Infrastructure\Http\Resources\UserResource;
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
            ->states(VacationRequestStatesRetriever::filterByStatus($status))
            ->paginate();

        return inertia("VacationRequest/Index", [
            "requests" => VacationRequestResource::collection($vacationRequests),
            "filters" => [
                "status" => $status,
            ],
        ]);
    }

    public function show(Request $request, VacationRequest $vacationRequest): Response
    {
        $user = $request->user();

        return inertia("VacationRequest/Show", [
            "request" => new VacationRequestResource($vacationRequest),
            "activities" => VacationRequestActivityResource::collection($vacationRequest->activities),
            "can" => [
                "acceptAsTechnical" => $vacationRequest->state->canTransitionTo(AcceptedByTechnical::class)
                    && $user === Role::TechnicalApprover,
                "acceptAsAdministrative" => $vacationRequest->state->canTransitionTo(AcceptedByAdministrative::class)
                    && $user === Role::AdministrativeApprover,
                "reject" => $vacationRequest->state->canTransitionTo(Rejected::class)
                    && in_array($user->role, [Role::TechnicalApprover, Role::AdministrativeApprover], true),
                "cancel" => $vacationRequest->state->canTransitionTo(Cancelled::class)
                    && $user === Role::AdministrativeApprover,
            ],
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
        $users = User::query()
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        return inertia("VacationRequest/Create", [
            "vacationTypes" => VacationType::casesToSelect(),
            "users" => UserResource::collection($users),
        ]);
    }

    public function store(
        VacationRequestRequest $request,
        VacationRequestValidator $vacationRequestValidator,
        VacationRequestStateManager $stateManager,
        VacationDaysCalculator $vacationDaysCalculator,
    ): RedirectResponse {
        /** @var VacationRequest $vacationRequest */
        $vacationRequest = $request->user()->createdVacationRequests()->make($request->data());
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

        $stateManager->markAsCreated($vacationRequest, $request->user());

        return redirect()
            ->route("vacation.requests.show", $vacationRequest)
            ->with("success", __("Vacation request has been created."));
    }

    public function reject(
        Request $request,
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->reject($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been rejected."));
    }

    public function cancel(
        Request $request,
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->cancel($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been cancelled."));
    }

    public function acceptAsTechnical(
        Request $request,
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->acceptAsTechnical($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been accepted."));
    }

    public function acceptAsAdministrative(
        Request $request,
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->acceptAsAdministrative($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been accepted."));
    }
}
