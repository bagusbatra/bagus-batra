# Rencana Pengembangan — Admin Panel & Perapian Data

Status: **Iterasi 0–9 selesai & terverifikasi — Fase 1 TUNTAS SEPENUHNYA.** Dikerjakan berurutan secara otomatis tanpa jeda review per-iterasi sesuai instruksi user (jeda review antar-iterasi yang dijelaskan di bagian 2 ditangguhkan untuk rentang ini) — rincian tiap iterasi ada di `LOG-ITERASI.md`, perubahan skema di `ERD.md`. Iterasi 9 (Polish & QA lintas admin) sudah dikerjakan: audit konsistensi/responsif/animasi selesai, regresi penuh publik+admin hijau, README diperbarui. Siap lanjut ke **Fase 2** (perapian data index/projects/detail project) — detail Fase 2 akan disusun terpisah nanti, belum dimulai.
Dibuat: 2026-08-23

## 1. Tujuan

Fase 1: membangun **halaman admin** untuk mengelola seluruh konten yang tampil di halaman publik (`/`) secara CRUD penuh — satu menu per section halaman index — lengkap dengan halaman login, filter, pagination, dan saklar aktif/nonaktif per section.

Fase 2 (menyusul setelah Fase 1 selesai & disetujui): merapikan data & tampilan halaman index, membuat halaman listing **Projects** terpisah, dan halaman **detail Project** (saat ini masih berupa modal on-page). Detail Fase 2 akan ditulis ulang lebih rinci di dokumen ini setelah seluruh iterasi Fase 1 tuntas — belum dirinci sekarang agar tidak jadi asumsi basi.

## 2. Cara kerja dokumen ini

- Dokumen ini (`RENCANA-PENGEMBANGAN.md`) adalah rencana hidup — bisa direvisi kapan pun sebelum/antara iterasi bila ada perubahan keputusan.
- `LOG-ITERASI.md` di folder yang sama diupdate **setiap kali satu iterasi selesai** dikerjakan (ringkasan perubahan, file yang disentuh, command yang dijalankan, hasil verifikasi).
- `ERD.md` di folder yang sama diupdate **setiap kali ada perubahan skema database** (tabel/kolom baru, relasi baru).
- Alur kerja: 1 iterasi = 1 unit kerja yang dieksekusi otomatis sampai selesai & terverifikasi → update LOG & ERD → **berhenti** menunggu review/persetujuan Anda → lanjut ke iterasi berikutnya setelah ada instruksi lanjut.
- Iterasi tidak dieksekusi mundur/dilewat — urutan di bawah adalah urutan pengerjaan.

## 3. Kondisi proyek saat ini (baseline)

Aplikasi publik sudah berupa Laravel 11 (Blade + Alpine.js + Tailwind v4), data disimpan di SQLite dengan tabel: `projects`, `blog_posts`, `experiences`, `testimonials`, `skills`, `contact_messages`. Data profil & social links masih di `config/portfolio.php` (bukan database). Section **Interactive Playground** murni demo interaktif client-side (spring physics, design tokens, optimistic UI) — **tidak punya data tersimpan**, jadi di admin hanya butuh saklar aktif/nonaktif, tanpa form CRUD konten.

Sudah ada mekanisme reveal-on-scroll di `resources/js/portfolio.js` (IntersectionObserver pada elemen `[data-reveal]`, dipicu dari komponen Alpine `appRoot`) — akan **dipakai ulang** di admin panel untuk animasi masuk saat scroll, bukan dibuat dari nol.

## 4. Keputusan arsitektur untuk Fase 1

Diputuskan sekarang (dengan asumsi wajar, akan dikonfirmasi/dikoreksi saat review Iterasi 0) agar pengerjaan bisa langsung jalan otomatis sesuai instruksi:

| Area | Keputusan |
|---|---|
| Database | Pindah dari SQLite ke **MySQL** via Laragon. Nama DB: `bagus_batra_portfolio`, host `127.0.0.1`, port `3306`, user `root`, password kosong (default Laragon). **Perlu dikonfirmasi**: pastikan service MySQL Laragon sedang aktif sebelum migrasi dijalankan. |
| Autentikasi admin | Pakai tabel `users` bawaan Laravel sebagai akun admin (tanpa halaman registrasi publik — akun admin hanya dibuat lewat seeder). Guard default `web`, middleware `auth` melindungi seluruh route `/admin/*`. Tidak pakai Breeze/Jetstream penuh (terlalu banyak fitur publik yang tidak perlu) — dibangun custom minimal: halaman login, logout, proteksi route. |
| Kredensial admin awal | Diseed via `AdminUserSeeder`: email `admin@bagusbatra.dev`, password `Admin#12345` — **wajib diganti setelah login pertama kali**, akan diingatkan di dokumen ini juga. |
| Data profil & social links | Dipindah dari `config/portfolio.php` ke tabel database (`site_profiles` singleton + `social_links`) supaya bisa diedit lewat admin. `config/portfolio.php` akan dipensiunkan setelah data dipindah (tidak dihapus paksa di iterasi awal, dihapus setelah dipastikan tidak lagi direferensikan). |
| Saklar aktif/nonaktif section | Tabel baru `section_settings` (key per section: hero, about, skills, projects, playground, experience, blog, testimonials, contact). Navbar & footer tidak disertakan sebagai section yang bisa dimatikan (elemen struktural halaman). View publik akan mengecek flag ini sebelum merender tiap section. |
| UI Kit admin | Blade + Alpine.js + Tailwind v4 (konsisten dengan stack publik), dibangun custom (bukan template admin pihak ketiga) supaya gaya visual selaras dengan tema frosted-glass yang sudah ada, tapi dengan layout dashboard: sidebar + topbar, bukan single-page scroll. |
| Upload gambar | Disimpan di `storage/app/public` via `php artisan storage:link`, form admin pakai `<input type="file">` + preview sebelum submit. Field image yang sekarang berupa URL Unsplash tetap didukung (opsional isi URL langsung ATAU upload file). |
| List/Table | Komponen Blade reusable untuk tabel data: search box, filter dropdown (kategori/status), sorting kolom sederhana, dan pagination Laravel (`paginate()` + view Tailwind bawaan). Dipasang di semua list yang datanya berpotensi banyak: Projects, Blog, Experience, Testimonials, Skills, Contact Messages. Social Links (hanya ~6 item) tidak perlu pagination, cukup list + search ringan. |
| Animasi | Reveal-on-scroll pakai ulang mekanisme `[data-reveal]` yang sudah ada di `portfolio.js` (di-load juga di layout admin). Transisi buka/tutup modal, dropdown, dan toggle switch pakai Alpine `x-transition`. |
| Konfirmasi hapus data | Modal konfirmasi sebelum delete (bukan `confirm()` browser bawaan) — konsisten dengan gaya modal yang sudah ada di halaman publik. |

Semua keputusan di atas bisa dikoreksi kapan saja — cukup sampaikan koreksinya, tidak perlu menunggu sampai akhir Fase 1.

## 5. Struktur menu admin (1 menu = 1 section index)

1. **Dashboard** — ringkasan jumlah data per section, status section aktif/nonaktif, pesan kontak belum dibaca.
2. **Profil & Hero** — form edit `site_profiles` (nama, tagline ID/EN, bio ID/EN, lokasi, kontak, avatar, statistik hero: tahun pengalaman, jumlah project, dst). Singleton — tidak ada create/delete, hanya edit.
3. **Social Links** — CRUD penuh (tambah/edit/hapus/urutkan platform sosial media).
4. **About & Skills** — CRUD `skills` (kategori frontend/backend/devops/tools, level, ikon). *4 kartu "prinsip kerja" di section About saat ini masih konten statis di Blade (tidak berasal dari data), diusulkan tetap statis di Fase 1 kecuali Anda minta dijadikan CRUD juga — akan dikonfirmasi saat Iterasi 3.*
5. **Projects** — CRUD penuh (judul, deskripsi, kategori, metrics, highlights, tech stack per layer, tags, link demo/github, gambar, featured, urutan tampil).
6. **Playground** — tidak ada form data (section ini murni demo interaktif tanpa konten tersimpan); menu ini hanya berisi saklar aktif/nonaktif dari halaman Settings.
7. **Experience** — CRUD penuh (periode, role, perusahaan, achievement, skill terkait, featured).
8. **Blog** — CRUD penuh, termasuk pengelolaan section-section artikel (heading, body, code snippet, tip) sebagai form dinamis (tambah/hapus blok section).
9. **Testimonials** — CRUD penuh (nama, role, perusahaan, isi testimoni, rating, tag project).
10. **Pesan Masuk (Contact)** — daftar pesan dari form kontak publik, tandai dibaca/belum, hapus, filter status.
11. **Pengaturan Section** — switch modern aktif/nonaktif per section publik (`section_settings`), berlaku langsung ke halaman index.
12. **Login / Logout** — halaman login admin terpisah dari layout dashboard.

## 6. Rincian iterasi

Setiap iterasi ditutup dengan: migrasi dijalankan bersih, seeder (bila ada) jalan, halaman terkait diverifikasi jalan tanpa error (dites lewat `php artisan serve` + curl atau cek log), commit git dengan pesan jelas, lalu update `LOG-ITERASI.md` (+ `ERD.md` jika skema berubah).

### Iterasi 0 — Fondasi
- Pindah `.env` dari SQLite ke MySQL (Laragon), pastikan `php artisan migrate` berhasil ke DB baru.
- Migration & model baru: `site_profiles`, `social_links`, `section_settings`; seeder mengisi dari `config/portfolio.php` saat ini (`SiteProfileSeeder`, `SocialLinkSeeder`, `SectionSettingSeeder`).
- Seeder `AdminUserSeeder` (akun admin awal).
- Auth admin: route `/admin/login` (GET/POST), `/admin/logout` (POST), middleware `auth` untuk grup route `/admin/*`, redirect ke login bila belum autentikasi.
- Layout dasar admin: `resources/views/admin/layouts/app.blade.php` (sidebar responsif + collapsible di mobile, topbar dgn nama admin & logout, area konten, flash message banner, reveal-on-scroll aktif).
- Dashboard shell minimal (statistik dummy/nyata dasar) supaya layout bisa diverifikasi end-to-end.
- Sidebar menampilkan seluruh 12 menu di atas (menu yang belum ada formnya ditandai "segera" sampai iterasinya sendiri tuntas).

**Selesai bila**: bisa login di `/admin/login`, melihat dashboard dengan sidebar lengkap, logout berfungsi, akses `/admin/*` tanpa login redirect ke login, DB sudah MySQL.

### Iterasi 1 — Dashboard nyata & Pengaturan Section
- Dashboard menampilkan angka nyata (jumlah project, blog, experience, testimonial, skill, pesan belum dibaca) + status tiap section (aktif/nonaktif).
- Halaman **Pengaturan Section**: daftar section dengan switch modern (toggle animasi smooth), tersimpan real-time (atau simpan tombol — diputuskan saat implementasi mana yang lebih baik UX-nya) ke `section_settings`.
- Update `resources/views/portfolio/index.blade.php` (+ partials terkait) agar tiap section dibungkus pengecekan `is_active` dari `section_settings`.

**Selesai bila**: mematikan sebuah section di admin membuat section itu hilang dari `/` tanpa error, dan sebaliknya.

### Iterasi 2 — Profil & Hero, Social Links
- Form edit **Profil & Hero** (singleton `site_profiles`), termasuk upload avatar.
- CRUD **Social Links** (list + form tambah/edit + hapus dgn konfirmasi + urutkan).
- Update partial `hero`, `navbar`, `footer`, `social-bar` publik agar membaca dari database, bukan lagi `config/portfolio.php`.

**Selesai bila**: mengubah bio/tagline/avatar di admin langsung berubah di halaman index; menambah/menghapus social link langsung terlihat.

### Iterasi 3 — Skills (About)
- CRUD penuh `skills` (kategori, level 1-100 dgn slider/input angka, ikon — dropdown pilihan ikon yang sudah didukung `x-icon`, urutan tampil).
- Konfirmasi keputusan soal 4 kartu "prinsip kerja" (statis vs CRUD) sebelum lanjut ke iterasi berikutnya.

**Selesai bila**: tambah/edit/hapus skill langsung terlihat di grid Skills halaman index, filter kategori tetap berfungsi.

### Iterasi 4 — Projects
- CRUD penuh dengan sub-form berulang (repeater Alpine) untuk: `tags`, `metrics` (label+value), `highlights`, `tech_stack` per kelompok (frontend/backend/database/cloud & devops).
- Upload gambar project + preview, pilihan featured, urutan tampil, filter kategori & pencarian judul di list admin, pagination.

**Selesai bila**: seluruh field yang tampil di modal case-study publik bisa diedit dari admin dan berubah sesuai di halaman index.

### Iterasi 5 — Experience
- CRUD penuh (periode, role, perusahaan, lokasi, tipe kerja, deskripsi, achievements sebagai repeater, skills terkait, featured, urutan). Filter & pagination di list admin.

### Iterasi 6 — Blog
- CRUD artikel + pengelolaan `sections` sebagai form dinamis berulang (tiap blok: heading, body, code snippet [bahasa, filename, kode], tip) — tambah/hapus/urutkan blok.
- Upload cover image, filter kategori, pencarian judul, pagination di list admin.

**Ini iterasi paling kompleks di sisi form** — kemungkinan besar dipecah sub-langkah saat eksekusi bila diperlukan, tetap dilaporkan sebagai satu iterasi utuh saat selesai.

### Iterasi 7 — Testimonials
- CRUD penuh (nama, role, perusahaan, avatar upload, isi, rating bintang, tag project, urutan). Filter & pagination.

### Iterasi 8 — Pesan Masuk (Contact)
- Tambah kolom `is_read` di `contact_messages`.
- List pesan dgn filter (belum dibaca/semua), tandai dibaca otomatis saat dibuka, hapus dgn konfirmasi, pagination.

### Iterasi 9 — Polish & QA lintas admin
- Audit konsistensi: setiap list punya search/filter/pagination yang seragam gaya & perilakunya.
- Audit responsif (mobile/tablet) seluruh halaman admin.
- Audit animasi reveal-on-scroll & transisi modal/toggle konsisten di semua halaman.
- Regresi penuh: jalankan ulang seluruh alur publik (`/`) memastikan tidak ada yang rusak akibat perubahan Fase 1.
- Update `README.md` dengan instruksi setup MySQL, kredensial admin default, cara migrate+seed.
- Commit akhir Fase 1.

## 7. Yang TIDAK termasuk Fase 1 (sengaja ditunda)

- Multi-admin / role & permission bertingkat (hanya 1 akun admin).
- Reset password via email (kredensial admin diganti manual/lewat tinker bila lupa).
- Halaman listing & detail Projects terpisah dari modal (masuk **Fase 2**).
- Perapian ulang copywriting/desain halaman index (masuk **Fase 2**).

## 8. Pertanyaan terbuka yang akan dikonfirmasi saat berjalan

- Apakah 4 kartu "prinsip kerja" di About perlu jadi CRUD juga, atau tetap statis? (dikonfirmasi di Iterasi 3)
- Apakah toggle Pengaturan Section perlu tombol "Simpan" eksplisit atau auto-save saat switch diklik? (default: auto-save, dikoreksi bila kurang pas)
- Apakah perlu halaman "ubah password admin" di Fase 1, atau cukup lewat `php artisan tinker`? (default: disediakan halaman sederhana ubah password di Iterasi 0/9, bisa disesuaikan)

---

# Fase 2 — Halaman Projects Terpisah & Perapian Data

Status: **Draft — dieksekusi otomatis berurutan (Iterasi 10→12) sesuai instruksi user "lanjut ke fase 2", tanpa jeda review per-iterasi**, mengikuti pola yang sama seperti Fase 1. Ditulis: 2026-08-23, setelah Fase 1 (Iterasi 0-9) tuntas.

## 9. Tujuan Fase 2

Saat ini, satu-satunya cara melihat detail sebuah project adalah lewat modal on-page ("Detail Case Study") di section Projects pada halaman index — tidak bisa di-bookmark, tidak bisa dibagikan sebagai link langsung, dan tidak SEO-friendly. Fase 2 mengubah ini jadi halaman sungguhan:

- **Halaman listing Projects** (`/projects`) — katalog lengkap semua project, terpisah dari halaman index.
- **Halaman detail Project** (`/projects/{project_key}`) — halaman penuh per project, menggantikan modal.
- **Merapikan halaman index** — section Projects di index jadi highlight/preview (bukan menampilkan seluruh project), dengan CTA ke halaman listing lengkap; sekaligus audit & rapikan data/tampilan yang mungkin masih kurang pas setelah migrasi ke database di Fase 1.

## 10. Keputusan arsitektur untuk Fase 2

| Area | Keputusan |
|---|---|
| Routing | `GET /projects` → listing (nama route `projects.index`). `GET /projects/{project:project_key}` → detail (nama route `projects.show`, route-model-binding pakai kolom `project_key`, bukan `id`, supaya URL rapi mis. `/projects/lumina-saas`). |
| Modal case-study di index | **Dihapus** (`project-modal.blade.php` + Alpine store terkait), digantikan navigasi sungguhan ke `/projects/{key}`. Tombol "Detail Case Study" di kartu project (baik di index maupun listing) jadi `<a>` biasa ke halaman detail, bukan lagi trigger modal. Alasan: URL bisa dibagikan/di-bookmark, SEO meta per project, tombol back browser bekerja natural — sejalan dengan tujuan "merapikan". |
| Konten halaman detail | Mereplikasi SEMUA konten yang sebelumnya ada di 3 tab modal (Overview & Solusi, Arsitektur Stack, Simulasi Interaktif) sebagai satu halaman penuh — tab tetap dipertahankan sebagai UI pattern (Alpine, tanpa reload) di dalam halaman, karena kontennya memang natural terbagi 3 kelompok. Tambahan: breadcrumb (Beranda → Projects → judul project), tombol "Kembali ke Semua Proyek", dan blok "Project Lainnya" (2-3 project lain sebagai related-link, exclude project yang sedang dibuka). |
| Section Projects di index | Diubah jadi highlight: tampilkan hanya project dengan `featured = true` (fallback: 3 project pertama berdasar `sort_order` bila featured kosong), filter kategori tetap ada tapi beroperasi di dalam subset featured itu, ditambah tombol CTA "Lihat Semua Proyek" menuju `/projects`. Kartu project tetap sama persis secara visual (tidak dirombak), hanya sumber data & tujuan tombol detail yang berubah. |
| Halaman listing `/projects` | Reuse layout/partial navbar+footer+ambient background dari halaman index (bukan dibuat dari nol), grid kartu project SEMUA data (bukan hanya featured), filter kategori (sama seperti yang ada di index), pagination Laravel bila project makin banyak (>12 misalnya — sekarang cuma 5, tapi pola pagination tetap dipasang sejak awal supaya scalable), dukung toggle bahasa ID/EN & tema visual (frosted-glass) yang identik dengan situs utama. |
| Section toggle | Section `projects` di `section_settings` sekarang mengontrol **blok highlight di index**, bukan mematikan `/projects` & `/projects/{key}` (halaman project tetap bisa diakses langsung via URL meski highlight di index dimatikan — konsisten dengan halaman lain yang tetap "ada" walau tidak ditonjolkan di navigasi utama). |
| SEO & meta | Halaman detail project pakai `<title>` dan `<meta name="description">` dinamis dari `title`/`tagline` project, plus Open Graph tag dasar (`og:title`, `og:description`, `og:image` dari `image` project) — perbaikan nyata dibanding modal yang tidak terindeks search engine. |
| Data cleanup (index) | Audit menyeluruh isi halaman index pasca-migrasi Fase 1: pastikan tidak ada teks "null"/kosong yang bocor ke tampilan (mis. kalau admin belum isi field opsional), gambar broken (URL tidak valid) ditangani dengan fallback placeholder, line-clamp/truncation teks masih rapi untuk konten terpanjang di database saat ini, label kategori & badge konsisten kapitalisasi/ejaannya, tautan demo/github yang kosong tidak menampilkan tombol kosong. |

## 11. Rincian iterasi Fase 2

### Iterasi 10 — Routing & Halaman Listing Projects
- Controller publik baru `App\Http\Controllers\ProjectPageController` (atau tambah method di `PortfolioController` — putuskan yang lebih rapi saat implementasi) dengan method `index` untuk `/projects`.
- View `resources/views/projects/index.blade.php` — reuse layout utama (navbar, footer, ambient background, lang store), grid semua project + filter kategori + pagination.
- Update section Projects di `resources/views/portfolio/partials/projects.blade.php`: filter ke featured-only/3 pertama, tambah tombol "Lihat Semua Proyek" ke `/projects`, tombol "Detail Case Study" tiap kartu diarahkan (sementara masih boleh ke modal lama) — modal baru benar-benar dicabut di Iterasi 11 sekalian dengan detail page supaya tidak ada state transisi yang rusak.
- **Selesai bila**: `/projects` menampilkan seluruh project dengan filter & pagination berfungsi, index menampilkan subset featured + CTA yang benar mengarah ke `/projects`.

### Iterasi 11 — Halaman Detail Project & Pencabutan Modal
- Route `GET /projects/{project:project_key}`, controller method `show`.
- View `resources/views/projects/show.blade.php` — replikasi penuh 3 tab modal (Overview, Arsitektur, Preview) sebagai halaman, breadcrumb, tombol kembali, blok related projects, meta tag SEO/OG dinamis.
- Cabut `project-modal.blade.php` dan seluruh wiring Alpine terkait (store, `@json` embed project list untuk modal) dari layout & partial projects — ganti tombol "Detail Case Study" di SEMUA tempat (index highlight & listing `/projects`) jadi `<a href="{{ route('projects.show', $project) }}">`.
- **Selesai bila**: klik "Detail Case Study" di manapun membuka halaman penuh `/projects/{key}` (bukan modal), URL bisa direfresh langsung & tetap menampilkan konten yang sama, tidak ada sisa kode modal project yang mati (dead code) di JS/Blade.

### Iterasi 12 — Merapikan Data & Audit Akhir
- Audit & perbaikan sesuai baris "Data cleanup (index)" di bagian 10 — jalankan pengecekan terhadap seluruh data project/blog/testimonial/skill/experience yang ada saat ini di database (bukan cuma index, sekalian cek halaman `/projects` & `/projects/{key}` baru).
- Pastikan section toggle (`section_settings`) tetap bekerja benar untuk model baru ini (mematikan highlight index tidak mematikan `/projects`).
- Regresi penuh: halaman index, `/projects`, `/projects/{key}` untuk SETIAP project yang ada, toggle bahasa ID/EN di ketiga jenis halaman, responsif mobile/tablet, reveal-on-scroll konsisten di halaman baru, admin CRUD Projects (Iterasi 4 lama) dicek masih sinkron dengan struktur baru (mis. tombol "Lihat di halaman publik" dari admin, kalau ada, mengarah ke URL yang benar — tambahkan kalau belum ada, ini quick-win yang masuk akal).
- Update `README.md` bila ada route/halaman baru yang perlu didokumentasikan.
- **Selesai bila**: seluruh regresi hijau, tidak ada dead code modal project tersisa, data yang tampil di 3 jenis halaman publik (index/listing/detail) bersih & konsisten.

## 12. Yang TIDAK termasuk Fase 2 (sengaja ditunda)

- Halaman listing/detail terpisah untuk Blog (artikel blog TETAP pakai modal seperti sekarang — hanya Projects yang diubah, sesuai instruksi eksplisit "halaman indeks, halaman projects, dan halaman detail projects").
- Comment system, related-content algorithm yang canggih (related projects cukup random/kategori sama, bukan rekomendasi pintar).
- Sitemap.xml / robots.txt / structured data (JSON-LD) — perbaikan SEO dasar (title/meta/OG) saja yang termasuk scope ini.
