<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Toby\Enums\Role;
use Toby\Models\Profile;
use Toby\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            "email" => $this->faker->unique()->safeEmail(),
            "password" => Hash::make("password"),
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

    public function employee(): static
    {
        return $this->state([
            "role" => Role::Employee,
        ])->afterCreating(fn(User $user): User => $user->syncPermissions(Role::Employee->permissions()));
    }

    public function admin(): static
    {
        return $this->state([
            "role" => Role::Administrator,
        ])->afterCreating(fn(User $user): User => $user->syncPermissions(Role::Administrator->permissions()));
    }

    public function technicalApprover(): static
    {
        return $this->state([
            "role" => Role::TechnicalApprover,
        ])->afterCreating(fn(User $user): User => $user->syncPermissions(Role::TechnicalApprover->permissions()));
    }

    public function administrativeApprover(): static
    {
        return $this->state([
            "role" => Role::AdministrativeApprover,
        ])->afterCreating(fn(User $user): User => $user->syncPermissions(Role::AdministrativeApprover->permissions()));
    }
}
