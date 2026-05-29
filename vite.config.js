import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
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
