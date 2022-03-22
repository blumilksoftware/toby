module.exports = {
  env: {
    node: true,
    'vue/setup-compiler-macros': true,
  },
  extends: [
    'eslint:recommended',
    'plugin:vue/vue3-recommended',
  ],
  rules: {
    semi: [2, 'never'],
    quotes: ['error', 'single'],
    indent: ['error', 2],
    'vue/html-indent': ['error', 2],
    'comma-dangle': ['error', 'always-multiline'],
    'object-curly-spacing': ['error', 'always'],
    'vue/require-default-prop': 0,
  },
}
