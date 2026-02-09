import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    build: {
        outDir: 'public/build', // garante que os arquivos vão para public/build
        emptyOutDir: true,      // limpa o diretório antes de compilar
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            publicDirectory: 'public',
        }),
    ],
});