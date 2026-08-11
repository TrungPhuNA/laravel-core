import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { fileURLToPath, URL } from 'url';

export default defineConfig({
    build: {
        outDir: '../../public/build-monitor',
        emptyOutDir: true,
        manifest: true,
    },
    plugins: [
        laravel({
            publicDirectory: '../../public',
            buildDirectory: 'build-monitor',
            input: [
                __dirname + '/resources/frontend/app/index.css',
                __dirname + '/resources/frontend/app/main.tsx',
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@shared': fileURLToPath(new URL('../../resources/frontend/shared', import.meta.url)),
        },
    },
});