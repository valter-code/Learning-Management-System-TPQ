// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // CSS utama yang mengimpor Tailwind
                'resources/js/app.js',   
            ],
            refresh: true,
        }),
    ],
});