<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Toby\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(35)->create();
        User::factory([
            "email" => env("LOCAL_EMAIL_FOR_LOGIN_VIA_GOOGLE"),
        ])->create();
    }
}
