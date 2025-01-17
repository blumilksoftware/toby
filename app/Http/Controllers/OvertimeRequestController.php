<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Actions\OvertimeRequest\AcceptAsTechnicalAction;
use Toby\Actions\OvertimeRequest\CancelAction;
use Toby\Actions\OvertimeRequest\CreateAction;
use Toby\Actions\OvertimeRequest\RejectAction;
use Toby\Actions\OvertimeRequest\SettleAction;
use Toby\Domain\OvertimeRequestStatesRetriever;
use Toby\Enums\SettlementType;
use Toby\Http\Requests\OvertimeRequestRequest;
use Toby\Http\Resources\OvertimeRequestActivityResource;
use Toby\Http\Resources\OvertimeRequestResource;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;

class OvertimeRequestController extends Controller
{
    public function index(Request $request): RedirectResponse|Response
    {
        if ($request->user()->can("listAllOvertimeRequests")) {
            return redirect()->route("overtime.requests.indexForApprovers");
        }

        $user = $request->user();
        $this->authorize("canUseOvertimeRequestFunctionality", $user);

        $status = $request->get("status", "all");
        $year = $request->integer("year", Carbon::now()->year);

        $overtimeRequests = $user
            ->overtimeRequests()
            ->whereYear("from", $year)
            ->with(["user.permissions", "user.profile"])
            ->latest()
            ->states(OvertimeRequestStatesRetriever::filterByStatusGroup($status, $request->user()))
            ->paginate();

        $pending = $user
            ->overtimeRequests()
            ->whereYear("from", $year)
            ->states(OvertimeRequestStatesRetriever::pendingStates())
            ->cache(key: "overtime:{$user->id}")
            ->count();

        $success = $user
            ->overtimeRequests()
            ->whereYear("from", $year)
            ->states(OvertimeRequestStatesRetriever::successStates())
            ->cache(key: "overtime:{$user->id}")
            ->count();

        $failed = $user
            ->overtimeRequests()
            ->whereYear("from", $year)
            ->states(OvertimeRequestStatesRetriever::failedStates())
            ->cache(key: "overtime:{$user->id}")
            ->count();

        $settled = $user
            ->overtimeRequests()
            ->whereYear("from", $year)
            ->states(OvertimeRequestStatesRetriever::settledStates())
            ->cache(key: "overtime:{$user->id}")
            ->count();

        return inertia("OvertimeRequest/Index", [
            "requests" => OvertimeRequestResource::collection($overtimeRequests),
            "stats" => [
                "all" => $pending + $success + $failed + $settled,
                "pending" => $pending,
                "success" => $success,
                "failed" => $failed,
                "settled" => $settled,
            ],
            "filters" => [
                "status" => $status,
                "year" => $year,
            ],
        ]);
    }

    public function indexForApprovers(
        Request $request,
    ): RedirectResponse|Response {
        if ($request->user()->cannot("listAllOvertimeRequests")) {
            abort(403);
        }

        $status = $request->get("status", "all");
        $user = $request->get("user");
        $year = $request->get("year");

        $authUser = $request->user();
        $withTrashedUsers = $authUser->canSeeInactiveUsers();

        $overtimeRequests = OvertimeRequest::query()
            ->with(["user.permissions", "user.profile"])
            ->whereRelation("user", fn(Builder $query): Builder => $query->withTrashed($withTrashedUsers))
            ->when($user !== null, fn(Builder $query): Builder => $query->where("user_id", $user))
            ->when($year !== null, fn(Builder $query): Builder => $query->whereYear("from", $year))
            ->states(OvertimeRequestStatesRetriever::filterByStatusGroup($status, $authUser))
            ->latest()
            ->paginate();

        $users = User::query()
            ->withTrashed($withTrashedUsers)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("OvertimeRequest/IndexForApprovers", [
            "requests" => OvertimeRequestResource::collection($overtimeRequests),
            "users" => SimpleUserResource::collection($users),
            "filters" => [
                "status" => $status,
                "user" => (int)$user,
                "year" => $year === null ? $year : (int)$year,
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(OvertimeRequest $overtimeRequest): Response
    {
        $this->authorize("show", $overtimeRequest);

        $overtimeRequest->load(["user.permissions", "user.profile", "activities.user.profile"]);

        return inertia("OvertimeRequest/Show", [
            "request" => new OvertimeRequestResource($overtimeRequest),
            "activities" => OvertimeRequestActivityResource::collection($overtimeRequest->activities),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize("canUseOvertimeRequestFunctionality", $request->user());

        return inertia("OvertimeRequest/Create", [
            "settlementTypes" => SettlementType::casesToSelect(),
            "overtimeFromDate" => $request->get("from_date"),
        ]);
    }

    public function store(OvertimeRequestRequest $request, CreateAction $createAction): RedirectResponse
    {
        $this->authorize("canUseOvertimeRequestFunctionality", $request->user());

        $overtimeRequest = $createAction->execute($request->getData(), $request->user());

        return redirect()
            ->route("overtime.requests.show", $overtimeRequest)
            ->with("success", __("Request created."));
    }

    /**
     * @throws AuthorizationException
     */
    public function reject(
        Request $request,
        OvertimeRequest $overtimeRequest,
        RejectAction $rejectAction,
    ): RedirectResponse {
        $this->authorize("reject", $overtimeRequest);

        $rejectAction->execute($overtimeRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Request rejected."));
    }

    /**
     * @throws AuthorizationException
     */
    public function cancel(
        Request $request,
        OvertimeRequest $overtimeRequest,
        CancelAction $cancelAction,
    ): RedirectResponse {
        $this->authorize("cancel", $overtimeRequest);

        $cancelAction->execute($overtimeRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Request cancelled."));
    }

    /**
     * @throws AuthorizationException
     */
    public function acceptAsTechnical(
        Request $request,
        OvertimeRequest $overtimeRequest,
        AcceptAsTechnicalAction $acceptAsTechnicalAction,
    ): RedirectResponse {
        $this->authorize("acceptAsTechApprover", $overtimeRequest);

        $acceptAsTechnicalAction->execute($overtimeRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Request accepted."));
    }

    /**
     * @throws AuthorizationException
     */
    public function settle(
        Request $request,
        OvertimeRequest $overtimeRequest,
        SettleAction $settleAction,
    ): RedirectResponse {
        $this->authorize("settle", $overtimeRequest);

        $settleAction->execute($overtimeRequest, $request->user());

        return redirect()->back()
            ->with("success", __("Overtime settled."));
    }
}
