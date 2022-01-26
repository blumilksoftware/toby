<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\YearPeriod;

class VacationLimitFactory extends Factory
{
    protected $model = VacationLimit::class;

    public function definition(): array
    {
        $hasVacation = $this->faker->boolean(75);

        return [
            "user_id" => User::factory(),
            "year_period_id" => YearPeriod::factory(),
            "days" => $hasVacation ? $this->faker->numberBetween(20, 26) : null,
        ];
    }
}
