import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/login.css', 
                'resources/css/register.css',
                'resources/css/create.css',
                'resources/css/create-musician.css',
                'resources/css/create-business.css',
                'resources/css/feed.css',
                'resources/css/socket.css',
                'resources/css/welcome.css',
                'resources/css/auth.css',
                'resources/js/app.js',
                'resources/js/layout.js',
                'resources/js/socket.js',
                'resources/js/messages.js',
                'resources/js/feed.js',
                'resources/js/create.js',
                'resources/js/create-musician.js',
                'resources/js/create-business.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
