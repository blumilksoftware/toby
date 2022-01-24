<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class YearPeriodFactory extends Factory
{
    public function definition(): array
    {
        return [
            "year" => (int)$this->faker->unique()->year,
        ];
    }
}
