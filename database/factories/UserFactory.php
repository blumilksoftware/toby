<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\Profile;
use Toby\Eloquent\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            "email" => $this->faker->unique()->safeEmail(),
            "role" => Role::Employee,
            "remember_token" => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if (!$user->profile()->exists()) {
                Profile::factory()->for($user)->create();
            }
        });
    }

    public function admin(): static
    {
        return $this->state([
            "role" => Role::Administrator,
        ]);
    }

    public function technicalApprover(): static
    {
        return $this->state([
            "role" => Role::TechnicalApprover,
        ]);
    }

    public function administrativeApprover(): static
    {
        return $this->state([
            "role" => Role::AdministrativeApprover,
        ]);
    }
}
