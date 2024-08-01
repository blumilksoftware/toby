<?php

declare(strict_types=1);

namespace Toby\Domain;

use Toby\Enums\VacationType;
use Toby\Models\User;

class MonthWorkingHoursCalculator
{
    public function calculateHours(array $calendar, User $user): int
    {
        return collect($calendar)
            ->filter(
                fn(array $day): bool => !$day["isWeekend"]
                && !$day["isHoliday"]
                && (
                    (
                        isset($day["vacations"][$user->id])
                            && in_array($day["vacations"][$user->id]["type"]->value, [VacationType::RemoteWork->value, VacationType::Delegation->value], true)
                    ) || !isset($day["vacations"][$user->id])
                ),
            )
            ->count();
    }
}
