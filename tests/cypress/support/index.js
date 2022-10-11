import './laravel-commands'
import './laravel-routes'
import './commands'

before(() => {
    cy.artisan('config:clear', {}, { log: false })
    cy.task('activateCypressEnvFile', {}, { log: false })
    cy.refreshRoutes()
})

after(() => {
    cy.task('activateLocalEnvFile', {}, { log: false })
    cy.artisan('config:clear', {}, { log: false })
})