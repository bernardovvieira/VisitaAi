import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // server: {
    //     host: true, // permite acesso externo
    //     cors: true, // habilita CORS
    //     hmr: {
    //         host: '0.0.0.0', // ip
    //     },
    // },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,  
        }),
    ],
});
