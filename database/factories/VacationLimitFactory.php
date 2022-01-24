<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class VacationLimitFactory extends Factory
{
    public function definition(): array
    {
        $hasVacation = $this->faker->boolean(75);

        return [
            "user_id" => User::factory(),
            "year_period_id" => YearPeriod::factory(),
            "has_vacation" => $hasVacation,
            "days" => $hasVacation ? $this->faker->numberBetween(20, 26) : null,
        ];
    }
}
