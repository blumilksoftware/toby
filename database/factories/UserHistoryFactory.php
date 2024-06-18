<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Enums\EmploymentForm;
use Toby\Enums\UserHistoryType;
use Toby\Models\User;
use Toby\Models\UserHistory;

class UserHistoryFactory extends Factory
{
    protected $model = UserHistory::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(UserHistoryType::cases());

        return [
            "user_id" => User::factory(),
            "from" => $this->faker->date(),
            "to" => $this->faker->date(),
            "type" => $type,
            "employment_form" => $type->is(UserHistoryType::Employment) ? $this->faker->randomElement(EmploymentForm::cases()) : null,
            "comment" => $this->faker->sentence(),
        ];
    }
}
