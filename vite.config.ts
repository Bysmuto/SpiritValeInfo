import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import tempest from 'vite-plugin-tempest';
import path from 'path';

export default defineConfig({
    plugins: [
        vue(),
        tailwindcss(),
        tempest()
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './app'),
        },
    }
});
