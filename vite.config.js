import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/sass/app.scss',
                'resources/sass/login.scss', // Agregado
                'resources/js/login.js',
                'resources/sass/admin/dashboard.css',
                'resources/js/betcontrol.js',
                'resources/js/bets.js',
                'resources/js/reports.js',
            ],
            refresh: true,
        }),
    ],
});