Cypress.Commands.add('attr', { prevSubject: false }, (attr) => {
  return cy.get(`[data-cy="${attr}"]`)
})

Cypress.Commands.add('changeDay', (day) => {
    cy.get('.open > .flatpickr-innerContainer > .flatpickr-rContainer > .flatpickr-days > .dayContainer')
    .contains('span', day)
    .click()
})

Cypress.Commands.add('changeMonth', (month) => {
  cy.get('.open > .flatpickr-months > .flatpickr-month > .flatpickr-current-month > .flatpickr-monthDropdown-months')
    .should('be.visible')
    .select(month)
})

Cypress.Commands.add('changeYear', (year) => {
  cy.get('.open > .flatpickr-months > .flatpickr-month > .flatpickr-current-month > .numInputWrapper > .numInput')
    .clear()
    .type(year)
})

Cypress.Commands.add('changeMonthAndDay', (month, day) => {
  cy.changeMonth(month)
  cy.changeDay(day)
})
