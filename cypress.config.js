const fs = require('fs')
const { defineConfig } = require('cypress')

module.exports = defineConfig({
    component: {
        devServer: {
            framework: 'vue',
            bundler: 'webpack',
        },
    },

    e2e: {
        supportFile: 'tests/cypress/support/index.js',
        baseUrl: 'http://localhost',
        specPattern: 'tests/cypress/e2e/**/*.cy.{js,jsx,ts,tsx}',
        setupNodeEvents(on, config) {
            on('task', {
                activateCypressEnvFile() {
                    if (fs.existsSync('.env.cypress')) {
                        fs.renameSync('.env', '.env.backup')
                        fs.renameSync('.env.cypress', '.env')
                    }

                    return null
                },

                activateLocalEnvFile() {
                    if (fs.existsSync('.env.backup')) {
                        fs.renameSync('.env', '.env.cypress')
                        fs.renameSync('.env.backup', '.env')
                    }

                    return null
                },
            })
        },
    },
})
