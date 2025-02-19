/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

export default {

  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],

  theme: {
    extend: {

      screens: {
        'xs': '440px',
      },

      minHeight: {
        'default': '2rem',
        'slim': '1.5rem',
      },
      
      maxWidth: {
        'page': '100rem',
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
        'md': '1.125rem', // 18px
        'lg': '2.25rem', // 36px
      },

    },

    spacing: {
      '0' : '0rem',
      '1' : '0.0625rem',
      '2' : '0.125rem',
      '3' : '0.1875rem',
      '4' : '0.25rem',
      '5' : '0.3125rem',
      '6' : '0.375rem',
      '7' : '0.4375rem',
      '8' : '0.5rem',
      '9' : '0.5625rem',
      '10' : '0.625rem',
      '11' : '0.6875rem',
      '12' : '0.75rem',
      '13' : '0.8125rem',
      '14' : '0.875rem',
      '15' : '0.9375rem',
      '16' : '1rem',
      '17' : '1.0625rem',
      '18' : '1.125rem',
      '19' : '1.1875rem',
      '20' : '1.25rem',
      '21' : '1.3125rem',
      '22' : '1.375rem',
      '23' : '1.4375rem',
      '24' : '1.5rem',
      '25' : '1.5625rem',
      '26' : '1.625rem',
      '27' : '1.6875rem',
      '28' : '1.75rem',
      '29' : '1.8125rem',
      '30' : '1.875rem',
      '31' : '1.9375rem',
      '32' : '2rem',
      '33' : '2.0625rem',
      '34' : '2.125rem',
      '35' : '2.1875rem',
      '36' : '2.25rem',
      '37' : '2.3125rem',
      '38' : '2.375rem',
      '39' : '2.4375rem',
      '40' : '2.5rem',
      '41' : '2.5625rem',
      '42' : '2.625rem',
      '43' : '2.6875rem',
      '44' : '2.75rem',
      '45' : '2.8125rem',
      '46' : '2.875rem',
      '47' : '2.9375rem',
      '48' : '3rem',
      '49' : '3.0625rem',
      '50' : '3.125rem',
      '51' : '3.1875rem',
      '52' : '3.25rem',
      '53' : '3.3125rem',
      '54' : '3.375rem',
      '55' : '3.4375rem',
      '56' : '3.5rem',
      '57' : '3.5625rem',
      '58' : '3.625rem',
      '59' : '3.6875rem',
      '60' : '3.75rem',
      '61' : '3.8125rem',
      '62' : '3.875rem',
      '63' : '3.9375rem',
      '64' : '4rem',
      '65' : '4.0625rem',
      '66' : '4.125rem',
      '67' : '4.1875rem',
      '68' : '4.25rem',
      '69' : '4.3125rem',
      '70' : '4.375rem',
      '71' : '4.4375rem',
      '72' : '4.5rem',
      '73' : '4.5625rem',
      '74' : '4.625rem',
      '75' : '4.6875rem',
      '76' : '4.75rem',
      '77' : '4.8125rem',
      '78' : '4.875rem',
      '79' : '4.9375rem',
      '80' : '5rem',
      '81' : '5.0625rem',
      '82' : '5.125rem',
      '83' : '5.1875rem',
      '84' : '5.25rem',
      '85' : '5.3125rem',
      '86' : '5.375rem',
      '87' : '5.4375rem',
      '88' : '5.5rem',
      '89' : '5.5625rem',
      '90' : '5.625rem',
      '91': '5.6875rem',
      '92': '5.75rem',
      '93': '5.8125rem',
      '94': '5.875rem',
      '95': '5.9375rem',
      '96': '6rem',
      '97': '6.0625rem',
      '98': '6.125rem',
      '99': '6.1875rem',
      '100': '6.25rem',
      '101': '6.3125rem',
      '102': '6.375rem',
      '103': '6.4375rem',
      '104': '6.5rem',
      '105': '6.5625rem',
      '106': '6.625rem',
      '107': '6.6875rem',
      '108': '6.75rem',
      '109': '6.8125rem',
      '110': '6.875rem',
      '111': '6.9375rem',
      '112': '7rem',
      '113': '7.0625rem',
      '114': '7.125rem',
      '115': '7.1875rem',
      '116': '7.25rem',
      '117': '7.3125rem',
      '118': '7.375rem',
      '119': '7.4375rem',
      '120': '7.5rem',
      '121': '7.5625rem',
      '122': '7.625rem',
      '123': '7.6875rem',
      '124': '7.75rem',
      '125': '7.8125rem',
      '126': '7.875rem',
      '127': '7.9375rem',
      '128': '8rem',
      '129': '8.0625rem',
      '130': '8.125rem',
      '131': '8.1875rem',
      '132': '8.25rem',
      '133': '8.3125rem',
      '134': '8.375rem',
      '135': '8.4375rem',
      '136': '8.5rem',
      '137': '8.5625rem',
      '138': '8.625rem',
      '139': '8.6875rem',
      '140': '8.75rem',
      '141': '8.8125rem',
      '142': '8.875rem',
      '143': '8.9375rem',
      '144': '9rem',
      '145': '9.0625rem',
      '146': '9.125rem',
      '147': '9.1875rem',
      '148': '9.25rem',
      '149': '9.3125rem',
      '150': '9.375rem',
      '151': '9.4375rem',
      '152': '9.5rem',
      '153': '9.5625rem',
      '154': '9.625rem',
      '155': '9.6875rem',
      '156': '9.75rem',
      '157': '9.8125rem',
      '158': '9.875rem',
      '159': '9.9375rem',
      '160': '10rem',
      '161': '10.0625rem',
      '162': '10.125rem',
      '163': '10.1875rem',
      '164': '10.25rem',
      '165': '10.3125rem',
      '166': '10.375rem',
      '167': '10.4375rem',
      '168': '10.5rem',
      '169': '10.5625rem',
      '170': '10.625rem',
      '171': '10.6875rem',
      '172' : '10.75rem',
      '173' : '10.8125rem',
      '174' : '10.875rem',
      '175' : '10.9375rem',
      '176' : '11rem',
      '177' : '11.0625rem',
      '178' : '11.125rem',
      '179' : '11.1875rem',
      '180' : '11.25rem',
      '181' : '11.3125rem',
      '182' : '11.375rem',
      '183' : '11.4375rem',
      '184' : '11.5rem',
      '185' : '11.5625rem',
      '186' : '11.625rem',
      '187' : '11.6875rem',
      '188' : '11.75rem',
      '189' : '11.8125rem',
      '190' : '11.875rem',
      '191' : '11.9375rem',
      '192' : '12rem',
      '193' : '12.0625rem',
      '194' : '12.125rem',
      '195' : '12.1875rem',
      '196' : '12.25rem',
      '197' : '12.3125rem',
      '198' : '12.375rem',
      '199' : '12.4375rem',
      '200' : '12.5rem',
      '201' : '12.5625rem',
      '202' : '12.625rem',
      '203' : '12.6875rem',
      '204' : '12.75rem',
      '205' : '12.8125rem',
      '206' : '12.875rem',
      '207' : '12.9375rem',
      '208' : '13rem',
      '209' : '13.0625rem',
      '210' : '13.125rem',
      '211' : '13.1875rem',
      '212' : '13.25rem',
      '213' : '13.3125rem',
      '214' : '13.375rem',
      '215' : '13.4375rem',
      '216' : '13.5rem',
      '217' : '13.5625rem',
      '218' : '13.625rem',
      '219' : '13.6875rem',
      '220' : '13.75rem',
      '221' : '13.8125rem',
      '222' : '13.875rem',
      '223' : '13.9375rem',
      '224' : '14rem',
      '225' : '14.0625rem',
      '226' : '14.125rem',
      '227': '14.1875rem',
      '228': '14.25rem',
      '229': '14.3125rem',
      '230': '14.375rem',
      '231': '14.4375rem',
      '232': '14.5rem',
      '233': '14.5625rem',
      '234': '14.625rem',
      '235': '14.6875rem',
      '236': '14.75rem',
      '237': '14.8125rem',
      '238': '14.875rem',
      '239': '14.9375rem',
      '240': '15rem',
      '241': '15.0625rem',
      '242': '15.125rem',
      '243': '15.1875rem',
      '244': '15.25rem',
      '245': '15.3125rem',
      '246': '15.375rem',
      '247': '15.4375rem',
      '248': '15.5rem',
      '249': '15.5625rem',
      '250': '15.625rem',
    },
  },

  plugins: [
    require('@tailwindcss/forms'),
  ],
}

