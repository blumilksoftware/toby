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
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
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
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Infrastructure\Http\Requests\VacationRequestRequest;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;
use Toby\Infrastructure\Http\Resources\VacationRequestActivityResource;
use Toby\Infrastructure\Http\Resources\VacationRequestResource;

class VacationRequestController extends Controller
{
    public function index(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response|RedirectResponse
    {
        if ($request->user()->can("listAll", VacationRequest::class)) {
            return redirect()->route("vacation.requests.indexForApprovers");
        }

        $status = $request->get("status", "all");
        $withoutRemote = $request->boolean("withoutRemote", false);

        $vacationRequests = $request->user()
            ->vacationRequests()
            ->with("vacations")
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->latest()
            ->states(VacationRequestStatesRetriever::filterByStatusGroup($status, $request->user()))
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
            ->paginate();

        $pending = $request->user()
            ->vacationRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(VacationRequestStatesRetriever::pendingStates())
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
            ->count();

        $success = $request->user()
            ->vacationRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(VacationRequestStatesRetriever::successStates())
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
            ->count();

        $failed = $request->user()
            ->vacationRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(VacationRequestStatesRetriever::failedStates())
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
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
        $status = $request->get("status", "all");
        $user = $request->get("user");
        $type = $request->get("type");

        $vacationRequests = VacationRequest::query()
            ->with(["user", "vacations"])
            ->whereBelongsTo($yearPeriod)
            ->when($user !== null, fn(Builder $query): Builder => $query->where("user_id", $user))
            ->when($type !== null, fn(Builder $query): Builder => $query->where("type", $type))
            ->states(VacationRequestStatesRetriever::filterByStatusGroup($status, $request->user()))
            ->latest()
            ->paginate();

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("VacationRequest/IndexForApprovers", [
            "requests" => VacationRequestResource::collection($vacationRequests),
            "users" => SimpleUserResource::collection($users),
            "types" => VacationType::casesToSelect(),
            "filters" => [
                "status" => $status,
                "user" => (int)$user,
                "type" => $type,
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Request $request, VacationRequest $vacationRequest, UserVacationStatsRetriever $statsRetriever, YearPeriodRetriever $yearPeriodRetriever): Response
    {
        $this->authorize("show", $vacationRequest);
        $user = $request->user();

        $currentYearPeriod = $yearPeriodRetriever->selected();

        $limit = $statsRetriever->getVacationDaysLimit($vacationRequest->user, $vacationRequest->yearPeriod);
        $used = $statsRetriever->getUsedVacationDays($vacationRequest->user, $vacationRequest->yearPeriod);
        $pending = $statsRetriever->getPendingVacationDays($vacationRequest->user, $vacationRequest->yearPeriod);
        $remaining = $limit - $used - $pending;

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
            "stats" => [
                "limit" => $limit,
                "used" => $used,
                "pending" => $pending,
                "remaining" => $remaining,
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function download(
        VacationRequest $vacationRequest,
        VacationTypeConfigRetriever $configRetriever,
    ): LaravelResponse {
        if (!$configRetriever->isVacation($vacationRequest->type)) {
            return abort(SymfonyResponse::HTTP_NOT_FOUND);
        }

        $this->authorize("show", $vacationRequest);

        $pdf = PDF::loadView("pdf.vacation-request", [
            "vacationRequest" => $vacationRequest,
        ]);

        return $pdf->stream();
    }

    public function create(Request $request): Response
    {
        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("VacationRequest/Create", [
            "vacationTypes" => VacationType::casesToSelect(),
            "users" => SimpleUserResource::collection($users),
            "can" => [
                "createOnBehalfOfEmployee" => $request->user()->can("createOnBehalfOfEmployee", VacationRequest::class),
                "skipFlow" => $request->user()->can("skipFlow", VacationRequest::class),
            ],
            "vacationUserId" => (int)$request->get("user"),
            "vacationFromDate" => $request->get("from_date"),
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
            ->with("success", __("Request created."));
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
            ->with("success", __("Request rejected."));
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
            ->with("success", __("Request cancelled."));
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
            ->with("success", __("Request accepted."));
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
            ->with("success", __("Request accepted."));
    }
}
