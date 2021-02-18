const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  future: {
    purgeLayersByDefault: true,
  },
  purge: {
    layers: ['components', 'utilities'],
    content: [
      "./pages/**/*.vue",
      "./components/**/*.vue",
      "./plugins/**/*.vue",
      "./static/**/*.vue",
      "./store/**/*.vue"
    ],
  },
  theme: {
    extend: {
      fontFamily: {
        sans: ['Cerebri Sans', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        indigo: {
          '900': '#191e38',
          '800': '#2f365f',
          '600': '#5661b3',
          '500': '#6574cd',
          '400': '#7886d7',
          '300': '#b2b7ff',
          '100': '#e6e8ff',
        },
      },
      boxShadow: theme => ({
        outline: '0 0 0 2px ' + theme('colors.indigo.500'),
      }),
      fill: theme => theme('colors'),
    },
  },
  variants: {
    fill: ['responsive', 'hover', 'focus', 'group-hover'],
    textColor: ['responsive', 'hover', 'focus', 'group-hover'],
    zIndex: ['responsive', 'focus'],
  },
  plugins: [],
}