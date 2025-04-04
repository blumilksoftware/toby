<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Toby\Actions\UpdateUserPermissionsAction;
use Toby\Http\Requests\PermissionRequest;
use Toby\Http\Resources\UserResource;
use Toby\Models\User;

class PermissionController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function show(User $user): Response
    {
        $this->authorize("managePermissions");

        $permissions = Permission::all()->pluck("name");

        return inertia("Permissions/Edit", [
            "user" => new UserResource($user),
            "permissions" => $permissions->mapWithKeys(
                fn($permission) => [$permission => $user->hasPermissionTo($permission)],
            )->toArray(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(PermissionRequest $request, UpdateUserPermissionsAction $action, User $user): RedirectResponse
    {
        $this->authorize("managePermissions");

        $action->execute($user, $request->input("permissions"));

        return redirect()
            ->back()
            ->with("success", __("Permissions updated."));
    }
}
