<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            "first_name" => $this->faker->firstName(),
            "last_name" => $this->faker->lastName(),
            "email" => $this->faker->unique()->safeEmail(),
            "employment_form" => $this->faker->randomElement(EmploymentForm::cases()),
            "position" => $this->faker->jobTitle(),
            "role" => Role::Employee,
            "employment_date" => Carbon::createFromInterface($this->faker->dateTimeBetween("2020-10-27"))->toDateString(),
            "remember_token" => Str::random(10),
        ];
    }
}
