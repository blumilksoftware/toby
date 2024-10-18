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
use Toby\Models\Holiday;
use Toby\Models\Key;
use Toby\Models\User;
use Toby\Models\VacationRequest;

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

        $year = Carbon::now()->year;

        foreach ([$year - 1, $year, $year + 1] as $i) {
            $polishHolidaysRetriever = new PolishHolidaysRetriever();

            foreach ($polishHolidaysRetriever->getForYear($i) as $holiday) {
                Holiday::query()->create([
                    "name" => $holiday["name"],
                    "date" => $holiday["date"],
                ]);
            }
        }

        foreach ($users as $user) {
            VacationRequest::factory()
                ->count(50)
                ->for($user)
                ->for($user, "creator")
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
