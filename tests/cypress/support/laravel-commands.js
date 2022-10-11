Cypress.Commands.add('login', (attributes = {}) => {
    let requestBody = attributes.attributes || attributes.state || attributes.load ? attributes : { attributes }

    return cy
      .csrfToken()
      .then((token) => {
          return cy.request({
              method: 'POST',
              url: '/__cypress__/login',
              body: { ...requestBody, _token: token },
              log: false,
          })
      })
      .then(({ body }) => {
          Cypress.Laravel.currentUser = body

          Cypress.log({
              name: 'login',
              message: JSON.stringify(body),
              consoleProps: () => ({ user: body }),
          })
      })
      .its('body', { log: false })
})

Cypress.Commands.add('currentUser', () => {
    return cy.csrfToken().then((token) => {
        return cy
          .request({
              method: 'POST',
              url: '/__cypress__/current-user',
              body: { _token: token },
              log: false,
          })
          .then((response) => {
              if (!response.body) {
                  cy.log('No authenticated user found.')
              }

              Cypress.Laravel.currentUser = response?.body

              return response?.body
          })
    })
})

Cypress.Commands.add('logout', () => {
    return cy
      .csrfToken()
      .then((token) => {
          return cy.request({
              method: 'POST',
              url: '/__cypress__/logout',
              body: { _token: token },
              log: false,
          })
      })
      .then(() => {
          Cypress.log({ name: 'logout', message: '' })
      })
})

Cypress.Commands.add('csrfToken', () => {
    return cy
      .request({
          method: 'GET',
          url: '/__cypress__/csrf_token',
          log: false,
      })
      .its('body', { log: false })
})

Cypress.Commands.add('refreshRoutes', () => {
    return cy.csrfToken().then((token) => {
        return cy
          .request({
              method: 'POST',
              url: '/__cypress__/routes',
              body: { _token: token },
              log: false,
          })
          .its('body', { log: false })
          .then((routes) => {
              cy.writeFile(Cypress.config().supportFolder + '/routes.json', routes, {
                  log: false,
              })

              Cypress.Laravel.routes = routes
          })
    })
})

Cypress.Commands.overwrite('visit', (originalFn, subject, options) => {
    if (subject.route) {
        return originalFn({
            url: Cypress.Laravel.route(subject.route, subject.parameters || {}),
            method: Cypress.Laravel.routes[subject.route].method[0],
            ...options,
        })
    }

    return originalFn(subject, options)
})

Cypress.Commands.add('create', (model, count = 1, attributes = {}, load = [], state = []) => {
    let requestBody = {}

    if (typeof model !== 'object') {
        if (Array.isArray(count)) {
            state = attributes
            attributes = {}
            load = count
            count = 1
        }

        if (typeof count === 'object') {
            state = load
            load = attributes
            attributes = count
            count = 1
        }

        requestBody = { model, state, attributes, load, count }
    } else {
        requestBody = model
    }

    return cy
      .csrfToken()
      .then((token) => {
          return cy.request({
              method: 'POST',
              url: '/__cypress__/factory',
              body: { ...requestBody, _token: token },
              log: false,
          })
      })
      .then((response) => {
          Cypress.log({
              name: 'create',
              message: requestBody.model + (requestBody.count > 1 ? ` (${requestBody.count} times)` : ''),
              consoleProps: () => ({ [model]: response.body }),
          })
      })
      .its('body', { log: false })
})

Cypress.Commands.add('refreshDatabase', (options = {}) => {
    return cy.artisan('migrate:fresh', options)
})

Cypress.Commands.add('seed', (seederClass) => {
    return cy.artisan('db:seed', {
        '--class': seederClass,
    })
})

Cypress.Commands.add('artisan', (command, parameters = {}, options = {}) => {
    options = Object.assign({}, { log: true }, options)

    if (options.log) {
        Cypress.log({
            name: 'artisan',
            message: (() => {
                let message = command

                for (let key in parameters) {
                    message += ` ${key}="${parameters[key]}"`
                }

                return message
            })(),
            consoleProps: () => ({ command, parameters }),
        })
    }

    return cy.csrfToken().then((token) => {
        return cy.request({
            method: 'POST',
            url: '/__cypress__/artisan',
            body: { command: command, parameters: parameters, _token: token },
            log: false,
        })
    })
})

Cypress.Commands.add('php', (command) => {
    return cy
      .csrfToken()
      .then((token) => {
          return cy.request({
              method: 'POST',
              url: '/__cypress__/run-php',
              body: { command: command, _token: token },
              log: false,
          })
      })
      .then((response) => {
          Cypress.log({
              name: 'php',
              message: command,
              consoleProps: () => ({ result: response.body.result }),
          })
      })
      .its('body.result', { log: false })
})