<?php

declare(strict_types=1);

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\BenefitsPage;
use Tests\Browser\Pages\AddBenefistPage;
use Tests\DuskTestCase;
use Toby\Domain\Enums\Role;
use Toby\Eloquent\Models\User;

class benefit extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::all()->last();
    }
    public function testExample(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->user)
                ->visit(new BenefitsPage())
                ->waitFor("@create-benefit-button")
                ->click("@create-benefit-button")
                ->waitFor("#name")
                ->type('#name','Gym')
                ->click("@save-benefit-button")
                ->waitUntilMissingText('Anuluj')
                ->visit(new AddBenefistPage())
                ->waitFor("@grid-employer")
                ->elements("@grid-employer")->type('0');
        });
    }
}

// it('Create a benefit, add it to a user and check if the calculation is correct', () => {
//     cy.visit('/benefits');

//     cy.attr('create-benefit-button')
//       .click()
    
//     cy.get('#name')
//       .type('Gym')

//     cy.attr('save-benefit-button')
//       .click()
    
//     cy.attr('benefit-name')
//       .should('contain.text', 'Gym')

//     cy.visit('/assigned-benefits')