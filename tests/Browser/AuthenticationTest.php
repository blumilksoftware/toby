<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Toby\Models\User;

class AuthenticationTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testUserCanLogout(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit("/")
                ->assertVisible("@user-menu")
                ->click("@user-menu")
                ->assertVisible("@user-menu-list")
                ->assertSee("Sign out")
                ->press("Sign out")
                ->assertPathIs("/");
        });
    }
}
