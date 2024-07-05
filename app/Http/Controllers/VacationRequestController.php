<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as LaravelResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Toby\Actions\VacationRequest\AcceptAsAdministrativeAction;
use Toby\Actions\VacationRequest\AcceptAsTechnicalAction;
use Toby\Actions\VacationRequest\CancelAction;
use Toby\Actions\VacationRequest\CreateAction;
use Toby\Actions\VacationRequest\RejectAction;
use Toby\Domain\UserVacationStatsRetriever;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Domain\VacationTypeConfigRetriever;
use Toby\Enums\VacationType;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Requests\VacationRequestRequest;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Http\Resources\SimpleVacationRequestResource;
use Toby\Http\Resources\VacationRequestActivityResource;
use Toby\Http\Resources\VacationRequestResource;
use Toby\Models\Holiday;
use Toby\Models\User;
use Toby\Models\Vacation;
use Toby\Models\VacationRequest;

class VacationRequestController extends Controller
{
    public function index(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user->can("listAllRequests")) {
            return redirect()->route("vacation.requests.indexForApprovers");
        }

        $status = $request->get("status", "all");
        $withoutRemote = $request->boolean("withoutRemote", default: false);

        $vacationRequests = $user
            ->vacationRequests()
            ->with(["vacations.user.profile", "user.permissions", "user.profile"])
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->latest()
            ->states(VacationRequestStatesRetriever::filterByStatusGroup($status, $user))
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
            ->paginate();

        $pending = $user
            ->vacationRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(VacationRequestStatesRetriever::pendingStates())
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
            ->cache(key: "vacations{$user->id}")
            ->count();

        $success = $user
            ->vacationRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(VacationRequestStatesRetriever::successStates())
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
            ->cache(key: "vacations{$user->id}")
            ->count();

        $failed = $user
            ->vacationRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(VacationRequestStatesRetriever::failedStates())
            ->when($withoutRemote, fn(Builder $query): Builder => $query->excludeType(VacationType::RemoteWork))
            ->cache(key: "vacations{$user->id}")
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
        if ($request->user()->cannot("listAllRequests")) {
            return redirect()->route("vacation.requests.index");
        }

        $yearPeriod = $yearPeriodRetriever->selected();
        $status = $request->get("status", "all");
        $user = $request->get("user");
        $type = $request->get("type");
        $withTrashedUsers = $request->boolean("withTrashedUsers") ?? false;

        $vacationRequests = VacationRequest::query()
            ->with(["vacations.user.profile", "user.permissions", "user.profile"])
            ->whereBelongsTo($yearPeriod)
            ->whereRelation("user", fn($query) => $query->withTrashed($withTrashedUsers))
            ->when($user !== null, fn(Builder $query): Builder => $query->where("user_id", $user))
            ->when($type !== null, fn(Builder $query): Builder => $query->where("type", $type))
            ->states(VacationRequestStatesRetriever::filterByStatusGroup($status, $request->user()))
            ->latest()
            ->paginate();

        $users = User::query()
            ->withTrashed($withTrashedUsers)
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
                "withTrashedUsers" => $withTrashedUsers,
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(
        VacationRequest $vacationRequest,
        UserVacationStatsRetriever $statsRetriever,
        YearPeriodRetriever $yearPeriodRetriever,
    ): Response {
        $this->authorize("show", $vacationRequest);

        $vacationRequest->load(["vacations.user.profile", "user.permissions", "user.profile", "activities.user.profile"]);
        $limit = $statsRetriever->getVacationDaysLimit($vacationRequest->user, $vacationRequest->yearPeriod);
        $used = $statsRetriever->getUsedVacationDays($vacationRequest->user, $vacationRequest->yearPeriod);
        $pending = $statsRetriever->getPendingVacationDays($vacationRequest->user, $vacationRequest->yearPeriod);
        $remaining = $limit - $used - $pending;

        $yearPeriod = $yearPeriodRetriever->selected();
        $requestFromDateMonth = $vacationRequest->from->month;
        $requestToDateMonth = $vacationRequest->to->month;

        $holidays = $yearPeriod->holidays()
            ->get();

        $user = $vacationRequest->user;

        $vacations = $user
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->approved()
            ->get();

        $pendingVacations = $user
            ->vacations()
            ->with("vacationRequest.vacations")
            ->whereBelongsTo($yearPeriod)
            ->pending()
            ->get();

        return inertia("VacationRequest/Show", [
            "request" => new VacationRequestResource($vacationRequest),
            "activities" => VacationRequestActivityResource::collection($vacationRequest->activities),
            "stats" => [
                "limit" => $limit,
                "used" => $used,
                "pending" => $pending,
                "remaining" => $remaining,
            ],
            "handyCalendarData" => [
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
                "startMonth" => $requestFromDateMonth > 1 ? --$requestFromDateMonth : 1,
                "endMonth" => $requestToDateMonth < 12 ? ++$requestToDateMonth : 12,
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
            abort(SymfonyResponse::HTTP_NOT_FOUND);
        }

        $this->authorize("show", $vacationRequest);

        $pdf = Pdf::loadView("pdf.vacation-request", [
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
            $this->authorize("createRequestsOnBehalfOfEmployee");
        }

        if ($request->wantsSkipFlow()) {
            $this->authorize("skipRequestFlow");
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
