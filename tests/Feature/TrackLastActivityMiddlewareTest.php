<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\User;

class TrackLastActivityMiddlewareTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testLastActiveAtFieldIsUpdatedWhenUserDidRequest(): void
    {
        $now = Carbon::now();

        Carbon::setTestNow($now);

        $user = User::factory()->create();

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "last_active_at" => null,
        ]);

        $this->actingAs($user)
            ->get("/")
            ->assertOk();

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "last_active_at" => $now,
        ]);
    }
}
