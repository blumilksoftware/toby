<?php

declare(strict_types=1);

namespace Toby\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use SVG\SVG;
use Toby\Models\User;

class UserAvatarGenerator
{
    public function __construct(
        protected InitialAvatar $generator,
    ) {
    }

    public function generateFor(User $user): string
    {
        $path = "avatars/{$this->generateUuid()}.svg";

        Storage::put($path, $this->generate($user));

        return $path;
    }

    protected function generate(User $user): SVG
    {
        return $this->generator->rounded()
            ->background($this->getRandomColor())
            ->color("#F4F8FD")
            ->smooth()
            ->generateSvg($user->name);
    }

    protected function getRandomColor(): string
    {
        $colors = config("colors");

        return Arr::random($colors);
    }

    protected function generateUuid(): string
    {
        return Str::uuid()->toString();
    }
}
