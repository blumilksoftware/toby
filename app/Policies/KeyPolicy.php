<?php

declare(strict_types=1);

namespace Toby\Policies;

use Toby\Models\Key;
use Toby\Models\User;

class KeyPolicy
{
    public function give(User $user, Key $key): bool
    {
        if ($key->user()->is($user)) {
            return true;
        }

        return $user->hasPermissionTo("manageKeys");
    }
}
