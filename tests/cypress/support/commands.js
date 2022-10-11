Cypress.Commands.add('attr', { prevSubject: false }, (attr) => {
  return cy.get(`[data-cy="${attr}"]`)
})

Cypress.Commands.add('changeDate', (year, month, day) => {
  cy.get('.open > .flatpickr-months > .flatpickr-month > .flatpickr-current-month > .numInputWrapper > .numInput')
    .clear()
    .type(year)

  cy.get('.open > .flatpickr-months > .flatpickr-month > .flatpickr-current-month > .flatpickr-monthDropdown-months')
    .should('be.visible')
    .select(month)

  cy.get('.open > .flatpickr-innerContainer > .flatpickr-rContainer > .flatpickr-days > .dayContainer')
    .contains('span', day)
    .click()
})