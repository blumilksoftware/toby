<?php

declare(strict_types=1);

namespace Toby\Observers;

use Illuminate\Support\Facades\Storage;
use Toby\Helpers\UserAvatarGenerator;
use Toby\Models\User;

class UserObserver
{
    public function __construct(
        protected UserAvatarGenerator $generator,
    ) {
    }

    public function created(User $user): void
    {
        $user->avatar = $this->generator->generateFor($user);

        $user->save();
    }

    public function updating(User $user): void
    {
        if ($user->isDirty("name")) {
            Storage::delete($user->avatar);
            $user->avatar = $this->generator->generateFor($user);
        }
    }

    public function forceDeleted(User $user): void
    {
        Storage::delete($user->avatar);
    }
}
