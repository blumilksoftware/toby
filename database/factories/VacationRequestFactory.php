<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Domain\Enums\VacationRequestState;
use Toby\Domain\Enums\VacationType;
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
            "year_period_id" => YearPeriod::factory(),
            "name" => fn(array $attributes) => $this->generateName($attributes),
            "type" => $this->faker->randomElement(VacationType::cases()),
            "state" => $this->faker->randomElement(VacationRequestState::cases()),
            "from" => $from,
            "to" => $from->addDays($days),
            "comment" => $this->faker->boolean ? $this->faker->paragraph() : null,
        ];
    }

    protected function generateName(array $attributes): string
    {
        $year = YearPeriod::find($attributes["year_period_id"])->year;
        $user = User::find($attributes["user_id"]);

        $number = $user->vacationRequests()
            ->whereYear("from", $year)
            ->count() + 1;

        return "{$number}/{$year}";
    }
}
