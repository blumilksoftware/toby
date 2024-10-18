<?php

declare(strict_types=1);

namespace Toby\Actions;

use Toby\Models\User;

class CreateUserAction
{
    public function execute(array $userData, array $profileData): User
    {
        $user = new User($userData);

        $user->save();

        $user->profile()->create($profileData);

        return $user;
    }
}
