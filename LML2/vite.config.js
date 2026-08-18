import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';

// Fungsi untuk membaca semua file .css di dalam folder resources/css
const cssFiles = fs.readdirSync('resources/css')
    .filter(file => file.endsWith('.css'))
    .map(file => `resources/css/${file}`);

// Fungsi untuk membaca semua file .js di dalam folder resources/js
const jsFiles = fs.readdirSync('resources/js')
    .filter(file => file.endsWith('.js'))
    .map(file => `resources/js/${file}`);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...cssFiles, // <-- Otomatis memasukkan semua file CSS!
                ...jsFiles,  // <-- Otomatis memasukkan semua file JS!
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});