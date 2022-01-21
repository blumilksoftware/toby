<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Toby\Models\User;
use Toby\Models\VacationLimit;
use Toby\Models\YearPeriod;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(35)->create();
        User::factory([
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
        ])->create();

        $users = User::all();

        YearPeriod::factory()
            ->count(3)
            ->sequence(
                ["year" => Carbon::now()->year - 1],
                ["year" => Carbon::now()->year],
                ["year" => Carbon::now()->year + 1],
            )
            ->afterCreating(function (YearPeriod $yearPeriod) use ($users) {
                foreach ($users as $user) {
                    VacationLimit::factory()
                        ->for($yearPeriod)
                        ->for($user)
                        ->create();
                }
            })
        ->create();
    }
}
