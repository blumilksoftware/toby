<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Toby\Models\Benefit;
use Toby\Models\BenefitsReport;
use Toby\Models\User;

class BenefitsReportFactory extends Factory
{
    protected $model = BenefitsReport::class;

    public function definition(): array
    {
        $users = $this->generateUsers();
        $benefits = $this->generateBenefits();
        $data = $this->generateData($users, $benefits);

        return [
            "name" => $this->faker->unique()->date("M Y"),
            "users" => $users,
            "benefits" => $benefits,
            "data" => $data,
            "committed_at" => $this->faker->date,
        ];
    }

    protected function generateUsers(): array
    {
        $users = User::all();

        return $users->map(fn(User $user): array => [
            "id" => $user->id,
            "name" => $user->profile->full_name,
            "email" => $user->email,
            "avatar" => $user->profile->getAvatar(),
        ])->all();
    }

    protected function generateBenefits(): array
    {
        $benefits = Benefit::all();

        return $benefits->map(fn(Benefit $benefit): array => [
            "id" => $benefit->id,
            "name" => $benefit->name,
            "companion" => $benefit->companion,
        ])->all();
    }

    protected function generateData(array $users, array $benefits): array
    {
        return Arr::map($users, fn(array $user): array => [
            "user" => $user["id"],
            "comment" => $this->faker->sentence,
            "benefits" => Arr::map($benefits, fn(array $benefit): array => [
                "id" => $benefit["id"],
                "employee" => $this->faker->numberBetween(100, 30000),
                "employer" => $this->faker->boolean(30) ? $this->faker->numberBetween(100, 30000) : null,
            ]),
        ]);
    }
}
