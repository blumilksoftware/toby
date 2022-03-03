<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Eloquent\Models\VacationRequestActivity;

class VacationRequestActivityFactory extends Factory
{
    protected $model = VacationRequestActivity::class;

    public function definition(): array
    {
        return [
            "from" => $this->faker->randomElement(VacationRequestState::cases()),
            "to" => $this->faker->randomElement(VacationRequestState::cases()),
        ];
    }
}
