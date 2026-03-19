import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import collectModuleAssetsPaths from './vite-module-loader.js';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig(async () => {
    const input = ['resources/css/app.css', 'resources/js/app.js'];
    const moduleInputs = await collectModuleAssetsPaths([], 'Modules');

    return {
        resolve: {
            alias: {
                '@shared': path.resolve(__dirname, 'resources/frontend/shared'),
            },
        },
        plugins: [
            laravel({
                input: [...input, ...moduleInputs],
                refresh: true,
            }),
            react(),
            tailwindcss(),
        ],
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
