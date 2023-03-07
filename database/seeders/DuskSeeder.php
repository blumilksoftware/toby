<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Toby\Domain\Enums\EmploymentForm;
use Toby\Domain\Enums\Role;
use Toby\Domain\PolishHolidaysRetriever;
use Toby\Eloquent\Models\BenefitsReport;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\Technology;
use Toby\Eloquent\Models\User;
use Toby\Eloquent\Models\VacationLimit;
use Toby\Eloquent\Models\YearPeriod;

class DuskSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(9)->create();
        User::factory([
            "email" => "anna.nowak@example.com",
            "role" => Role::Employee,
            "remember_token" => Str::random(10),
        ])
            ->hasProfile([
                "first_name" => "Anna",
                "last_name" => "Nowak",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "tester",
                "employment_date" => Carbon::createFromDate(2021, 5, 10),
            ])
            ->create();
        User::factory([
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
            "role" => Role::Administrator,
        ])
            ->hasProfile([
                "first_name" => "Jan",
                "last_name" => "Kowal",
                "employment_form" => EmploymentForm::EmploymentContract,
                "position" => "tester",
                "employment_date" => Carbon::createFromDate(2021, 5, 10),
            ])
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
                    VacationLimit::factory([
                        "days" => $user->profile->employment_form === EmploymentForm::EmploymentContract ? 26 : null,
                    ])
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
            Key::factory()
                ->for($user)
                ->create();
        }

        BenefitsReport::factory()->create([
            "name" => "current",
            "committed_at" => null,
        ]);

        BenefitsReport::factory(3)->create();
        Technology::factory()->createMany([
            ["name" => "Laravel"],
            ["name" => "Cypress"],
            ["name" => "Dusk"],
        ]);
    }
}
