/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

export default {

  darkMode: ['selector', '[data-color-scheme="dark"]'],

  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],

  theme: {
    extend: {

      screens: {
        'xs': '440px',
        '3xl': '100rem',
      },

      minHeight: {
        'slim': '1.5rem',
        'default': '2rem',
      },

      minWidth: {
        'nav': '240px',
        '1/12': '6.25rem',  // 100px
        '2/12': '12.5rem',  // 200px
        '3/12': '18.75rem', // 300px
        '4/12': '25rem',    // 400px
        '5/12': '31.25rem', // 500px
        '6/12': '37.5rem',  // 600px
        '7/12': '43.75rem', // 700px
        '8/12': '50rem',    // 800px
        '9/12': '56.25rem', // 900px
        '10/12': '75rem', // 1000px
      },
      
      maxWidth: {
        'page': '125rem',
      },

      width: {
        '1/12': '8.33333%',
        '2/12': '16.66667%',
        '3/12': '25%',
        '4/12': '33.33333%',
        '5/12': '41.66667%',
        '6/12': '50%',
        '7/12': '58.33333%',
        '8/12': '66.66667%',
        '9/12': '75%',
        '10/12': '83.33333%',
        '11/12': '91.66667%',
        '12/12': '100%',
      },

      colors: {
        'graphite': '#78786E',
        'ash': '#96968C',
        'pebble': '#D2D2C8',
        'snow': '#F5F5F3',
        'ice': '#96E6FF',
        'candy': '#FF8CD2',
        'lemon': '#EBF000',
        'lime': '#B4E600',
        'flame': '#FF3200',
        'onyx': '#1E1E1E',
        'charcoal': '#464644',
        'steel': '#8C8C8C',
      },

      zIndex: {
        '60': 60,
        '70': 70,
        '80': 80,
        '90': 90,
        '100': 100,
      },

      fontFamily: {
        'otto-bold': ['ABCOtto-Bold', ...defaultTheme.fontFamily.sans],
        'otto-regular': ['ABCOtto-Regular', ...defaultTheme.fontFamily.sans],
        'muoto-regular': ['Muoto-Regular', ...defaultTheme.fontFamily.sans],
        'muoto-medium': ['Muoto-Medium', ...defaultTheme.fontFamily.sans],
        'muoto-italic': ['Muoto-italic', ...defaultTheme.fontFamily.sans],
      },

      fontSize: {
        'xs': '0.75rem', // 12px
        'sm': '0.875rem', // 14px
        'md': '1.063rem', // 17px
        'lg': '2.25rem', // 36px
        'title': '1.75rem', // 28px, cap height = x-height of the logo
        'huge': '12rem', // 128px
      },
    },

    // 0-250 in 1px steps (n/16 rem): mt-48 = 48px
    spacing: Object.fromEntries(
      Array.from({ length: 251 }, (_, n) => [String(n), `${n / 16}rem`])
    ),
  },

  plugins: [
    require('@tailwindcss/forms'),
  ],
}
