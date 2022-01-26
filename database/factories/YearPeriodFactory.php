<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Eloquent\Models\YearPeriod;

class YearPeriodFactory extends Factory
{
    protected $model = YearPeriod::class;

    public function definition(): array
    {
        return [
            "year" => (int)$this->faker->unique()->year,
        ];
    }
}
