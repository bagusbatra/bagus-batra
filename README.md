# Bagus Batra — Portfolio (Laravel)

Portfolio & tech blog Bagus Batra ("Senior Web Developer & Technical Writer"), dibangun sebagai aplikasi **Laravel** murni: Blade templating + [Alpine.js](https://alpinejs.dev) untuk seluruh interaktivitas client-side (toggle bahasa ID/EN, modal CV/artikel, filter kategori, scroll-spy navbar, mobile drawer, dsb.) dan **Tailwind CSS v4** untuk styling. Tidak ada React/Vue di project ini.

Sejak Fase 2, **Projects** punya halaman publik sungguhan (bukan lagi modal on-page): katalog lengkap di `/projects` dan detail per proyek di `/projects/{project_key}` (mis. `/projects/lumina-saas`), dengan URL yang bisa di-bookmark/dibagikan dan meta tag SEO (title/description/Open Graph) dinamis per proyek. Section Projects di halaman utama (`/`) sekarang hanya menampilkan highlight (proyek `featured`) dengan tombol "Lihat Semua Proyek" menuju `/projects`. Blog masih memakai modal on-page seperti sebelumnya (di luar scope Fase 2).

Seluruh konten yang tampil di halaman publik (`/`) — profil & hero, social links, skills, projects, experience, blog, testimonials, pesan kontak, dan status aktif/nonaktif tiap section — dikelola lewat **admin panel** (`/admin`) yang dibangun khusus untuk project ini (bukan template pihak ketiga), dengan data tersimpan di database **MySQL** via Eloquent models + migrations + seeders. Tidak ada lagi data yang hidup di file config statis.

## Stack

- Laravel 13 (PHP 8.3)
- MySQL (via [Laragon](https://laragon.org) di environment development)
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
4. Siapkan database **MySQL** kosong bernama `bagus_batra_portfolio` (mis. via Laragon, phpMyAdmin, atau `mysql -u root -e "CREATE DATABASE bagus_batra_portfolio"`), lalu pastikan service MySQL sedang berjalan. Isi `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bagus_batra_portfolio
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   (sesuaikan `DB_USERNAME`/`DB_PASSWORD` bila server MySQL Anda tidak memakai default Laragon.)
5. Jalankan migration + seeder (mengisi 5 proyek, 4 artikel blog lengkap, 3 pengalaman kerja, 3 testimoni, 12 skill, 6 social link, 1 profil situs, 9 pengaturan section, dan 1 akun admin):
   ```bash
   php artisan migrate --seed
   ```
6. Buat symlink storage publik (dibutuhkan untuk avatar/gambar yang diupload lewat admin):
   ```bash
   php artisan storage:link
   ```
7. Jalankan server development:
   ```bash
   php artisan serve
   ```
   - Halaman publik: `http://127.0.0.1:8000`
   - Admin panel: `http://127.0.0.1:8000/admin/login`

## Login Admin

Akun admin awal dibuat oleh `AdminUserSeeder` (bagian dari `php artisan migrate --seed` di atas):

```
Email    : admin@bagusbatra.dev
Password : Admin#12345
```

**Wajib ganti password ini setelah login pertama kali** di environment produksi/publik — belum ada halaman "ubah password" di UI, ganti lewat `php artisan tinker`:

```php
$u = App\Models\User::where('email', 'admin@bagusbatra.dev')->first();
$u->password = Hash::make('password-baru-anda');
$u->save();
```

Tidak ada halaman registrasi admin publik dan tidak ada reset password via email — hanya 1 akun admin, sesuai desain Fase 1 (lihat `docs/RENCANA-PENGEMBANGAN.md` bagian 7).

## Struktur Penting

### Halaman publik
- `app/Http/Controllers/PortfolioController.php` — merender halaman utama (`/`), membaca profil/social links/section-status dari database; section Projects di sini hanya menampilkan proyek `featured` (fallback 3 pertama by `sort_order` bila belum ada yang featured)
- `app/Http/Controllers/ProjectPageController.php` — halaman Projects terpisah: `index()` untuk katalog lengkap `/projects` (paginasi + filter kategori), `show()` untuk detail `/projects/{project_key}` (route-model-binding by `project_key`, lihat `routes/web.php`) + related projects
- `app/Http/Controllers/ContactMessageController.php` — menangani submit form kontak (POST `/contact`)
- `resources/views/layouts/app.blade.php` — layout utama (head dengan title/meta/OG dinamis via `@section('meta_title'|'meta_description'|'meta_image')`, scroll progress bar, ambient background, floating widget, modal global)
- `resources/views/portfolio/index.blade.php` — halaman utama, meng-include semua partial section (tiap section dibungkus pengecekan `section_settings.is_active`)
- `resources/views/portfolio/partials/` — satu file per section (navbar, hero, about, skills, projects [highlight + CTA ke `/projects`], playground, experience, blog, testimonials, contact, footer) + modal (cv-modal, article-modal — modal proyek sudah dicabut di Fase 2, diganti halaman sungguhan)
- `resources/views/projects/index.blade.php`, `resources/views/projects/show.blade.php` — halaman katalog & detail Projects (Fase 2), reuse `layouts.app` yang sama dengan `/`

### Admin panel (`/admin`)
- `app/Http/Controllers/Admin/` — satu controller per menu (`AuthController`, `DashboardController`, `ProfileController`, `SocialLinkController`, `SkillController`, `ProjectController`, `ExperienceController`, `BlogPostController`, `TestimonialController`, `MessageController`, `SectionSettingController`, `PlaceholderController` untuk menu Playground yang memang tanpa data tersimpan)
- `routes/admin.php` — seluruh route `/admin/*`, dilindungi middleware `auth` (kecuali `/admin/login`)
- `resources/views/admin/layouts/app.blade.php` — layout dashboard (sidebar responsif + collapsible di mobile, topbar, flash message, reveal-on-scroll)
- `resources/views/admin/` — satu folder per menu (`profile`, `social-links`, `skills`, `projects`, `experience`, `blog`, `testimonials`, `messages`, `section-settings`), masing-masing berisi `index.blade.php` (list + search/filter/pagination) dan `form.blade.php` (create & edit)

### Model & data (semua di MySQL)
- `app/Models/` — `Project`, `BlogPost`, `Experience`, `Testimonial`, `Skill`, `ContactMessage`, `SiteProfile` (singleton, profil & hero), `SocialLink`, `SectionSetting` (toggle aktif/nonaktif per section), `User` (login admin)
- `database/migrations/` — skema seluruh tabel di atas
- `database/seeders/` — data seed awal (`SkillSeeder`, `ProjectSeeder`, `BlogPostSeeder`, `ExperienceSeeder`, `TestimonialSeeder`, `SiteProfileSeeder`, `SocialLinkSeeder`, `SectionSettingSeeder`, `AdminUserSeeder`)
- `docs/ERD.md` — diagram & catatan skema lengkap

### Shared
- `resources/views/components/icon.blade.php` — komponen ikon SVG inline (pengganti lucide-react), dipakai di halaman publik maupun admin
- `resources/css/app.css` — Tailwind v4 + custom classes (frosted-glass, scrollbar, reveal-on-scroll, dsb.)
- `resources/js/reveal.js` — reveal-on-scroll (IntersectionObserver `[data-reveal]` + komponen Alpine `revealOnScroll`), dipakai publik & admin, di-import oleh KEDUA entry di bawah
- `resources/js/public.js` — entry Vite untuk halaman publik (Iterasi 13/Fase 3): Alpine core + `reveal.js` + `portfolio.js` (store bahasa, store UI/modal, komponen per section publik)
- `resources/js/admin.js` — entry Vite untuk halaman admin (Iterasi 13/Fase 3): Alpine core + `reveal.js` + komponen Alpine khusus admin (mis. `sectionToggle`) — TIDAK mengimpor `portfolio.js`, jadi halaman admin tidak ikut mengunduh logic publik (lang store, playground demo, dst)

## Catatan

- Bahasa ID/EN untuk label UI halaman publik ditulis langsung di Blade dengan dua `<span>` yang ditoggle Alpine (`$store.lang.current`), disimpan ke `localStorage` agar konsisten di seluruh halaman. Admin panel berbahasa Indonesia saja (tidak ada toggle bahasa di admin).
- Konten panjang (deskripsi proyek, isi artikel blog, achievement pengalaman kerja) hanya berbahasa Indonesia, sesuai data sumber asli.
- Form kontak publik melakukan submit sungguhan (POST `/contact`) tersimpan ke tabel `contact_messages` (`is_read = false` secara default), lalu menampilkan pesan sukses via session flash. Pesan masuk dikelola dari menu admin "Pesan Masuk" — tandai dibaca otomatis saat dibuka, hapus dengan konfirmasi.
- Upload gambar (avatar profil, avatar testimoni, cover blog, gambar project) disimpan di `storage/app/public` dan diakses publik lewat symlink `public/storage` (langkah 6 di atas) — field terkait juga menerima URL gambar langsung sebagai alternatif upload file.
- Riwayat lengkap pembangunan admin panel (Iterasi 0-9, Fase 1) dan halaman Projects terpisah (Iterasi 10-12, Fase 2) — lihat `docs/RENCANA-PENGEMBANGAN.md` untuk rencana & keputusan arsitektur, `docs/LOG-ITERASI.md` untuk detail teknis per iterasi.

## Deploy ke Produksi

Ditulis di Iterasi 16 (Fase 3, `docs/RENCANA-OPTIMASI-PERFORMA.md`), setelah diaudit & diuji langsung bahwa ketiga command cache produksi di bawah aman dipakai untuk project ini (tidak ada `env()` dipanggil langsung dari luar `config/*.php`, tidak ada route berbasis closure). Langkah ini di LUAR langkah "Menjalankan Project" di atas (yang untuk development) — jalankan tambahan ini saat deploy ke server produksi sungguhan.

### 1. Environment (`.env` produksi)
- `APP_ENV=production`
- `APP_DEBUG=false` — **wajib**, kalau tetap `true` di produksi, stack trace error (termasuk isi query/credential) bisa bocor ke publik.
- Pastikan `APP_KEY` sudah di-generate (`php artisan key:generate`) dan **tidak** memakai key yang sama dengan environment development/staging.
- Seluruh koneksi eksternal (DB, mail, dsb.) dibaca lewat file `config/*.php` yang memanggil `env(...)`, BUKAN dipanggil `env()` langsung dari controller/model — sudah diverifikasi bersih di Iterasi 16 (lihat `docs/LOG-ITERASI.md`), kondisi ini WAJIB dipertahankan di kode baru manapun supaya `config:cache` di langkah 3 tidak diam-diam menghasilkan nilai `null`.

### 2. Build aset front-end
```bash
npm install
npm run build
```
Pakai `npm run build`, **bukan** `npm run dev` — `build` menghasilkan file production-ready di `public/build/` (minified, ter-hash) yang dibaca `@vite([...])` di Blade lewat `public/build/manifest.json`; `dev` menyalakan Vite dev server (hot-reload) yang tidak dimaksudkan untuk publik.

### 3. Cache produksi Laravel
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
- `config:cache` — menggabungkan seluruh file `config/*.php` (hasil evaluasi `env()`-nya) jadi satu file di `bootstrap/cache/config.php`, supaya Laravel tidak perlu baca ulang `.env` tiap request.
- `route:cache` — mem-build ulang seluruh definisi route jadi satu file cache. Diverifikasi tidak ada route berbasis Closure di `routes/web.php`/`routes/admin.php` (satu closure route placeholder menu "Playground" ditemukan & diperbaiki jadi Controller method di Iterasi 16, lihat `App\Http\Controllers\Admin\PlaceholderController`), jadi command ini aman dipakai.
- `view:cache` — pre-compile semua file Blade (publik & admin) supaya request pertama tidak menanggung biaya compile on-the-fly.
- Project ini tidak punya listener event kustom (`app/Listeners/` tidak ada, `AppServiceProvider::boot()` kosong, Laravel 13 tidak lagi memakai `EventServiceProvider`) — `php artisan event:cache` **opsional**, boleh dijalankan (tidak akan menghasilkan apa pun yang berarti) tapi tidak wajib untuk project ini saat ini. Kalau ke depan ditambah event/listener kustom, sertakan `event:cache` di urutan di atas.

**Setiap kali deploy ulang kode baru**, cache-cache di atas WAJIB di-`:clear` dulu sebelum `:cache` lagi (kalau tidak, perubahan kode/route/view/config yang baru di-deploy tidak akan kepakai — server tetap menyajikan versi cache lama):
```bash
php artisan config:clear && php artisan route:clear && php artisan view:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
```
Laravel 13 menyediakan shortcut gabungan untuk pola di atas:
```bash
php artisan optimize        # setara config:cache + route:cache + view:cache (+ event:cache bila ada listener)
php artisan optimize:clear  # clear semua cache di atas sekaligus
```

### 4. Header cache untuk aset Vite (konfigurasi web server, bukan kode Laravel)
Setiap file di `public/build/assets/*` (hasil `npm run build`) sudah punya hash unik di nama filenya (mis. `app-CXyZ1234.css`, `public-AbCd5678.js`) — setiap kali isi file berubah, nama filenya ikut berubah. Karena itu, file-file ini **aman** diberi header cache jangka panjang & `immutable` oleh web server produksi (Nginx/Apache/Laragon), misalnya:
```
Cache-Control: public, max-age=31536000, immutable
```
Browser tidak akan pernah menyajikan versi lama secara tidak sengaja — kalau isinya berubah, `manifest.json` hasil build otomatis menunjuk ke nama file hash yang baru. Ini rekomendasi konfigurasi di level web server (virtual host/`.htaccess`/`nginx.conf`), bukan sesuatu yang diatur dari kode Laravel — cukup jadi catatan untuk siapa pun yang menyiapkan server produksi nanti; **tidak diubah** di konfigurasi Laragon lokal project ini.

### 5. OPcache PHP (pengaturan `php.ini`, bukan kode aplikasi)
Aktifkan `opcache.enable=1` (dan `opcache.validate_timestamps=0` bila deploy lewat proses build/release yang jelas, supaya OPcache tidak mengecek perubahan file tiap request) di `php.ini` server produksi — mengurangi biaya parse & compile file PHP di tiap request secara signifikan. Ini pengaturan PHP di level server, di luar kendali kode aplikasi, cukup jadi reminder deployment.
