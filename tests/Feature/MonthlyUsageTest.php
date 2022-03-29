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
        $admin = User::factory()->admin()->createQuietly();

        $this->actingAs($admin)
            ->get("/monthly-usage")
            ->assertOk();
    }

    public function testEmployeeCannotSeeVacationsMonthlyUsage(): void
    {
        $user = User::factory()->createQuietly();

        $this->actingAs($user)
            ->get("/monthly-usage")
            ->assertForbidden();
    }
}
