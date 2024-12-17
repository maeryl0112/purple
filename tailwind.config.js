const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./node_modules/flowbite/**/*.js",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            keyframes: {
                'fade-slide': {
                  '0%': { opacity: '0', transform: 'translateY(20px)' },
                  '100%': { opacity: '1', transform: 'translateY(0)' },
                },
              },
              animation: {
                'fade-slide': 'fade-slide 1s ease-out',
              },
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                salonPurple: '#8751aa', // salon color
                secondary: '#9667b4',
                darkPurple: '#6a2695',
                lightPurple: '#c3a8d4',
            },
        },
    },

    safelist: [
        ...[...Array(10).keys()].flatMap(i => [`top-[${i*10}%]`, `left-[${i*10}%]`])
    ],

    plugins: [
        require('flowbite/plugin')({
            charts: true,
        }),
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
        require('@tailwindcss/aspect-ratio'),
    ],
};
