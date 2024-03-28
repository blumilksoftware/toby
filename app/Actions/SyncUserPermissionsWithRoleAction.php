<?php

declare(strict_types=1);

namespace Toby\Actions;

use Toby\Models\User;

class SyncUserPermissionsWithRoleAction
{
    public function execute(User $user): User
    {
        $user->syncPermissions($user->role->permissions());

        return $user;
    }
}
