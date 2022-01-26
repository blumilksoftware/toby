<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Eloquent\Models\Holiday;
use Toby\Eloquent\Models\YearPeriod;

class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->word,
            "date" => $this->faker->unique->date,
            "year_period_id" => YearPeriod::current()->id,
        ];
    }
}
