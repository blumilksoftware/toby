<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\FeatureTestCase;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Models\User;

class SelectYearPeriodTest extends FeatureTestCase
{
    use DatabaseMigrations;

    protected YearPeriodRetriever $yearPeriodRetriever;

    protected function setUp(): void
    {
        parent::setUp();

        $this->yearPeriodRetriever = $this->app->make(YearPeriodRetriever::class);
    }

    public function testUserCanSelectNextYearPeriod(): void
    {
        $nextYearPeriod = $this->createYearPeriod(Carbon::now()->year + 1);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post("/year-periods/{$nextYearPeriod->id}/select")
            ->assertRedirect();

        $this->assertSame($nextYearPeriod->id, $this->yearPeriodRetriever->selected()->id);
    }

    public function testUserCannotSelectNextYearPeriodIfDoesntExist(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post("/year-periods/25/select")
            ->assertNotFound();
    }

    public function testIfUserDoesntSelectAnyYearPeriodCurrentActsAsSelected(): void
    {
        $currentYearPeriod = $this->yearPeriodRetriever->current();

        $this->assertSame($currentYearPeriod->id, $this->yearPeriodRetriever->selected()->id);
    }
}
