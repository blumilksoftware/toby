<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Eloquent\Models\Profile;
use Toby\Eloquent\Models\User;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            "user_id" => User::factory(),
            "first_name" => $this->faker->firstName(),
            "last_name" => $this->faker->lastName(),
            "employment_form" => $this->faker->randomElement(EmploymentForm::cases()),
            "position" => $this->faker->jobTitle(),
            "employment_date" => Carbon::createFromInterface($this->faker->dateTimeBetween("2020-10-27"))->toDateString(),
        ];
    }
}
