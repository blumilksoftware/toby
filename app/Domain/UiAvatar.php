<?php

declare(strict_types=1);

namespace Toby\Domain;

use Stringable;

class UiAvatar implements Stringable
{
    public int $length = 2;
    public float $fontSize = 0.33;
    public int $size = 128;
    public bool $rounded = true;
    public bool $uppercase = true;
    public string $background = "#A0A0A0";
    public string $color = "#FFFFFF";
    public bool $bold = true;
    public string $region = "eu";
    public string $name = "";

    public function __toString(): string
    {
        return $this->getUrl();
    }

    public function getUrl(): string
    {
        return "https://eu.ui-avatars.com/api/?" . http_build_query([
            "length" => $this->length,
            "font-size" => $this->fontSize,
            "size" => $this->size,
            "rounded" => $this->rounded ? 1 : 0,
            "uppercase" => $this->uppercase ? 1 : 0,
            "background" => $this->background,
            "color" => $this->color,
            "bold" => $this->bold ? 1 : 0,
            "region" => $this->region,
            "name" => $this->name,
        ]);
    }
}
