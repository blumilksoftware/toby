<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Eloquent\Models\EquipmentItem;
use Toby\Eloquent\Models\User;

class EquipmentItemFactory extends Factory
{
    protected $model = EquipmentItem::class;

    public function definition(): array
    {
        $isAssigned = $this->faker->boolean;

        return [
            "id_number" => $this->faker->numerify("ABC#########"),
            "name" => $this->faker->word,
            "assignee_id" => $isAssigned ? User::factory() : null,
            "assigned_at" => $isAssigned ? $this->faker->dateTimeBetween("-1 year") : null,
            "is_mobile" => $this->faker->boolean,
            "labels" => [
                $this->faker->word, $this->faker->word, $this->faker->word,
            ],
        ];
    }
}
