<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\Actions\CreateUserAction;
use Toby\Domain\Actions\UpdateUserAction;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\UserRequest;
use Toby\Infrastructure\Http\Resources\UserFormDataResource;
use Toby\Infrastructure\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $this->authorize("manageUsers");

        $searchTest = $request->query("search");
        $status = $request->query("status", "active");

        $users = User::query()
            ->search($searchTest)
            ->status($status)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->paginate()
            ->withQueryString();

        return inertia("Users/Index", [
            "users" => UserResource::collection($users),
            "filters" => [
                "search" => $searchTest,
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
    public function store(UserRequest $request, CreateUserAction $createUserAction): RedirectResponse
    {
        $this->authorize("manageUsers");

        $createUserAction->execute($request->userData(), $request->profileData());

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
    public function update(UserRequest $request, UpdateUserAction $updateUserAction, User $user): RedirectResponse
    {
        $this->authorize("manageUsers");

        $updateUserAction->execute($user, $request->userData(), $request->profileData());

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
            ->with("success", __("User deleted."));
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
