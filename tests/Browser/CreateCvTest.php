<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\CvPage;
use Tests\DuskTestCase;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;

class CreateCv extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::all()->last();
    }

    public function testAdminCanCreateCv(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit(new CvPage())
                ->waitFor('@create-resume-button')
                ->click('@create-resume-button')
                ->waitFor('@users-listbox-button');
        });
    }
}
