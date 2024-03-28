<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Models\EquipmentLabel;

class EquipmentLabelFactory extends Factory
{
    protected $model = EquipmentLabel::class;

    public function definition(): array
    {
        return [
            "name" => $this->faker->word,
        ];
    }
}
