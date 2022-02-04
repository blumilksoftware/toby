<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationRequestStateManager;
use Toby\Domain\Validation\VacationRequestValidator;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Requests\VacationRequestRequest;
use Toby\Infrastructure\Http\Resources\VacationRequestActivityResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;

class VacationRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->get("status", "all");

        $vacationRequests = $request->user()
            ->vacationRequests()
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
    ): RedirectResponse {
        /** @var VacationRequest $vacationRequest */
        $vacationRequest = $request->user()->vacationRequests()->make($request->data());

        $vacationRequestValidator->validate($vacationRequest);

        $vacationRequest->save();
        $stateManager->markAsCreated($vacationRequest);

        return redirect()
            ->route("vacation.requests.index");
    }

    public function reject(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->reject($vacationRequest);

        return redirect()->back();
    }

    public function cancel(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->cancel($vacationRequest);

        return redirect()->back();
    }

    public function acceptAsTechnical(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->acceptAsTechnical($vacationRequest);

        return redirect()->back();
    }

    public function acceptAsAdministrative(
        VacationRequest $vacationRequest,
        VacationRequestStateManager $stateManager,
    ): RedirectResponse {
        $stateManager->acceptAsAdministrative($vacationRequest);

        return redirect()->back();
    }
}
