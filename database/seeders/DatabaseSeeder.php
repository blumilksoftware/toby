<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\VacationDaysCalculator;
use Toby\Eloquent\Helpers\UserAvatarGenerator;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\VacationRequest;
use Toby\Eloquent\Models\YearPeriod;

class DatabaseSeeder extends Seeder
{
    public function __construct(
        protected UserAvatarGenerator $avatarGenerator,
    ) {
    }

    public function run(): void
    {
        User::unsetEventDispatcher();
        YearPeriod::unsetEventDispatcher();
        VacationRequest::unsetEventDispatcher();

        User::factory(9)->create();
        User::factory([
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
        ])
            ->create();

        $users = User::all();

        $this->generateAvatarsForUsers($users);

        YearPeriod::factory()
            ->count(3)
            ->sequence(
                [
                    "year" => Carbon::now()->year - 1,
                ],
                [
                    "year" => Carbon::now()->year,
                ],
                [
                    "year" => Carbon::now()->year + 1,
                ],
            )
            ->afterCreating(function (YearPeriod $yearPeriod) use ($users): void {
                foreach ($users as $user) {
                    VacationLimit::factory()
                        ->for($yearPeriod)
                        ->for($user)
                        ->create();
                }
            })
            ->afterCreating(function (YearPeriod $yearPeriod): void {
                $polishHolidaysRetriever = new PolishHolidaysRetriever();

                foreach ($polishHolidaysRetriever->getForYearPeriod($yearPeriod) as $holiday) {
                    $yearPeriod->holidays()->create([
                        "name" => $holiday["name"],
                        "date" => $holiday["date"],
                    ]);
                }
            })
            ->create();

        $yearPeriods = YearPeriod::all();

        foreach ($users as $user) {
            VacationRequest::factory()
                ->count(10)
                ->for($user)
                ->sequence(fn() => [
                    "year_period_id" => $yearPeriods->random()->id,
                ])
                ->afterCreating(function (VacationRequest $vacationRequest): void {
                    $days = app(VacationDaysCalculator::class)->calculateDays(
                        $vacationRequest->yearPeriod,
                        $vacationRequest->from,
                        $vacationRequest->to,
                    );

                    foreach ($days as $day) {
                        $vacationRequest->vacations()->create([
                            "date" => $day,
                            "user_id" => $vacationRequest->user->id,
                        ]);
                    }
                })
                ->create();
        }
    }

    protected function generateAvatarsForUsers(Collection $users): void
    {
        foreach ($users as $user) {
            $user->saveAvatar($this->avatarGenerator->generateFor($user));
        }
    }
}
