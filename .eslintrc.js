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
    'eol-last': ['error', 'always'],
    'vue/html-indent': ['error', 2],
    'comma-dangle': ['error', 'always-multiline'],
    'comma-spacing': ['error'],
    'key-spacing': ['error'],
    'object-curly-spacing': ['error', 'always'],
    'vue/require-default-prop': 0,
    'vue/multi-word-component-names': 0,
    'vue/padding-line-between-blocks': ['error', 'always'],
    'vue/component-tags-order': ['error', {
      'order': ['script', 'template', 'style']
    }]
  },
}
