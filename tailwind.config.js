import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', '"Noto Sans JP"', ...defaultTheme.fontFamily.sans],
                jp: ['"Noto Sans JP"', 'sans-serif'],
            },
            colors: {
                japan: {
                    red: '#DC2626',
                    crimson: '#E11D48',
                    sakura: '#FB7185',
                    'sakura-light': '#FFF1F2',
                    navy: '#0F172A',
                    'navy-card': '#1E293B',
                    slate: '#334155',
                    gold: '#F59E0B',
                    emerald: '#10B981',
                },
            },
        },
    },

    plugins: [forms],
};
