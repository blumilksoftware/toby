module.exports = {
    env: {
        node: true,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/vue3-recommended',
    ],
    rules: {
        semi: [2, 'always'],
        quotes: ['error', 'single'],
        indent: ['error', 4],
        'vue/html-indent': ['error', 4],
        'vue/multi-word-component-names': 'off',
    }
};
