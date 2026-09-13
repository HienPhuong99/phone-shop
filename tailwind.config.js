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
                ink: 'oklch(18% 0.008 240)',
                'ink-soft': 'oklch(48% 0.008 240)',
                paper: 'oklch(98% 0.003 240)',
                line: 'oklch(90% 0.006 240)',
                accent: 'oklch(52% 0.14 220)',
                'accent-dark': 'oklch(42% 0.14 220)',
                'accent-light': 'oklch(75% 0.09 220)',
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                serif: ['Fraunces', 'serif'],
            },
        },
    },

    plugins: [forms],
};
