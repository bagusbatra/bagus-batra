# Bagus Batra — Portfolio (Laravel)

Portfolio & tech blog Bagus Batra ("Senior Web Developer & Technical Writer"), dibangun sebagai aplikasi **Laravel** murni: Blade templating + [Alpine.js](https://alpinejs.dev) untuk seluruh interaktivitas client-side (toggle bahasa ID/EN, modal CV/proyek/artikel, filter kategori, scroll-spy navbar, mobile drawer, dsb.) dan **Tailwind CSS v4** untuk styling. Tidak ada React/Vue di project ini.

Konten (proyek, artikel blog, pengalaman kerja, testimoni, skill) disimpan di database SQLite via Eloquent models + migrations + seeders. Data profil/situs singleton (`PERSONAL_INFO`, `SOCIAL_LINKS`) ada di `config/portfolio.php`.

## Stack

- Laravel 13 (PHP 8.3)
- SQLite (`database/database.sqlite`)
- Blade + Alpine.js 3
- Tailwind CSS v4 (`@tailwindcss/vite`)
- Vite

## Menjalankan Project

1. Install dependency PHP:
   ```bash
   composer install
   ```
2. Install dependency Node & build asset front-end:
   ```bash
   npm install
   npm run build
   # atau untuk mode development dengan hot-reload:
   npm run dev
   ```
3. Siapkan file environment (jika belum ada `.env`):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Buat file database SQLite kosong (jika belum ada):
   ```bash
   touch database/database.sqlite
   ```
   Pastikan `.env` berisi:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database/database.sqlite
   ```
5. Jalankan migration + seeder (mengisi 5 proyek, 4 artikel blog lengkap, 3 pengalaman kerja, 3 testimoni, 12 skill):
   ```bash
   php artisan migrate --seed
   ```
6. Jalankan server development:
   ```bash
   php artisan serve
   ```
   Buka `http://127.0.0.1:8000`.

## Struktur Penting

- `app/Models/` — `Project`, `BlogPost`, `Experience`, `Testimonial`, `Skill`, `ContactMessage`
- `app/Http/Controllers/PortfolioController.php` — merender halaman utama
- `app/Http/Controllers/ContactMessageController.php` — menangani submit form kontak (POST `/contact`)
- `database/migrations/` — skema 6 tabel (5 entitas konten + pesan kontak)
- `database/seeders/` — data seed persis dari desain asli
- `config/portfolio.php` — data profil & social links (bukan tabel database)
- `resources/views/layouts/app.blade.php` — layout utama (head, scroll progress bar, ambient background, floating widget, modal global)
- `resources/views/portfolio/index.blade.php` — halaman utama, meng-include semua partial section
- `resources/views/portfolio/partials/` — satu file per section (navbar, hero, about, projects, playground, experience, blog, testimonials, contact, footer) + modal (cv-modal, project-modal, article-modal)
- `resources/views/components/icon.blade.php` — komponen ikon SVG inline (pengganti lucide-react)
- `resources/css/app.css` — Tailwind v4 + custom classes (frosted-glass, scrollbar, dsb.)
- `resources/js/app.js` & `resources/js/portfolio.js` — inisialisasi Alpine.js, store bahasa (`$store.lang`), store UI/modal (`$store.ui`), dan komponen Alpine per section

## Catatan

- Bahasa ID/EN untuk label UI ditulis langsung di Blade dengan dua `<span>` yang ditoggle Alpine (`$store.lang.current`), disimpan ke `localStorage` agar konsisten di seluruh halaman.
- Konten panjang (deskripsi proyek, isi artikel blog, achievement pengalaman kerja) hanya berbahasa Indonesia, sesuai data sumber asli.
- Form kontak melakukan submit sungguhan (POST `/contact`) tersimpan ke tabel `contact_messages`, lalu menampilkan pesan sukses via session flash.
