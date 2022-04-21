<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;

class KeyFactory extends Factory
{
    protected $model = Key::class;

    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
        ];
    }
}
