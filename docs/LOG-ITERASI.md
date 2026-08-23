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

## Iterasi 2 — Profil & Hero, Social Links (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "Profil & Hero" (placeholder sejak Iterasi 0) sekarang jadi form edit singleton nyata untuk `site_profiles` (identitas, tagline ID/EN, bio ID/EN, kontak, statistik hero, dan 2 avatar — masing-masing bisa diisi via URL langsung atau upload file dengan preview live). Menu "Social Links" sekarang CRUD penuh (tambah/edit/hapus dgn modal konfirmasi/reorder naik-turun) untuk tabel `social_links`. Halaman publik (`/`) tidak lagi membaca `config('portfolio.*')` sama sekali — `PortfolioController@index` sekarang mengambil `$personalInfo` dari `SiteProfile::current()->toArray()` dan `$socialLinks` dari `SocialLink::where('is_active', true)->orderBy('sort_order')->get()->toArray()`. Karena kedua model fillable-nya cocok 1:1 dengan struktur array config yang lama, seluruh partial publik (`hero`, `about`, `contact`, `social-bar`, `cv-modal`) otomatis ikut baca dari database tanpa perlu diubah satu baris pun — sudah dicek tidak ada partial yang memanggil `config('portfolio...')` langsung.

### File/area utama yang berubah
- `app/Http/Controllers/Admin/ProfileController.php` (baru) — `edit()` (tampilkan form dgn `SiteProfile::current()`) dan `update()` (validasi semua field, `resolveImage()` privat menangani prioritas: file upload > URL yang diketik > nilai lama yang tersimpan, supaya form tidak sengaja mengosongkan avatar kalau kedua field dikosongkan).
- `app/Http/Controllers/Admin/SocialLinkController.php` (baru) — CRUD penuh (`index`, `create`, `store`, `edit`, `update`, `destroy`) + `move()` (tukar `sort_order` dengan tetangga naik/turun — dipilih dibanding drag-and-drop karena daftar pendek ~6 item, jauh lebih sederhana & robust untuk ditest).
- `routes/admin.php` — placeholder `profile` & `social-links` dihapus dari array `$placeholders`; ditambah route `admin.profile` (GET) + `admin.profile.update` (PUT), dan 7 route `admin.social-links*` (index/create/store/edit/update/destroy/move).
- `resources/views/admin/profile/edit.blade.php` (baru) — form terbagi 5 kartu (Identitas, Tagline & Bio, Kontak & Profil Singkat, Statistik Hero, Foto Profil). Toggle "Tersedia untuk proyek baru" pakai pill switch yang sama gayanya dengan Iterasi 1. Avatar: `x-data` per gambar menyimpan `preview` (URL atau `URL.createObjectURL()` saat file dipilih) supaya ada preview langsung tanpa submit.
- `resources/views/admin/social-links/index.blade.php` (baru) — list dgn tombol naik/turun (submit form kecil per baris ke route `move`), badge "Nonaktif" utk link yang di-nonaktifkan, dan modal konfirmasi hapus (pola sama dgn modal publik: backdrop blur + `x-transition`, bukan `confirm()` browser — sesuai keputusan arsitektur di `RENCANA-PENGEMBANGAN.md` #4).
- `resources/views/admin/social-links/form.blade.php` (baru) — dipakai bersama untuk create & edit (`$socialLink->exists` menentukan mode), dropdown ikon dibatasi ke 6 pilihan yang benar-benar didukung `x-icon` (`Github/Linkedin/Twitter/Youtube/Instagram/Mail`, sama seperti `$iconMap` di `social-bar.blade.php`) supaya tidak mungkin memilih ikon yang tidak ada.
- `app/Http/Controllers/PortfolioController.php` — `$personalInfo` & `$socialLinks` sekarang dari database, bukan `config('portfolio.*')` (lihat Ringkasan). Komentar ditambahkan menjelaskan kenapa tidak ada partial lain yang perlu diubah.
- `php artisan storage:link` dijalankan (symlink `public/storage` → `storage/app/public`, belum ada sejak Iterasi 0 karena belum ada fitur upload) — dibutuhkan supaya file avatar yang diupload bisa diakses publik via `/storage/avatars/...`.
- `config/portfolio.php` — **sengaja tidak dihapus/disentuh.** Dicek: sudah tidak direferensikan lagi oleh `app/` maupun `resources/views/` manapun (hanya muncul di 2 baris komentar penjelas di `PortfolioController.php`), TAPI masih dipakai sebagai sumber data awal oleh `SiteProfileSeeder` & `SocialLinkSeeder` (dibaca sekali saat `db:seed`). Sesuai keputusan arsitektur "dihapus setelah dipastikan tidak lagi direferensikan" — karena masih direferensikan oleh seeder, file ini tetap dipertahankan untuk saat ini.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (skema `site_profiles` & `social_links` sudah lengkap sejak Iterasi 0, tidak ada kolom baru yang dibutuhkan Iterasi 2).
- Tidak ada seeder baru dijalankan (data awal sudah ada dari `migrate:fresh --seed` di Iterasi 0).

### Verifikasi
- `php artisan route:list --path=admin` — 22 route, bersih, tidak ada placeholder `profile`/`social-links` tersisa.
- `npm run build` — sukses.
- `php artisan storage:link` — symlink berhasil dibuat.
- End-to-end via `php artisan serve` + `curl` (cookie jar, login `admin@bagusbatra.dev` / `Admin#12345`):
  - `GET /admin/profile` → 200, form termuat dengan nilai `site_profiles` saat ini.
  - `POST /admin/profile` (method-spoofed jadi PUT via `_method`) dengan `tagline_id` bernilai unik `TAGLINE-TEST-UNIK-12345` → 302 redirect sukses; `GET /` sesudahnya mengandung teks tersebut (1 match) — bukti bio/tagline yang diubah di admin langsung tampil di halaman publik.
  - Upload avatar **file sungguhan** (PNG 1x1 dibuat via `php -r`) ke field `avatar_file` → tersimpan ke `storage/app/public/avatars/<hash>.png`, kolom `avatar` di DB terisi `/storage/avatars/<hash>.png`, dan `GET /storage/avatars/<hash>.png` → 200 (file benar-benar bisa diakses publik lewat symlink, bukan cuma tersimpan).
  - `GET /admin/social-links` → 200, 6 social link dari seed (GitHub, LinkedIn, X (Twitter), YouTube, Instagram, Direct Email) semua tampil.
  - `POST /admin/social-links` menambah link baru "TikTok Test Unik" → 302; `GET /` sesudahnya mengandung teks itu (3 match: kartu Hero + kartu integrasi sosial Contact) — bukti tambah social link langsung terlihat.
  - `PATCH /admin/social-links/{id}/move` (`direction=up`) → `sort_order` bertukar dengan tetangganya di atas, dicek via `tinker`.
  - `DELETE /admin/social-links/{id}` untuk link TikTok test → 302; `GET /` sesudahnya **0 match** untuk teks itu — bukti hapus social link langsung hilang dari halaman publik.
  - Seluruh data test dikembalikan ke kondisi awal: `site_profiles` (bio_id/bio_en/phone/github/linkedin/twitter/available_for_work/avatar/secondary_avatar/location) dicocokkan ulang manual field-per-field terhadap nilai asli di `config/portfolio.php` (semua identik, dicek via `tinker` — termasuk koreksi 1 field `location` yang sempat kepotong saat restore parsial), dan `social_links` kembali ke 6 baris dgn `sort_order` 0–5 berurutan tanpa celah (baris "Email" sempat bergeser ke urutan 6 akibat swap+delete berurutan, diperbaiki manual balik ke 5).
  - `storage/logs/laravel.log` dicek setelah seluruh rangkaian di atas — kosong, tidak ada exception baru.
  - Server `php artisan serve` dimatikan setelah verifikasi (dikonfirmasi request setelahnya gagal connect); file scratch (`scratch_avatar.png`, cookie jar) dihapus, `git status` bersih dari sisa file test.

### Commit
- (diisi setelah commit dibuat)

### Catatan untuk review
- **Temuan navbar/footer**: sesuai instruksi, dicek dulu apakah `navbar.blade.php` & `footer.blade.php` memakai `$personalInfo`/`$socialLinks` sebelum diubah — **ternyata tidak sama sekali** (brand name "Bagus.dev" di navbar & footer hardcoded di Blade, bukan dari config/DB; footer juga tidak menampilkan kartu social link apa pun). Jadi tidak ada perubahan yang diperlukan di kedua file itu untuk "membaca dari database" — sudah otomatis tidak bergantung pada sumber data apa pun yang berubah. Dicatat di sini supaya jelas ini bukan terlewat, tapi memang tidak ada yang perlu diubah.
- **Field `github`/`linkedin`/`twitter` di `site_profiles` vs tabel `social_links`**: keduanya sengaja dipertahankan terpisah sesuai skema yang sudah ada sejak Iterasi 0 (bukan keputusan baru di iterasi ini) — `site_profiles.github/linkedin/twitter` dipakai untuk referensi profil singkat (tidak ada di UI publik manapun saat ini secara langsung, field ini ada di form tapi belum ada consumer publik yang membacanya secara eksplisit selain lewat kartu Social Links berbasis tabel terpisah), sedangkan `social_links` adalah sumber data untuk kartu/tombol yang benar-benar tampil di Hero & Contact. Berpotensi duplikasi konsep untuk iterasi mendatang, dicatat sebagai rekomendasi peninjauan (bukan bug, tidak diperbaiki di iterasi ini karena mengubah skema di luar scope).
- Keputusan reorder Social Links pakai tombol naik/turun (bukan drag-and-drop) — cukup untuk daftar pendek, jauh lebih mudah diuji lewat `curl`/otomatis, dan tidak menambah dependency JS baru.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` diupdate hanya di bagian "Riwayat perubahan skema" untuk mencatat hal ini, tanpa entri diagram baru.

---

## Iterasi 1 — Dashboard nyata & Pengaturan Section (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "Pengaturan Section" (placeholder sejak Iterasi 0) sekarang jadi halaman nyata: daftar 9 section dari `section_settings` dengan switch modern (pill toggle, gaya dipakai ulang dari toggle "Backdrop Blur" di `playground.blade.php`), auto-save via `fetch` PATCH tanpa reload halaman. Halaman publik (`/`) sekarang menghormati flag `is_active` — tiap `@include` section di `portfolio/index.blade.php` dibungkus `@if`, termasuk `hero` yang sebelumnya tidak dibungkus di rencana awal iterasi (rencana eksplisit menyebut hero termasuk yang bisa dimatikan). Dashboard diperkaya dengan grid "Menu Cepat" ke 9 menu lain dan link "Kelola" langsung ke halaman Pengaturan Section dari panel status section (statistik jumlah project/blog/dll sudah nyata sejak Iterasi 0, tidak dirombak).

### File/area utama yang berubah
- `app/Http/Controllers/Admin/SectionSettingController.php` (baru) — `index()` (daftar 9 section, urut `sort_order`) dan `toggle()` (route-model-binding `SectionSetting`, flip `is_active`, balas JSON `{success, section_key, is_active}`).
- `routes/admin.php` — route `admin.section-settings` (GET) dan `admin.section-settings.toggle` (PATCH `{sectionSetting}/toggle`) dipindah keluar dari array `$placeholders` ke controller nyata; entri placeholder `section-settings` dihapus. Sidebar (`resources/views/admin/layouts/app.blade.php`) tidak perlu diubah — nama route sudah sama, cuma sekarang resolve ke controller sungguhan.
- `resources/views/admin/section-settings/index.blade.php` (baru) — daftar 9 section, tiap baris `data-reveal`, switch pill Alpine (`x-data="sectionToggle(id, isActive)"`), label Indonesia dari kolom `label`, feedback inline "Tersimpan"/"Gagal, dicoba lagi" yang fade otomatis setelah 2 detik.
- `resources/js/admin.js` (baru) — `Alpine.data('sectionToggle', ...)`: optimistic UI (flip state duluan sebelum respons server), rollback + pesan error kalau `fetch` gagal, sinkron ulang dengan `is_active` dari respons server saat sukses. Diimpor di `resources/js/app.js` (pola sama seperti ekstraksi `reveal.js` di Iterasi 0 — komponen Alpine di file JS terpisah, bukan inline `<script>` di Blade, supaya konsisten dgn konvensi yang sudah ada).
- `resources/views/admin/layouts/app.blade.php` — tambah `<meta name="csrf-token">` di `<head>` (dipakai header `X-CSRF-TOKEN` oleh `fetch` di `admin.js`; sebelumnya tidak ada karena Iterasi 0 belum butuh request AJAX apa pun dari admin).
- `app/Http/Controllers/Admin/DashboardController.php` — tambah `$quickLinks` (9 menu, dikirim ke view).
- `resources/views/admin/dashboard.blade.php` — panel "Status Section Halaman Publik" tidak lagi berlabel "Read-only"; sekarang menampilkan ringkasan "X dari 9 section aktif" + tombol "Kelola" ke halaman Pengaturan Section. Tambah grid "Menu Cepat" (link ke 9 menu admin lain, ikon + label, konsisten gaya kartu yang sudah ada).
- `app/Http/Controllers/PortfolioController.php` — tambah `$sectionActive = SectionSetting::pluck('is_active', 'section_key')`, dikirim ke `portfolio.index`.
- `resources/views/portfolio/index.blade.php` — tiap `@include` section (`hero`, `about`, `projects`, `playground`, `experience`, `blog`, `testimonials`, `contact`) dibungkus `@if ($sectionActive['<key>'] ?? true)`. Fallback `?? true` supaya baris `section_settings` yang belum ke-seed tidak diam-diam menyembunyikan section (fail-open, bukan fail-closed).
- `resources/views/portfolio/partials/about.blade.php` — **tidak dipecah jadi 2 partial.** Dicek dulu strukturnya: "About" dan "Skills" ternyata satu `<section id="about">` fisik yang sama (skills matrix ada di `<div id="skills">` bersarang di dalamnya). Solusi: `@if ($sectionActive['about'] ?? true)` membungkus seluruh `@include` di `index.blade.php` (mematikan "about" otomatis ikut menyembunyikan skills, karena bersarang), dan `@if ($sectionActive['skills'] ?? true)` tambahan membungkus khusus `<div id="skills">` di dalam partial itu sendiri (mematikan "skills" saja menyembunyikan grid keahlian tapi copy About tetap tampil). Kedua toggle jadi punya efek yang jelas dan independen sejauh strukturnya memungkinkan.
- `database/seeders/SectionSettingSeeder.php` — label diubah ke Bahasa Indonesia (mis. "Hero" → "Hero / Beranda", "Skills" → "Keahlian & Tech Stack", dst, 9 baris total) sesuai instruksi. Dijalankan ulang (`php artisan db:seed --class=SectionSettingSeeder`, idempotent via `updateOrCreate`) untuk update label di DB tanpa `migrate:fresh`.
- `docs/RENCANA-PENGEMBANGAN.md` — baris status di baris 3 diupdate dari draft ke "Iterasi 0 selesai" sebelum sesi ini dimulai (perubahan bawaan sesi sebelumnya yang belum ter-commit, dimasukkan ke commit ini karena relevan dan tidak ada commit terpisah untuk itu sebelumnya).

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (tidak ada perubahan skema di iterasi ini).
- `php artisan db:seed --class=SectionSettingSeeder` — update label 9 baris `section_settings` yang sudah ada (idempotent, `is_active` & `sort_order` tidak berubah).

### Verifikasi
- `php artisan route:list --path=admin` — 15 route, bersih, tidak ada placeholder `section-settings` tersisa; `admin.section-settings` (GET) dan `admin.section-settings.toggle` (PATCH) mengarah ke `SectionSettingController`.
- `npm run build` — sukses (`resources/js/admin.js` ikut ter-bundle ke `app-*.js` tanpa error).
- End-to-end via `php artisan serve` + `curl` (cookie jar, login `admin@bagusbatra.dev` / `Admin#12345`):
  - `GET /admin/section-settings` → 200, HTML memuat 9 label Indonesia (`Hero / Beranda`, `Tentang Saya`, `Keahlian & Tech Stack`, `Proyek / Portfolio`, `Interactive Playground`, `Pengalaman Kerja`, `Artikel Blog`, `Testimoni`, `Kontak`) — dicek via grep, semua ketemu.
  - `PATCH /admin/section-settings/8/toggle` (id 8 = `testimonials`, header `X-CSRF-TOKEN` dari meta tag) → 200, JSON `{"success":true,"section_key":"testimonials","is_active":false}`; dicek langsung ke DB via `tinker` → `is_active` = `false`.
  - `GET /` setelah testimonials dimatikan → 200, `grep -io 'testimonial'` di HTML respons → **0 match** (section beserta seluruh markupnya hilang total, bukan cuma disembunyikan CSS).
  - Toggle testimonials kembali ke aktif → DB `is_active` = `true`; `GET /` → `id="testimonials"` muncul lagi (1 match).
  - Diulang untuk section kedua (`playground`, id 5): matikan → `id="playground"` 0 match di `/`; nyalakan lagi → 1 match. Bukan kebetulan.
  - Diuji juga toggle `skills` (id 3, terpisah dari `about`): matikan → `id="skills"` 0 match tapi `id="about"` tetap 1 match (copy About & 4 kartu prinsip tetap tampil, cuma grid keahlian yang hilang) — sesuai desain nested toggle di atas. Dinyalakan lagi setelahnya.
  - Edge case: **seluruh 9 section** dimatikan sekaligus (`SectionSetting::query()->update(['is_active' => false])` via tinker) → `GET /` tetap 200, tidak ada exception (navbar/footer tetap render, halaman jadi kosong di tengah tapi tidak error).
  - State akhir dikembalikan: seluruh 9 section `is_active = true` lagi (dicek ulang via tinker, hasil `hero:1 about:1 skills:1 projects:1 playground:1 experience:1 blog:1 testimonials:1 contact:1`) sebelum sesi verifikasi ditutup.
  - `GET /admin/dashboard` → 200, mengandung teks "Menu Cepat" (grid quick-link baru tampil).
  - `storage/logs/laravel.log` dicek setelah seluruh rangkaian di atas — kosong, tidak ada exception baru.
  - Server `php artisan serve` dimatikan setelah verifikasi (dikonfirmasi request setelahnya gagal connect).

### Commit
- `33db39a` — Iterasi 1: dashboard + toggle aktif/nonaktif section publik

### Catatan untuk review
- **Keputusan UX auto-save vs tombol simpan**: dipilih **auto-save** (opsi default yang disebut di `RENCANA-PENGEMBANGAN.md` bagian 8), bukan tombol "Simpan" terpisah. Alasan: hanya ada 1 field per baris (switch on/off), tidak ada form multi-field yang butuh review sebelum submit, dan pola ini konsisten dengan sifat "saklar" yang secara semantik memang seharusnya langsung berefek begitu diklik (mirip toggle setting di aplikasi lain). UX detail yang ditambahkan sendiri (tidak diminta eksplisit, tapi wajar): optimistic UI (switch berpindah posisi duluan sebelum respons server datang, supaya terasa instan), rollback otomatis + pesan "Gagal, dicoba lagi" kalau request gagal (mis. network error), dan indikator "Tersimpan" yang fade otomatis setelah 2 detik (tidak menumpuk/mengganggu kalau user klik banyak switch berturut-turut).
- **Temuan soal link navbar saat section dimatikan**: dicek `resources/js/portfolio.js` (`appRoot.scrollTo()` dan `appRoot.onScroll()`) — keduanya sudah pakai `document.getElementById(id)` dengan pengecekan `if (el)` sebelum dipakai, jadi **tidak ada bug**: kalau section dimatikan dan elemennya hilang dari DOM, klik link navbar ke section itu cukup jadi no-op (tidak error, tidak scroll ke mana-mana) dan scroll-spy otomatis skip section itu saat highlight active-nya. Tidak ada perubahan diperlukan di `navbar.blade.php` untuk iterasi ini — link tetap selalu ditampilkan semua, sesuai batasan yang diminta. Catatan: `testimonials` sebenarnya sudah sejak awal **tidak ada** di `$navLinks` navbar (beda dari 8 section lain) — bukan perubahan dari iterasi ini, sudah begitu sejak partial navbar dibuat sebelum Iterasi 0; dicatat di sini sekadar temuan, bukan sesuatu yang perlu diperbaiki.
- Keputusan implementasi "about" vs "skills" (satu partial fisik, dua flag) dijelaskan lengkap di bagian "File/area utama yang berubah" di atas — didokumentasikan di sana, bukan diulang di sini, supaya tetap satu sumber kebenaran.
- Tidak ada perubahan skema database di iterasi ini (`section_settings` sudah lengkap sejak Iterasi 0) — `docs/ERD.md` diupdate hanya pada bagian "Riwayat perubahan skema" untuk mencatat bahwa Iterasi 1 tidak mengubah struktur tabel apa pun, tanpa entri diagram baru.

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
