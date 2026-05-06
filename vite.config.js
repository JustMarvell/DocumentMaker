import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // for future ref.
                // add path here if use additional css or js
                // just if use ngrok
                // but also if use other like ngrok

                'resources/css/app.css',
                'resources/css/home.css',
                'resources/css/video-player.css',
                'resources/js/app.js',
                'resources/js/video-player.js',
            ],
            refresh: true,
        }),
    ],
});
