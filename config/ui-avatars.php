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
    "background_color" => "#a0a0a0",
    "font_color" => "#F4F8FD",
    "font_bold" => true,
    "providers" => [
        "api" => Rackbeat\UIAvatars\Generators\ApiGenerator::class,
        "local" => Rackbeat\UIAvatars\Generators\LocalGenerator::class,
    ],
];
