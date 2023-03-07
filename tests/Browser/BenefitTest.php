<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\AddBenefistPage;
use Tests\Browser\Pages\BenefitsPage;
use Tests\DuskTestCase;
use Toby\Eloquent\Models\User;

class BenefitTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed("DuskSeeder");

        $this->user = User::all()->last();
    }

    public function testAdminCanCreateAddDeleteBenefits(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit(new BenefitsPage())
                ->waitFor("@create-benefit-button")
                ->click("@create-benefit-button")
                ->waitFor("#name")
                ->type("#name", "Gym")
                ->click("@save-benefit-button")
                ->waitUntilMissingText("Anuluj")
                ->click("@create-benefit-button")
                ->waitFor("#name")
                ->type("#name", "Food")
                ->click("@save-benefit-button")
                ->waitUntilMissingText("Anuluj")
                ->visit(new AddBenefistPage())
                ->waitFor("@grid-employer")
                ->fillGrid(2, 2.1)
                ->fillGrid(3, 2)
                ->fillGrid(4, 2.32)
                ->fillGrid(5, 2)
                ->waitForTextIn("@grid-sum", "4,42 zł")
                ->waitFor(".Vue-Toastification__toast")
                ->visit(new BenefitsPage())
                ->waitFor("@benefit-button")
                ->click("@benefit-button")
                ->click("@benefit-delete-button")
                ->visit(new AddBenefistPage())
                ->waitForTextIn("@grid-sum", "2,32 zł");
        });
    }
}
