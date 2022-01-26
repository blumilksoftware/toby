<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Enums\VacationRequestState;
use Toby\Enums\VacationType;
use Toby\Models\User;

class VacationRequestFactory extends Factory
{
    public function definition(): array
    {
        $number = $this->faker->numberBetween(1, 20);
        $year = $this->faker->year;

        return [
            "user_id" => User::factory(),
            "name" => "{$number}/{$year}",
            "type" => $this->faker->randomElement(VacationType::cases()),
            "state" => $this->faker->randomElement(VacationRequestState::cases()),
            "from" => $this->faker->date,
            "to" => $this->faker->date,
            "comment" => $this->faker->boolean ? $this->faker->paragraph() : null,
        ];
    }
}
