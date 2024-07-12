<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Actions\CreateUserAction;
use Toby\Actions\SyncUserPermissionsWithRoleAction;
use Toby\Actions\UpdateUserAction;
use Toby\Enums\EmploymentForm;
use Toby\Enums\Role;
use Toby\Http\Requests\UserRequest;
use Toby\Http\Resources\UserFormDataResource;
use Toby\Http\Resources\UserResource;
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
            "user" => new UserFormDataResource($user),
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
            ->route("users.index")
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
}
