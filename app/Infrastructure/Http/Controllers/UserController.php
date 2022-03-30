<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\Actions\CreateUserAction;
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

        $users = User::query()
            ->withTrashed()
            ->search($request->query("search"))
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->paginate()
            ->withQueryString();

        return inertia("Users/Index", [
            "users" => UserResource::collection($users),
            "filters" => $request->only("search"),
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

        $createUserAction->execute($request->data());

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been created."));
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
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize("manageUsers");

        $user->update($request->data());

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been updated."));
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize("manageUsers");

        $user->delete();

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been deleted."));
    }

    /**
     * @throws AuthorizationException
     */
    public function restore(User $user): RedirectResponse
    {
        $this->authorize("manageUsers");

        $user->restore();

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been restored."));
    }
}
