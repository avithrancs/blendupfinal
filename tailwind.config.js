import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                'poppins': ['Poppins', 'sans-serif'],
                'open-sans': ['Open Sans', 'sans-serif'],
            },
            colors: {
                brand: '#689b8a',
                'deep-teal': '#256D6D',
                'muted-coral': '#E38470',
                'soft-cream': '#FFF5E1',
                'olive-green': '#7C9473',
                'charcoal': '#4A4A4A',
            },
        },
    },

    plugins: [forms, typography],
};
