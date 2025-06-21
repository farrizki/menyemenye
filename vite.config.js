import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // PERBAIKAN PENTING: Tambahkan konfigurasi server Vite
    server: {
        host: '0.0.0.0', // Atau IP Address spesifik PC kamu, misal '192.168.1.100'
        // Jika ada port yang berbeda, bisa juga ditambahkan:
        // port: 5173, // Port default Vite, bisa disesuaikan
        hmr: {
            host: 'localhost', // PENTING: Ganti dengan IP Address PC kamu yang diakses dari PC lain
            // atau host: 'localhost' jika tidak yakin dan hanya test di LAN
        },
    },
});