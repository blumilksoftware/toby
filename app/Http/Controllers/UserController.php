<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Enums\FormOfEmployment;
use Toby\Http\Requests\UserRequest;
use Toby\Http\Resources\UserFormDataResource;
use Toby\Http\Resources\UserResource;
use Toby\Models\User;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->withTrashed()
            ->search($request->query("search"))
            ->latest()
            ->paginate()
            ->withQueryString();

        return inertia("Users/Index", [
            "users" => UserResource::collection($users),
            "filters" => $request->only("search"),
        ]);
    }

    public function create(): Response
    {
        return inertia("Users/Create", [
            "employmentForms" => FormOfEmployment::casesToSelect(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::query()->create($request->data());

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been created"));
    }

    public function edit(User $user): Response
    {
        return inertia("Users/Edit", [
            "user" => new UserFormDataResource($user),
            "employmentForms" => FormOfEmployment::casesToSelect(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->data());

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been updated"));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been deleted"));
    }

    public function restore(User $user): RedirectResponse
    {
        $user->restore();

        return redirect()
            ->route("users.index")
            ->with("success", __("User has been restored"));
    }
}
