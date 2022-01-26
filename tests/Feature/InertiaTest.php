<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\FeatureTestCase;
use Toby\Eloquent\Models\User;

class InertiaTest extends FeatureTestCase
{
    use DatabaseMigrations;

    public function testInertia(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get("/");

        $response->assertOk();
        $response->assertInertia(fn(Assert $page) => $page->component("Dashboard"));
    }
}
