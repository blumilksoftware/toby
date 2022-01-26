<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Enums\VacationType;
use Toby\Models\User;

class VacationRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "type" => $this->faker->randomElement(VacationType::cases()),
            "from" => $this->faker->date,
            "to" => $this->faker->date,
            "comment" => $this->faker->boolean ? $this->faker->paragraph() : null,
        ];
    }
}
