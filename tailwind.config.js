const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        'blumilk': {
          '25': '#F4F8FD',
          '50': '#D5DFEE',
          '100': '#C7D4E9',
          '200': '#AABDDD',
          '300': '#8CA7D1',
          '400': '#6F90C6',
          '500': '#527ABA',
          '600': '#3C5F97',
          '700': '#2C466F',
          '800': '#1C2D47',
          '900': '#0C141F',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/line-clamp'),
  ],
}
