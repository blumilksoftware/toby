<?php

declare(strict_types=1);

namespace Toby\Observers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Toby\Models\User;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

class UserObserver
{
    public function __construct(protected InitialAvatar $generator)
    {
    }

    public function created(User $user): void
    {
        $user->avatar = $this->generateAvatar($user);

        $user->save();
    }

    public function updating(User $user): void
    {
        if ($user->isDirty("name")) {
            $user->avatar = $this->generateAvatar($user);
        }
    }

    public function forceDeleted(User $user): void
    {
        Storage::delete($user->avatar);
    }

    protected function generateAvatar(User $user): string
    {
        $path = "avatars/{$user->id}.svg";

        Storage::put($path, $this->generator->rounded()->background($this->getRandomColor())->generateSvg($user->name));

        return $path;
    }

    protected function getRandomColor(): string
    {
        $colors = config("colors");

        return Arr::random($colors);
    }
}
