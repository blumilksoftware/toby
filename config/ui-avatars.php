<?php

declare(strict_types=1);
use Rackbeat\UIAvatars\Generators\ApiGenerator;
use Rackbeat\UIAvatars\Generators\LocalGenerator;

return [
    "provider" => "api",
    "default_region" => "eu",
    "length" => 2,
    "image_size" => 128,
    "font_size" => 0.33,
    "rounded" => true,
    "smooth_rounding" => true,
    "uppercase" => true,
    "background_color" => "#A0A0A0",
    "font_color" => "#FFFFFF",
    "font_bold" => true,
    "providers" => [
        "api" => ApiGenerator::class,
        "local" => LocalGenerator::class,
    ],
];
