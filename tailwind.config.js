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
            colors: {
                matcha: {
                    50:  '#e8f0eb',
                    100: '#c5d9cc',
                    200: '#9fbfaa',
                    400: '#6a9b78',
                    600: '#4a7c59',
                    800: '#2d5a3d',
                    900: '#1a3324',
                },
                strawberry: {
                    50:  '#fceef2',
                    100: '#f5c8d5',
                    200: '#ed99b5',
                    400: '#e07090',
                    600: '#c94f6d',
                    800: '#8f2a47',
                    900: '#5e1830',
                },
            },
            fontFamily: {
                sans: ['system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
