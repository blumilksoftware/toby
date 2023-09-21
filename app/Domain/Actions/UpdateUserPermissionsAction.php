<?php

declare(strict_types=1);

namespace Toby\Domain\Actions;

use Toby\Eloquent\Models\User;

class UpdateUserPermissionsAction
{
    public function execute(User $user, array $permissions): User
    {
        foreach ($permissions as $permission => $value) {
            if ($value) {
                $user->givePermissionTo($permission);
            } else {
                $user->revokePermissionTo($permission);
            }
        }

        return $user;
    }
}
