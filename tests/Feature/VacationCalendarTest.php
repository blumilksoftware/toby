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
        $administrativeApprover = User::factory()->administrativeApprover()->create();

        $this->actingAs($administrativeApprover)
            ->get("/vacation/timesheet/january")
            ->assertOk();
    }

    public function testEmployeeCannotDownloadTimesheet(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get("/vacation/timesheet/january")
            ->assertForbidden();
    }
}
