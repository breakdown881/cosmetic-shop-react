import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        origin: 'http://localhost:5173',
        cors: {
            origin: [
                'http://cosmetic-shop.local.com',
                'http://localhost',
                'http://localhost:8001',
            ],
        },
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/public.css',
                'resources/css/admin.css',
                'resources/js/public.jsx',
                'resources/js/admin.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
    test: {
        environment: 'happy-dom',
        setupFiles: ['resources/js/test/setup.js'],
        globals: true,
    },
});
