import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'public/build', // garante que o Laravel Vite procure aqui
        }),
    ],
    build: {
        outDir: 'public/build',   // saída real do Vite
        emptyOutDir: true,        // limpa antes de buildar
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'resources/js/app.js'),
                style: path.resolve(__dirname, 'resources/css/app.css'),
            },
        },
    },
});