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
                brand: '#0B4174',        // primary navy — buttons, links, active states (measured: rgb(11,65,116))
                'brand-dark': '#083150', // hover/pressed state, darker navy
                ink: '#0f172a',          // headings, body text (slate-900)
                'ink-soft': '#64748b',   // secondary/muted text (slate-500)
                paper: '#f8fafc',        // page background tint (slate-50) — NOT white; use white only for cards/header
                line: '#e2e8f0',         // borders (slate-200)
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
