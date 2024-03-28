<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Models\VacationRequestActivity;
use Toby\States\VacationRequest\VacationRequestState;

class VacationRequestActivityFactory extends Factory
{
    protected $model = VacationRequestActivity::class;

    public function definition(): array
    {
        return [
            "from" => $this->faker->randomElement(VacationRequestState::all()),
            "to" => $this->faker->randomElement(VacationRequestState::all()),
        ];
    }
}
