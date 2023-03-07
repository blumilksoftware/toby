<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;
use Toby\Eloquent\Models\User;

class AuthenticationTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::all()->last();
    }

    public function testUserCanLogout(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit(new HomePage())
                ->waitFor("@user-menu")
                ->click("@user-menu")
                ->waitForText("Wyloguj się")
                ->press("Wyloguj się")
                ->on(new HomePage())
                ->waitFor("@login-link");
        });
    }
}
