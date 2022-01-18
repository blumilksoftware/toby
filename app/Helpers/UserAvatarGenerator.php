<?php

declare(strict_types=1);

namespace Toby\Helpers;

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
            ->background($this->getColor($user->name))
            ->color("#F4F8FD")
            ->smooth()
            ->fontSize(0.33)
            ->generateSvg($user->name);
    }

    protected function getColor(string $name): string
    {
        $colors = config("colors");

        return $colors[strlen($name) % count($colors)];
    }

    protected function generateUuid(): string
    {
        return Str::uuid()->toString();
    }
}
