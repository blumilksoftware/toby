<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Enums\VacationType;
use Toby\Helpers\VacationRequestStateManager;
use Toby\Helpers\VacationRequestValidator;
use Toby\Http\Requests\VacationRequestRequest;
use Toby\Http\Resources\VacationRequestActivityResource;
use Toby\Http\Resources\VacationRequestResource;
use Toby\Models\VacationRequest;

class VacationRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $requests = $request->user()
            ->vacationRequests()
            ->paginate();

        return inertia("VacationRequest/Index", [
            "requests" => VacationRequestResource::collection($requests),
        ]);
    }

    public function show(Request $request, VacationRequest $vacationRequest): Response
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
