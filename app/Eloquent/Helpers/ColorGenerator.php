<?php

declare(strict_types=1);

namespace Toby\Eloquent\Helpers;

class ColorGenerator
{
    public static function generate(string $text): string
    {
        $colors = config("colors");
        $hash = static::calculateHash($text);

        $index = $hash - count($colors) * floor($hash / count($colors));

        return $colors[$index];
    }

    protected static function calculateHash(string $text): int
    {
        $hash = 0;

        if (empty($text)) {
            return $hash;
        }

        for ($i = 0; $i < mb_strlen($text); $i++) {
            $hash = abs((int)(($hash << 2) - $hash) + mb_ord($text[$i]));
        }

        return $hash;
    }
}
