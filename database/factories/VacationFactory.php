<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\Vacation;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class VacationFactory extends Factory
{
    protected $model = Vacation::class;

    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "vacation_request_id" => VacationRequest::factory(),
            "year_period_id" => YearPeriod::factory(),
            "date" => CarbonImmutable::create($this->faker->dateTimeThisYear),
        ];
    }
}
