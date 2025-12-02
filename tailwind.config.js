import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#77BF43',
                    50: '#f0f9e8',
                    100: '#d9f0c4',
                    200: '#bfe69c',
                    300: '#a5dc74',
                    400: '#8ed357',
                    500: '#77BF43',
                    600: '#65a836',
                    700: '#528b2c',
                    800: '#406e22',
                    900: '#2d5118',
                },
                secondary: {
                    DEFAULT: '#144BE9',
                    50: '#e8effe',
                    100: '#c4d7fc',
                    200: '#9cbdfa',
                    300: '#74a3f8',
                    400: '#578ff6',
                    500: '#144BE9',
                    600: '#1142d4',
                    700: '#0e38b8',
                    800: '#0b2e9c',
                    900: '#082080',
                },
            },
        },
    },

    plugins: [forms],
};
