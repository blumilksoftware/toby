<?php

declare(strict_types=1);

namespace Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Toby\Domain\OvertimeRequestStatesRetriever;
use Toby\Enums\SettlementType;
use Toby\Models\OvertimeRequest;
use Toby\Models\User;

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
            "state" => $this->faker->randomElement(OvertimeRequestStatesRetriever::all()),
            "from" => $from,
            "to" => $to,
            "hours" => $hours,
            "settlement_type" => $this->faker->randomElement(SettlementType::cases())->value,
            "comment" => $this->faker->boolean ? $this->faker->paragraph() : null,
        ];
    }
}
