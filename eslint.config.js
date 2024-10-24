import blumilkDefault from '@blumilksoftware/eslint-config'

export default [
  ...blumilkDefault,
  {
    rules: {
      'vue/require-default-prop': 0,
    },
  },
]
