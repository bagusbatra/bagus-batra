# Log Iterasi — Admin Panel & Perapian Data

Log ini diupdate otomatis setiap satu iterasi di `RENCANA-PENGEMBANGAN.md` selesai dikerjakan dan terverifikasi. Urutan entri dari yang terbaru di paling atas.

Format tiap entri:

```
## Iterasi N — <judul> (selesai: YYYY-MM-DD)
Status: Selesai / Selesai dengan catatan

### Ringkasan
...

### File/area utama yang berubah
- ...

### Migrasi & seeder dijalankan
- ...

### Verifikasi
- ...

### Commit
- <hash pendek> — <pesan commit>

### Catatan untuk review
- ...
```

---

## Iterasi 0 — Fondasi (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Membangun fondasi admin panel: pindah database dari SQLite ke MySQL (Laragon), menambahkan 3 tabel baru (`site_profiles`, `social_links`, `section_settings`) diisi dari `config/portfolio.php` agar datanya identik dengan tampilan publik saat ini, akun admin awal, sistem auth custom (bukan Breeze/Jetstream) untuk `/admin/*`, layout dashboard (sidebar + topbar responsif, reveal-on-scroll dipakai ulang), dan dashboard nyata pertama (statistik jumlah data + status 8 section, read-only). 10 menu admin lain diarahkan ke halaman placeholder "Segera Hadir" yang menyebutkan iterasi pemiliknya, sehingga sidebar 12 menu sudah lengkap dan tidak ada yang 404. View publik (`resources/views/portfolio/**`) sama sekali tidak diubah — masih membaca dari `config('portfolio.*')` seperti sebelumnya, sesuai batasan Iterasi 0.

### File/area utama yang berubah
- `.env` — `DB_CONNECTION` dari `sqlite` ke `mysql` (host `127.0.0.1:3306`, db `bagus_batra_portfolio`, user `root`, password kosong). File ini gitignored, tidak masuk commit.
- `bootstrap/app.php` — `redirectGuestsTo()` ke `admin.login` dan `redirectUsersTo()` ke `admin.dashboard` untuk middleware `auth`/`guest` bawaan Laravel.
- `database/migrations/2026_08_23_051324_create_site_profiles_table.php`, `..._051325_create_social_links_table.php`, `..._051326_create_section_settings_table.php`.
- `app/Models/SiteProfile.php` (dengan accessor statis `SiteProfile::current()` — `firstOrCreate(['id' => 1])`), `app/Models/SocialLink.php`, `app/Models/SectionSetting.php`.
- `database/seeders/SiteProfileSeeder.php`, `SocialLinkSeeder.php`, `SectionSettingSeeder.php`, `AdminUserSeeder.php` (idempotent — `updateOrCreate` by email) — semua didaftarkan di `database/seeders/DatabaseSeeder.php` (seeder lama tidak diubah).
- `app/Http/Controllers/Admin/AuthController.php`, `DashboardController.php`, `PlaceholderController.php`.
- `routes/admin.php` (baru, di-require dari `routes/web.php`) — prefix `admin`, name prefix `admin.`, route login/logout guest-only, dan 11 route lain (dashboard + 10 placeholder) di bawah middleware `auth`.
- `resources/views/admin/auth/login.blade.php`, `resources/views/admin/layouts/app.blade.php`, `resources/views/admin/dashboard.blade.php`, `resources/views/admin/placeholder.blade.php`.
- `resources/views/components/icon.blade.php` — tambah 2 ikon (`user`, `log-out`) untuk sidebar admin, mengikuti pola ikon yang sudah ada.
- `resources/js/reveal.js` (baru) — logic `[data-reveal]` IntersectionObserver diekstrak keluar dari `appRoot` di `resources/js/portfolio.js`, jadi Alpine component reusable `revealOnScroll` yang dipakai admin layout, sekaligus dipanggil ulang oleh `appRoot.initReveal()` di halaman publik (tanpa duplikasi logic, tanpa membawa scroll-spy/floating-widget publik ke admin).
- `resources/js/app.js` — import `./reveal` di samping `./portfolio`.

### Migrasi & seeder dijalankan
- `php artisan migrate:fresh --seed` ke MySQL (`bagus_batra_portfolio`) — 12 tabel berhasil dibuat (9 tabel lama tanpa perubahan skema + 3 tabel baru), seluruh seeder (`SkillSeeder`, `ProjectSeeder`, `BlogPostSeeder`, `ExperienceSeeder`, `TestimonialSeeder`, `SiteProfileSeeder`, `SocialLinkSeeder`, `SectionSettingSeeder`, `AdminUserSeeder`) jalan tanpa error.
- Tidak ditemukan isu kompatibilitas tipe data SQLite→MySQL pada migration lama (semua kolom `string`/`text`/`json`/`boolean`/`unsignedInteger`/`unsignedTinyInteger` standar Laravel, tidak ada penyesuaian yang diperlukan).

### Verifikasi
- `php artisan route:list --path=admin` — 14 route terdaftar bersih (2 guest: `admin.login` GET/POST; 12 auth: `admin.logout`, `admin.dashboard`, 10 placeholder).
- `npm run build` — sukses, tidak ada error bundling setelah ekstraksi `reveal.js`.
- End-to-end via `php artisan serve` + `curl` (cookie jar):
  - `GET /admin/login` (tanpa cookie) → 200, HTML mengandung form login.
  - `GET /admin/dashboard` (tanpa login) → 302 redirect ke `/admin/login`.
  - `POST /admin/login` dengan `admin@bagusbatra.dev` / `Admin#12345` (CSRF token diambil dari halaman login) → 302 redirect ke `/admin/dashboard`.
  - `GET /admin/dashboard` (dengan cookie sesi hasil login) → 200; angka pada kartu statistik (Projects 5, Blog Posts 4, Experience 3, Testimonials 3, Skills 12, Pesan Masuk 0) cocok persis dengan hasil `Model::count()` langsung dari DB; 9 section (`hero`…`contact`) tampil semua berstatus Aktif.
  - `GET /admin/dashboard` dengan cookie jar kosong (belum login) → 302 redirect ke `/admin/login`, konsisten dengan poin di atas.
  - `GET /admin/projects` (placeholder, dengan login) → 200, mengandung teks "Segera Hadir".
  - `GET /` (halaman publik) → 200, tidak ada regresi setelah pindah ke MySQL.
  - `storage/logs/laravel.log` dicek — tidak ada exception baru (file kosong/1 baris).
  - Server `php artisan serve` dimatikan setelah verifikasi selesai (dikonfirmasi port sudah tidak merespons).

### Commit
- `56b3c0e` — Iterasi 0: fondasi admin panel — MySQL, auth, layout, dashboard shell

### Catatan untuk review
- **PENTING — ganti password admin default.** Kredensial awal: email `admin@bagusbatra.dev`, password `Admin#12345`. Wajib diganti setelah login pertama kali (belum ada halaman ubah password di Iterasi 0 — sesuai rencana, bisa lewat `php artisan tinker` untuk sementara, atau ditambahkan sebagai halaman kecil di iterasi berikutnya bila diinginkan).
- Penyimpangan kecil dari rencana asli / keputusan teknis tambahan yang diambil selama eksekusi (tidak ada yang mengubah scope, semua murni detail implementasi):
  1. Struktur route placeholder: alih-alih membuat satu controller method per menu, dipakai satu `PlaceholderController@show(title, iterationNote)` yang dipanggil dari closure per route di `routes/admin.php` (menghindari fragilitas `Route::defaults()` untuk parameter yang tidak muncul di URI). Tidak mengubah perilaku yang terlihat user — tetap 200 + pesan "Segera Hadir" yang jelas menyebut iterasinya.
  2. Redirect guest/auth dikonfigurasi lewat `Middleware::redirectGuestsTo()` **dan** `redirectUsersTo()` di `bootstrap/app.php` (bukan cuma guest saja) — perlu ditambahkan supaya admin yang sudah login dan membuka `/admin/login` diarahkan ke `admin.dashboard`, bukan fallback default Laravel yang mengarah ke `/` (default `RedirectIfAuthenticated` mencari route bernama `dashboard`/`home`, sementara route kita bernama `admin.dashboard`).
  3. `git status` menunjukkan direktori project sebenarnya **sudah** berupa git repo (2 commit sebelumnya: konversi React→Laravel dan checkpoint awal), berbeda dari asumsi awal instruksi. Commit Iterasi 0 dibuat sebagai commit ketiga di branch `master` yang sama.
  4. `docs/` (termasuk `RENCANA-PENGEMBANGAN.md` ini) belum pernah di-commit sebelumnya — akan masuk ke commit terpisah bersama update `LOG-ITERASI.md` dan `ERD.md` ini, mengikuti urutan "commit kode → update LOG/ERD" yang dijelaskan di `RENCANA-PENGEMBANGAN.md` bagian 6.
- Sesuai batasan Iterasi 0: belum ada toggle section yang berfungsi ke halaman publik (Iterasi 1), view publik belum baca dari `site_profiles`/`social_links` (Iterasi 2), dan belum ada form CRUD apapun (Iterasi 3+) — seluruhnya menunggu instruksi lanjut sebelum dikerjakan.
