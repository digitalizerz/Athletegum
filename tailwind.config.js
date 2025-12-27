import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Enable class-based dark mode
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
        },
    },

    plugins: [forms, daisyui],

    daisyui: {
        themes: [
            {
                light: {
                    primary: '#111827', // gray-900 (black)
                    'primary-content': '#FFFFFF', // white text on primary
                    secondary: '#6B7280', // gray-500
                    'secondary-content': '#FFFFFF',
                    accent: '#EF4444', // red-500 for admin
                    'accent-content': '#FFFFFF',
                    neutral: '#374151', // gray-700
                    'neutral-content': '#FFFFFF',
                    'base-100': '#FFFFFF',
                    'base-200': '#F3F4F6',
                    'base-300': '#E5E7EB',
                    'base-content': '#111827',
                    // Override checkbox colors
                    '--checkbox-checked-bg': '#111827',
                    '--checkbox-checked-border-color': '#111827',
                },
            },
        ],
        base: true,
        styled: true,
        utils: true,
    },
};
