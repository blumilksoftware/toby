<?php

declare(strict_types=1);

namespace Toby\Domain\Actions;

use Toby\Eloquent\Models\User;

class SyncUserPermissionsWithRoleAction
{
    public function execute(User $user): User
    {
        $user->syncPermissions($user->role->permissions());

        return $user;
    }
}
