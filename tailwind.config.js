import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                // Palette "B&B di charme": terracotta caldo + crema + salvia.
                clay: {
                    50: '#fbf1ea',
                    100: '#f4dccb',
                    200: '#e7b699',
                    300: '#db9069',
                    400: '#cf7346',
                    500: '#b85c38', // terracotta principale
                    600: '#9e4a2c',
                    700: '#8f4429', // terracotta scuro
                    800: '#6e341f',
                    900: '#4f2517',
                },
                cream: {
                    50: '#fdfaf4',
                    100: '#faf4ea', // sfondo principale
                    200: '#f3e9d6',
                    300: '#e9d9bd',
                    400: '#dcc39c',
                },
                sage: {
                    50: '#f2f4ee',
                    100: '#e0e5d6',
                    200: '#c3cdb1',
                    300: '#a3b189',
                    400: '#889a70',
                    500: '#7e8d6b', // salvia
                    600: '#657056',
                    700: '#4f5844',
                    800: '#3c4234',
                },
                ink: {
                    DEFAULT: '#3a2e26', // testo
                    light: '#5c4a3a',
                    soft: '#8a7864',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['"Cormorant Garamond"', ...defaultTheme.fontFamily.serif],
            },
            maxWidth: {
                content: '1200px',
            },
        },
    },
    plugins: [forms, typography],
};
