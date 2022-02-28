<?php

declare(strict_types=1);

namespace Toby\Domain\Policies;

use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;

class HolidayPolicy
{
    public function create(User $user): bool
    {
        return $user->role == Role::AdministrativeApprover || $user->role == Role::Administrator;
    }
}
