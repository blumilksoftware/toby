<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Enums\VacationType;
use Toby\Models\User;
use Toby\Models\VacationRequest;
use Toby\Models\YearPeriod;

class VacationRequestFactory extends Factory
{
    protected $model = VacationRequest::class;

    public function definition(): array
    {
        $from = CarbonImmutable::create($this->faker->dateTimeThisYear);
        $days = $this->faker->numberBetween(0, 20);

        return [
            "user_id" => User::factory(),
            "creator_id" => fn(array $attributes): int => $attributes["user_id"],
            "year_period_id" => YearPeriod::factory(),
            "type" => $this->faker->randomElement(VacationType::cases()),
            "state" => $this->faker->randomElement(VacationRequestStatesRetriever::all()),
            "from" => $from,
            "to" => $from->addDays($days),
            "comment" => $this->faker->boolean ? $this->faker->paragraph() : null,
        ];
    }
}
