import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('DM Sans', {
                    weights: [400, 500, 600, 700],
                    display: 'swap',
                    fallbacks: ['Arial', 'sans-serif'],
                    optimizedFallbacks: false,
                }),
                bunny('Instrument Serif', {
                    weights: [400],
                    display: 'swap',
                    fallbacks: ['Georgia', 'Times New Roman', 'serif'],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
