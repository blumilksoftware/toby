<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\User;

class VacationCalendarTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testAdministrativeApproverCanDownloadTimesheet(): void
    {
        $user = User::factory()->createQuietly();
        $administrativeApprover = User::factory()->administrativeApprover()->createQuietly();

        $this->actingAs($administrativeApprover)
            ->get("/timesheet/january")
            ->assertOk();
    }

    public function testEmployeeCannotDownloadTimesheet(): void
    {
        $user = User::factory()->createQuietly();

        $this->actingAs($user)
            ->get("/timesheet/january")
            ->assertForbidden();
    }
}
