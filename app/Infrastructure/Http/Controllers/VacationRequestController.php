<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as LaravelResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Toby\Domain\Actions\VacationRequest\AcceptAsAdministrativeAction;
use Toby\Domain\Actions\VacationRequest\AcceptAsTechnicalAction;
use Toby\Domain\Actions\VacationRequest\CancelAction;
use Toby\Domain\Actions\VacationRequest\CreateAction;
use Toby\Domain\Actions\VacationRequest\RejectAction;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\States\VacationRequest\AcceptedByAdministrative;
use Toby\Domain\States\VacationRequest\AcceptedByTechnical;
use Toby\Domain\States\VacationRequest\Cancelled;
use Toby\Domain\States\VacationRequest\Rejected;
use Toby\Domain\VacationRequestStatesRetriever;
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
            ->states(VacationRequestStatesRetriever::filterByStatusGroup($status, $request->user()))
            ->paginate();

        $pending = $request->user()
            ->vacationRequests()
            ->where("year_period_id", $yearPeriodRetriever->selected()->id)
            ->states(VacationRequestStatesRetriever::pendingStates())
            ->count();

        $success = $request->user()
            ->vacationRequests()
            ->where("year_period_id", $yearPeriodRetriever->selected()->id)
            ->states(VacationRequestStatesRetriever::successStates())
            ->count();

        $failed = $request->user()
            ->vacationRequests()
            ->where("year_period_id", $yearPeriodRetriever->selected()->id)
            ->states(VacationRequestStatesRetriever::failedStates())
            ->count();

        return inertia("VacationRequest/Index", [
            "requests" => VacationRequestResource::collection($vacationRequests),
            "stats" => [
                "all" => $pending + $success + $failed,
                "pending" => $pending,
                "success" => $success,
                "failed" => $failed,
            ],
            "filters" => [
                "status" => $status,
            ],
        ]);
    }

    public function indexForApprovers(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
    ): RedirectResponse|Response {
        if ($request->user()->cannot("listAll", VacationRequest::class)) {
            return redirect()->route("vacation.requests.index");
        }

        $yearPeriod = $yearPeriodRetriever->selected();
        $status = $request->get("status");
        $user = $request->get("user");

        $vacationRequests = VacationRequest::query()
            ->with(["user", "vacations"])
            ->where("year_period_id", $yearPeriod->id)
            ->when($user !== null, fn(Builder $query) => $query->where("user_id", $user))
            ->when(
                $status !== null,
                fn(Builder $query) => $query->states(
                    VacationRequestStatesRetriever::filterByStatusGroup($status, $request->user()),
                ),
            )
            ->latest()
            ->paginate();

        $users = User::query()
            ->withVacationLimitIn($yearPeriod)
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        return inertia("VacationRequest/IndexForApprovers", [
            "requests" => VacationRequestResource::collection($vacationRequests),
            "users" => UserResource::collection($users),
            "filters" => [
                "status" => $status,
                "user" => (int)$user,
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Request $request, VacationRequest $vacationRequest): Response
    {
        $this->authorize("show", $vacationRequest);
        $user = $request->user();

        return inertia("VacationRequest/Show", [
            "request" => new VacationRequestResource($vacationRequest),
            "activities" => VacationRequestActivityResource::collection($vacationRequest->activities),
            "can" => [
                "acceptAsTechnical" => $vacationRequest->state->canTransitionTo(AcceptedByTechnical::class)
                    && $user->can("acceptAsTechApprover", $vacationRequest),
                "acceptAsAdministrative" => $vacationRequest->state->canTransitionTo(AcceptedByAdministrative::class)
                    && $user->can("acceptAsAdminApprover", $vacationRequest),
                "reject" => $vacationRequest->state->canTransitionTo(Rejected::class)
                    && $user->can("reject", $vacationRequest),
                "cancel" => $vacationRequest->state->canTransitionTo(Cancelled::class)
                    && $user->can("cancel", $vacationRequest),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function download(VacationRequest $vacationRequest): LaravelResponse
    {
        $this->authorize("show", $vacationRequest);

        $pdf = PDF::loadView("pdf.vacation-request", [
            "vacationRequest" => $vacationRequest,
        ]);

        return $pdf->stream();
    }

    public function create(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response
    {
        $yearPeriod = $yearPeriodRetriever->selected();

        $users = User::query()
            ->withVacationLimitIn($yearPeriod)
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        return inertia("VacationRequest/Create", [
            "vacationTypes" => VacationType::casesToSelect(),
            "users" => UserResource::collection($users),
            "can" => [
                "createOnBehalfOfEmployee" => $request->user()->can("createOnBehalfOfEmployee", VacationRequest::class),
                "skipFlow" => $request->user()->can("skipFlow", VacationRequest::class),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(VacationRequestRequest $request, CreateAction $createAction): RedirectResponse
    {
        if ($request->createsOnBehalfOfEmployee()) {
            $this->authorize("createOnBehalfOfEmployee", VacationRequest::class);
        }

        if ($request->wantsSkipFlow()) {
            $this->authorize("skipFlow", VacationRequest::class);
        }

        $vacationRequest = $createAction->execute($request->data(), $request->user());

        return redirect()
            ->route("vacation.requests.show", $vacationRequest)
            ->with("success", __("Vacation request has been created."));
    }

    /**
     * @throws AuthorizationException
     */
    public function reject(
        Request $request,
        VacationRequest $vacationRequest,
        RejectAction $rejectAction,
    ): RedirectResponse {
        $this->authorize("reject", $vacationRequest);

        $rejectAction->execute($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been rejected."));
    }

    /**
     * @throws AuthorizationException
     */
    public function cancel(
        Request $request,
        VacationRequest $vacationRequest,
        CancelAction $cancelAction,
    ): RedirectResponse {
        $this->authorize("cancel", $vacationRequest);

        $cancelAction->execute($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been cancelled."));
    }

    /**
     * @throws AuthorizationException
     */
    public function acceptAsTechnical(
        Request $request,
        VacationRequest $vacationRequest,
        AcceptAsTechnicalAction $acceptAsTechnicalAction,
    ): RedirectResponse {
        $this->authorize("acceptAsTechApprover", $vacationRequest);

        $acceptAsTechnicalAction->execute($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been accepted."));
    }

    /**
     * @throws AuthorizationException
     */
    public function acceptAsAdministrative(
        Request $request,
        VacationRequest $vacationRequest,
        AcceptAsAdministrativeAction $acceptAsAdministrativeAction,
    ): RedirectResponse {
        $this->authorize("acceptAsAdminApprover", $vacationRequest);

        $acceptAsAdministrativeAction->execute($vacationRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Vacation request has been accepted."));
    }
}
