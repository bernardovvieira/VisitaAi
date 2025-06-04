import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    // server: {
    //     host: true, // permite acesso externo
    //     cors: true, // habilita CORS
    //     hmr: {
    //         host: '10.205.203.27', // ip
    //     },
    // },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,  
        }),
    ],
});
