<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Models\User;
use Toby\Models\Vacation;
use Toby\Models\VacationRequest;

class VacationFactory extends Factory
{
    protected $model = Vacation::class;

    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "vacation_request_id" => VacationRequest::factory(),
            "date" => CarbonImmutable::create($this->faker->dateTimeThisYear),
        ];
    }
}
