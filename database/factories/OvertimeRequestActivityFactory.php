<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Models\OvertimeRequestActivity;
use Toby\States\OvertimeRequest\OvertimeRequestState;

class OvertimeRequestActivityFactory extends Factory
{
    protected $model = OvertimeRequestActivity::class;

    public function definition(): array
    {
        return [
            "from" => $this->faker->randomElement(OvertimeRequestState::all()),
            "to" => $this->faker->randomElement(OvertimeRequestState::all()),
        ];
    }
}
