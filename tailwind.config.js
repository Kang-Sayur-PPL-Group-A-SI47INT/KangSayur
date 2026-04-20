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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Georgia', 'Cambria', 'Times New Roman', 'serif'],
            },
            colors: {
                cream: {
                    50: '#faf8f5',
                    100: '#f5f1eb',
                    200: '#ede7db',
                    300: '#e0d5c4',
                },
                forest: {
                    600: '#2d5016',
                    700: '#1a3a0a',
                    800: '#0f2b05',
                    900: '#0a1f03',
                },
                olive: {
                    500: '#4a7c2e',
                    600: '#3d6b24',
                    700: '#2f551b',
                },
            },
        },
    },

    plugins: [forms],
};
