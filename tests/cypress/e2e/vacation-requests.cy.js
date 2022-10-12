describe('Vacation requests', () => {

    beforeEach(() => {
        cy.refreshDatabase()
        cy.seed('DemoSeeder')

        cy.php (`Toby\\Eloquent\\Models\\User::first();`)
          .then(user=> {
            cy.login({email: user.email})
        })
    })

    it('Creates a sick vacation request by administrator for another employee', () => {
        cy.visit('/vacation/requests');

        cy.attr('create-vacation-request-button')
          .click()

        cy.url()
          .should('include', '/vacation/requests/create')

        cy.attr('users-listbox-button')
          .click()
        cy.attr('users-list')
          .should('be.visible')
          .contains('Katarzyna Zając')
          .click()

        cy.attr('vacation-types-listbox-button')
          .click()
        cy.attr('vacation-types-list')
          .should('be.visible')
          .contains('Zwolnienie lekarskie')
          .click()

        cy.get('#date_from')
          .parent()
          .click()
          .should('be.visible')
          .changeMonthAndDay(10,14)

        cy.get('#date_to')
          .parent()
          .click()
          .should('be.visible')
          .changeMonthAndDay(10,18)

        cy.attr('estimated-days-text')
          .should('contain.text', '5')

        cy.get('#comment')
          .type('Zwolnienie lekarskie.')

        cy.attr('flowSkipped')
          .click()

        cy.attr('save-button')
          .click()

        cy.url()
          .should('not.include', '/vacation/requests/create')
    });
});
