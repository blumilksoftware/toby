<?php

declare(strict_types=1);

namespace Toby\Domain\Actions;

use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\YearPeriod;

class CreateUserAction
{
    public function execute(array $data): User
    {
        $user = new User($data);

        $user->save();

        $this->createVacationLimitsFor($user);

        return $user;
    }

    protected function createVacationLimitsFor(User $user): void
    {
        $yearPeriods = YearPeriod::all();

        foreach ($yearPeriods as $yearPeriod) {
            $user->vacationLimits()->create([
                "year_period_id" => $yearPeriod->id,
            ]);
        }
    }
}
