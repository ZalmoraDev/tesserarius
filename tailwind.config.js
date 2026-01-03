/** @type {import('tailwindcss').Config} */

export default {
    content: [
        './app/view/**/*.php',       // all php files containing Tailwind classes
        './app/view/skeleton/**/*.php',       // all php files containing Tailwind classes
        './app/view/components/**/*.php',       // all php files containing Tailwind classes
        './app/resources/js/**/*.js', // any JS files using Tailwind classes
    ],
    theme: {
        extend: {}
    },
    plugins: []
}
