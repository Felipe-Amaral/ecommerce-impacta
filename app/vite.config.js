import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                'app/Livewire/**',
                'app/View/Components/**',
                'lang/**',
                'resources/views/**',
                'routes/**',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: process.env.VITE_HOST || '0.0.0.0',
        port: Number(process.env.VITE_PORT || 5173),
        strictPort: true,
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            protocol: process.env.VITE_HMR_PROTOCOL || 'ws',
        },
        watch: {
            usePolling: process.env.VITE_USE_POLLING === 'true',
            interval: Number(process.env.VITE_POLL_INTERVAL || 150),
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
