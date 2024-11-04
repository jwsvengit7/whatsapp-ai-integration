import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { fileURLToPath, URL } from 'url';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    base: 'https://uat.smefunds.com/',
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/media.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
});
