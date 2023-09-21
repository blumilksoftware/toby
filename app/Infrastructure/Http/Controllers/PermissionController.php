<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Toby\Domain\Actions\UpdateUserPermissionsAction;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\PermissionRequest;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;

class PermissionController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function show(User $user): Response
    {
        $this->authorize("manage permissions");

        $permissions = Permission::all()->pluck("name");

        return inertia("Permissions/Edit", [
            "user" => new SimpleUserResource($user),
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
        $this->authorize("manage permissions");

        $action->execute($user, $request->input("permissions"));

        return redirect()
            ->route("users.index")
            ->with("success", __("Permissions updated."));
    }
}
