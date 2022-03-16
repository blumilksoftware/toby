<?php

declare(strict_types=1);

return [
    "provider" => "api",
    "default_region" => "eu",
    "length" => 2,
    "image_size" => 48,
    "font_size" => 0.33,
    "rounded" => true,
    "smooth_rounding" => true,
    "uppercase" => true,
    "background_color" => "#A0A0A0",
    "font_color" => "#FFFFFF",
    "font_bold" => true,
    "providers" => [
        "api" => Rackbeat\UIAvatars\Generators\ApiGenerator::class,
        "local" => Rackbeat\UIAvatars\Generators\LocalGenerator::class,
    ],
];
