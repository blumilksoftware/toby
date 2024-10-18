<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\FeatureTestCase;
use Toby\Models\User;

class CalendarTest extends FeatureTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanSeeCalendarWithDefaultMonthAndYear(): void
    {
        $this->actingAs($this->user)
            ->get("/calendar")
            ->assertOk();
    }

    public function testUserCanSeeCalendarWithSelectedMonthAndYear(): void
    {
        $this->actingAs($this->user)
            ->get("/calendar/05-2024")
            ->assertOk();
    }
}
