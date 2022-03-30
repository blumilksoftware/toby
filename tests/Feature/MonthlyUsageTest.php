<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\User;

class MonthlyUsageTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdministatorCanSeeVacationsMonthlyUsage(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get("/vacation/monthly-usage")
            ->assertOk();
    }

    public function testEmployeeCannotSeeVacationsMonthlyUsage(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get("/vacation/monthly-usage")
            ->assertForbidden();
    }
}
