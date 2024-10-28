<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Response;
use Toby\Actions\CreateUserAction;
use Toby\Actions\SyncUserPermissionsWithRoleAction;
use Toby\Actions\UpdateUserAction;
use Toby\Domain\DashboardAggregator;
use Toby\Enums\EmploymentForm;
use Toby\Enums\Role;
use Toby\Http\Requests\UserRequest;
use Toby\Http\Resources\BirthdayResource;
use Toby\Http\Resources\EquipmentItemResource;
use Toby\Http\Resources\OvertimeRequestResource;
use Toby\Http\Resources\UserFormDataResource;
use Toby\Http\Resources\UserResource;
use Toby\Http\Resources\VacationRequestResource;
use Toby\Models\EquipmentItem;
use Toby\Models\User;

class UserController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize("manageUsers");

        $searchText = $request->query("search");
        $status = $request->query("status", "active");

        $users = User::query()
            ->with("histories")
            ->search($searchText)
            ->status($status)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->paginate()
            ->withQueryString();

        return inertia("Users/Index", [
            "users" => UserResource::collection($users),
            "filters" => [
                "search" => $searchText,
                "status" => $status,
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize("manageUsers");

        return inertia("Users/Create", [
            "employmentForms" => EmploymentForm::casesToSelect(),
            "roles" => Role::casesToSelect(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(
        UserRequest $request,
        CreateUserAction $createUserAction,
        SyncUserPermissionsWithRoleAction $syncUserPermissionsWithRoleAction,
    ): RedirectResponse {
        $this->authorize("manageUsers");

        $user = $createUserAction->execute($request->userData(), $request->profileData());
        $syncUserPermissionsWithRoleAction->execute($user);

        return redirect()
            ->route("users.index")
            ->with("success", __("User created."));
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(User $user): Response
    {
        $this->authorize("manageUsers");

        return inertia("Users/Edit", [
            "user" => new UserResource($user),
            "form" => new UserFormDataResource($user),
            "employmentForms" => EmploymentForm::casesToSelect(),
            "roles" => Role::casesToSelect(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(
        UserRequest $request,
        UpdateUserAction $updateUserAction,
        SyncUserPermissionsWithRoleAction $syncUserPermissionsWithRoleAction,
        User $user,
    ): RedirectResponse {
        $this->authorize("manageUsers");

        $shouldSyncPermissions = $request->input("role") !== $user->role->value;

        $updateUserAction->execute($user, $request->userData(), $request->profileData());

        if ($shouldSyncPermissions) {
            $syncUserPermissionsWithRoleAction->execute($user);
        }

        return redirect()
            ->back()
            ->with("success", __("User updated."));
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize("manageUsers");

        $user->delete();

        return back()
            ->with("success", __("User blocked."));
    }

    /**
     * @throws AuthorizationException
     */
    public function restore(User $user): RedirectResponse
    {
        $this->authorize("manageUsers");

        $user->restore();

        return back()
            ->with("success", __("User restored."));
    }

    public function show(
        User $user,
        DashboardAggregator $dashboardAggregator,
    ): Response {
        $this->authorize("manageUsers");

        $year = Carbon::now()->year;
        $equipment = EquipmentItem::query()
            ->with("assignee")
            ->where("assignee_id", $user->id)
            ->take(5)
            ->get();
        $vacationRequests = $user->vacationRequests()
            ->with(["user", "vacations", "vacations.user", "vacations.user.profile", "user.permissions", "user.profile"])
            ->whereYear("from", $year)
            ->latest("updated_at")
            ->limit(2)
            ->get();
        $overtimeRequests = $user->overtimeRequests()
            ->with(["user", "user.profile", "user.permissions"])
            ->whereYear("from", $year)
            ->latest("updated_at")
            ->limit(2)
            ->get();

        return inertia("Users/Show", [
            "user" => new UserResource($user),
            "vacationRequests" => VacationRequestResource::collection($vacationRequests),
            "overtimeRequests" => OvertimeRequestResource::collection($overtimeRequests),
            "benefits" => $dashboardAggregator->aggregateUserBenefits($user),
            "calendar" => $dashboardAggregator->aggregateCalendarData($user, $year),
            "stats" => $dashboardAggregator->aggregateStats($user, $year),
            "equipmentItems" => EquipmentItemResource::collection($equipment),
            "upcomingBirthday" => new BirthdayResource($user),
            "seniority" => $user->seniority(),
        ]);
    }
}
