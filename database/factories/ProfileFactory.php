<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Toby\Enums\EmploymentForm;
use Toby\Models\Profile;
use Toby\Models\User;

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
            "birthday" => Carbon::createFromInterface($this->faker->dateTimeBetween("1970-01-01", "1998-01-01"))->toDateString(),
        ];
    }
}
