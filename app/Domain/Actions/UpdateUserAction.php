<?php

declare(strict_types=1);

namespace Toby\Domain\Actions;

use Toby\Models\User;

class UpdateUserAction
{
    public function execute(User $user, array $userData, array $profileData): User
    {
        $user->update($userData);

        $user->profile->update($profileData);

        return $user;
    }
}
