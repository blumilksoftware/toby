<?php

declare(strict_types=1);

namespace Toby\Domain\Policies;

use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;

class KeyPolicy
{
    public function manage(User $user): bool
    {
        return $user->role === Role::AdministrativeApprover;
    }

    public function give(User $user, Key $key): bool
    {
        if ($key->user()->is($user)) {
            return true;
        }

        return $user->role === Role::AdministrativeApprover;
    }
}
