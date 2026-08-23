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
