<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Eloquent\Models\Technology;

class TechnologyFactory extends Factory
{
    protected $model = Technology::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->unique()->word,
        ];
    }
}
