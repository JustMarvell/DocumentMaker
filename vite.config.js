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
                'resources/css/home-new.css',
                'resources/css/welcome.css',
                'resources/css/signature/already-reviewed.css',
                'resources/css/signature/create.css',
                'resources/css/signature/review-done.css',
                'resources/css/signature/review.css',
                'resources/css/video-player.css',
                'resources/js/app.js',
                'resources/js/video-player.js',
            ],
            refresh: true,
        }),
    ],
});
