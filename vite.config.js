import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            // Iterasi 13 (Fase 3): 2 entry JS terpisah (publik vs admin) supaya
            // tiap konteks halaman hanya mengunduh bundle yang benar-benar
            // dipakai — lihat docs/RENCANA-OPTIMASI-PERFORMA.md #5 Iterasi 13.
            // CSS tetap satu entry (app.css) — dipakai kedua sisi, lihat
            // docs/LOG-ITERASI.md untuk alasan lengkap kenapa tidak dipecah.
            input: ['resources/css/app.css', 'resources/js/public.js', 'resources/js/admin.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
