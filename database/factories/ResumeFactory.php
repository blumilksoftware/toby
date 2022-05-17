<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Eloquent\Models\Resume;
use Toby\Eloquent\Models\Technology;

class ResumeFactory extends Factory
{
    protected $model = Resume::class;

    public function definition(): array
    {
        return [
            "name" => fn(array $attributes): bool => empty($attributes["user_id"]) ? $this->faker->name : null,
            "education" => $this->generateEducation(),
            "languages" => $this->generateLanguages(),
            "technologies" => $this->generateTechnologies(),
            "projects" => $this->generateProjects(),
        ];
    }

    protected function generateEducation(): array
    {
        $items = [];

        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $items[] = [
                "school" => $this->faker->sentence,
                "degree" => $this->faker->sentence,
                "fieldOfStudy" => $this->faker->sentence,
                "current" => false,
                "startDate" => Carbon::create($this->faker->date)->format("m/Y"),
                "endDate" => Carbon::create($this->faker->date)->format("m/Y"),
            ];
        }

        return $items;
    }

    protected function generateLanguages(): array
    {
        $languages = new Collection(["English", "Polish", "German"]);
        $number = $this->faker->numberBetween(1, $languages->count());

        return $languages->random($number)
            ->map(fn(string $language): array => [
                "name" => $language,
                "level" => $this->faker->numberBetween(1, 6),
            ])
            ->all();
    }

    protected function generateTechnologies(): array
    {
        $technologies = Technology::all()->pluck("name");
        $number = $this->faker->numberBetween(2, $technologies->count());

        return $technologies->random($number)
            ->map(fn(string $technology): array => [
                "name" => $technology,
                "level" => $this->faker->numberBetween(1, 5),
            ])
            ->all();
    }

    protected function generateProjects(): array
    {
        $items = [];
        $technologies = Technology::all()->pluck("name");

        for ($i = 0; $i < $this->faker->numberBetween(1, 3); $i++) {
            $number = $this->faker->numberBetween(2, $technologies->count());

            $items[] = [
                "description" => $this->faker->text,
                "technologies" => $technologies->random($number)->all(),
                "current" => false,
                "startDate" => Carbon::create($this->faker->date)->format("m/Y"),
                "endDate" => Carbon::create($this->faker->date)->format("m/Y"),
                "tasks" => $this->faker->text,
            ];
        }

        return $items;
    }
}
