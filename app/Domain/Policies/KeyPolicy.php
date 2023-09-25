<?php

declare(strict_types=1);

namespace Toby\Domain\Policies;

use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;

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
