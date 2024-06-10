<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Actions\OvertimeRequest\AcceptAsTechnicalAction;
use Toby\Actions\OvertimeRequest\CancelAction;
use Toby\Actions\OvertimeRequest\CreateAction;
use Toby\Actions\OvertimeRequest\RejectAction;
use Toby\Actions\OvertimeRequest\SettleAction;
use Toby\Domain\OvertimeRequestStatesRetriever;
use Toby\Enums\SettlementType;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Http\Requests\OvertimeRequestRequest;
use Toby\Http\Resources\OvertimeRequestActivityResource;
use Toby\Http\Resources\OvertimeRequestResource;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;

class OvertimeRequestController extends Controller
{
    public function index(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response|RedirectResponse
    {
        if ($request->user()->can("listAllRequests")) {
            return redirect()->route("overtime.requests.indexForApprovers");
        }

        $this->authorize("canUseOvertimeRequestFunctionality", $request->user());

        $status = $request->get("status", "all");

        $overtimeRequests = $request->user()
            ->overtimeRequests()
            ->with(["user.permissions", "user.profile"])
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->latest()
            ->states(OvertimeRequestStatesRetriever::filterByStatusGroup($status, $request->user()))
            ->paginate();

        $pending = $request->user()
            ->overtimeRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(OvertimeRequestStatesRetriever::pendingStates())
            ->count();

        $success = $request->user()
            ->overtimeRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(OvertimeRequestStatesRetriever::successStates())
            ->count();

        $failed = $request->user()
            ->overtimeRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(OvertimeRequestStatesRetriever::failedStates())
            ->count();

        $settled = $request->user()
            ->overtimeRequests()
            ->whereBelongsTo($yearPeriodRetriever->selected())
            ->states(OvertimeRequestStatesRetriever::settledStates())
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
            ],
        ]);
    }

    public function indexForApprovers(
        Request $request,
        YearPeriodRetriever $yearPeriodRetriever,
    ): RedirectResponse|Response {
        if ($request->user()->cannot("listAllRequests")) {
            return redirect()->route("overtime.requests.index");
        }

        $yearPeriod = $yearPeriodRetriever->selected();
        $status = $request->get("status", "all");
        $user = $request->get("user");

        $overtimeRequests = OvertimeRequest::query()
            ->with(["user.permissions", "user.profile"])
            ->whereBelongsTo($yearPeriod)
            ->when($user !== null, fn(Builder $query): Builder => $query->where("user_id", $user))
            ->states(OvertimeRequestStatesRetriever::filterByStatusGroup($status, $request->user()))
            ->latest()
            ->paginate();

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("OvertimeRequest/IndexForApprovers", [
            "requests" => OvertimeRequestResource::collection($overtimeRequests),
            "users" => SimpleUserResource::collection($users),
            "filters" => [
                "status" => $status,
                "user" => (int)$user,
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
        $overtimeRequest = $createAction->execute($request->data(), $request->user());

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
