<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Domain\Enums\VacationType;
use Toby\Domain\VacationRequestStatesRetriever;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

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
