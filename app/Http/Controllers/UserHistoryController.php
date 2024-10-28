<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Enums\EmploymentForm;
use Toby\Enums\UserHistoryType;
use Toby\Http\Requests\UserHistoryRequest;
use Toby\Http\Resources\UserHistoryResource;
use Toby\Http\Resources\UserResource;
use Toby\Models\User;
use Toby\Models\UserHistory;

class UserHistoryController extends Controller
{
    public function index(User $user): Response
    {
        $this->authorize("manageUsers");

        $history = $user->histories()
            ->orderBy("to", "desc")
            ->get();

        return inertia("UserHistory/Index", [
            "history" => UserHistoryResource::collection($history),
            "user" => new UserResource($user),
        ]);
    }

    public function create(User $user): Response
    {
        $this->authorize("manageUsers");

        return inertia("UserHistory/Create", [
            "types" => UserHistoryType::casesToSelect(),
            "employmentForms" => EmploymentForm::casesToSelect(),
            "userId" => $user->id,
        ]);
    }

    public function store(UserHistoryRequest $request, User $user): RedirectResponse
    {
        $this->authorize("manageUsers");

        $user->histories()->create($request->data());

        return redirect()
            ->route("users.history", $user)
            ->with("success", __("User history created."));
    }

    public function edit(UserHistory $history): Response
    {
        $this->authorize("manageUsers");

        return inertia("UserHistory/Edit", [
            "history" => $history,
            "types" => UserHistoryType::casesToSelect(),
            "employmentForms" => EmploymentForm::casesToSelect(),
        ]);
    }

    public function update(UserHistoryRequest $request, UserHistory $history): RedirectResponse
    {
        $this->authorize("manageUsers");

        $history->update($request->data());

        return redirect()
            ->route("users.history", $history->user_id)
            ->with("success", __("User history updated."));
    }

    public function destroy(UserHistory $history): RedirectResponse
    {
        $this->authorize("manageUsers");

        $history->delete();

        return redirect()
            ->route("users.history", $history->user_id)
            ->with("success", __("User history deleted."));
    }
}
