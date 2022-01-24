<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HolidayFactory extends Factory
{
    public function definition(): array
    {
        $now = Carbon::now();

        return [
            "name" => $this->faker->word,
            "date" => $this->faker->dateTimeBetween($now->startOfYear(), $now->endOfYear()),
        ];
    }
}
