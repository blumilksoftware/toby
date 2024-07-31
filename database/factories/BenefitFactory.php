<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Models\Benefit;

class BenefitFactory extends Factory
{
    protected $model = Benefit::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->unique()->word,
            "companion" => $this->faker->boolean(20),
        ];
    }
}
