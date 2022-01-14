<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Toby\Enums\FormOfEmployment;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => "{$this->faker->firstName} {$this->faker->lastName}",
            "email" => $this->faker->unique()->safeEmail(),
            "employment_form" => $this->faker->randomElement(FormOfEmployment::cases()),
            "employment_start_date" => $this->faker->dateTimeBetween("2020-10-27"),
            "remember_token" => Str::random(10),
        ];
    }
}
