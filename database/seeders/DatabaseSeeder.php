<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Domain\WorkDaysCalculator;
use Toby\Models\Benefit;
use Toby\Models\BenefitsReport;
use Toby\Models\EquipmentItem;
use Toby\Models\EquipmentLabel;
use Toby\Models\Key;
use Toby\Models\User;
use Toby\Models\VacationLimit;
use Toby\Models\VacationRequest;
use Toby\Models\YearPeriod;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionsSeeder::class);

        User::factory(9)->employee()->create();
        $user = User::factory([
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
        ])
            ->admin()
            ->create();

        $users = User::all();

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
                ->count(50)
                ->for($user)
                ->for($user, "creator")
                ->sequence(fn() => [
                    "year_period_id" => $yearPeriods->random()->id,
                ])
                ->afterCreating(function (VacationRequest $vacationRequest): void {
                    $days = app(WorkDaysCalculator::class)->calculateDays(
                        $vacationRequest->from,
                        $vacationRequest->to,
                        $vacationRequest->type,
                    );

                    foreach ($days as $day) {
                        $vacationRequest->vacations()->create([
                            "date" => $day,
                            "user_id" => $vacationRequest->user->id,
                            "year_period_id" => $vacationRequest->yearPeriod->id,
                        ]);
                    }
                })
                ->create();
        }

        foreach ($users as $user) {
            Key::factory()
                ->for($user)
                ->create();
        }

        Benefit::factory(10)->create();

        BenefitsReport::factory()->create([
            "name" => "current",
            "committed_at" => null,
        ]);

        BenefitsReport::factory(3)->create();

        EquipmentLabel::factory(10)->create();

        EquipmentItem::factory(40)->create();
    }
}
