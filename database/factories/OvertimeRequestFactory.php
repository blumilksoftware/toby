<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Domain\OvertimeRequestStatesRetriever;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;
use Toby\Models\YearPeriod;

class OvertimeRequestFactory extends Factory
{
    protected $model = OvertimeRequest::class;

    public function definition(): array
    {
        $from = CarbonImmutable::create($this->faker->dateTimeThisYear);
        $to = $from->addHours($this->faker->numberBetween(1, 10));
        $hours = $to->diffInHours($from);

        return [
            "user_id" => User::factory(),
            "creator_id" => fn(array $attributes): int => $attributes["user_id"],
            "year_period_id" => YearPeriod::factory(),
            "state" => $this->faker->randomElement(OvertimeRequestStatesRetriever::all()),
            "from" => $from,
            "to" => $to,
            "hours" => $hours,
            "comment" => $this->faker->boolean ? $this->faker->paragraph() : null,
        ];
    }
}
