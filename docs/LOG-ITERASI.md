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

## Iterasi 7 — Testimonials (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "Testimonials" (placeholder sejak Iterasi 0) sekarang CRUD penuh — model paling sederhana di antara Iterasi 4-7 (tidak ada kolom JSON/repeater), jadi form-nya jauh lebih ringkas. Fitur baru yang belum ada di CRUD sebelumnya: **star rating picker** interaktif (klik bintang untuk set rating 1-5, hover preview sebelum diklik) menggantikan input angka biasa. `testimonial_key` dibuat otomatis dari `name + company` dan immutable, pola sama dengan iterasi CRUD sebelumnya.

### File/area utama yang berubah
- `app/Http/Controllers/Admin/TestimonialController.php` (baru) — CRUD penuh + `move()` + `index()` dgn search (nama/perusahaan) + filter rating (dropdown 1-5) + pagination.
- `routes/admin.php` — placeholder `testimonials` dihapus dari `$placeholders`; ditambah 7 route `admin.testimonials*`.
- `resources/views/admin/testimonials/index.blade.php` (baru) — pola identik list CRUD lain, tambahan render bintang rating (`@for` loop icon `star` terisi sejumlah `$item->rating`) per baris, filter dropdown rating 5→1 bintang.
- `resources/views/admin/testimonials/form.blade.php` (baru) — **star rating picker**: 5 tombol ikon bintang, `x-data` menyimpan `rating` (nilai tersimpan) & `hoverRating` (preview saat hover, direset ke 0 saat mouse keluar area); ikon terisi (`fill-amber-400`) bila `(hoverRating || rating) >= i`, memberi feedback visual instan tanpa perlu klik dulu. Nilai final dikirim lewat `<input type="hidden" name="rating" :value="rating">`. Avatar pakai pola upload+URL yang sama dgn iterasi sebelumnya.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (skema `testimonials` sudah lengkap sebelum Fase 1).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `php artisan route:list --path=admin/testimonials` — 7 route bersih, tidak ada placeholder tersisa.
- `npm run build` — sukses.
- End-to-end via `php artisan serve` + `curl` (cookie jar, login admin):
  - `GET /admin/testimonials` → 200, 3 testimoni seed tampil (`Testimonial::count()` = 3, cocok).
  - `POST /admin/testimonials` menambah "Testimoni Test Unik Orion" (rating 4) → 302; dicek DB via `tinker`: `rating` tersimpan `4` persis, `testimonial_key` ter-generate `testimoni-test-unik-orion-test-corp-unik`. `GET /` sesudahnya mengandung teks itu (2 match) — bukti tambah langsung tampil.
  - `PUT /admin/testimonials/{id}` mengubah nama jadi "...EDITED" & rating jadi 5 → 302; `GET /` mengandung nama baru; `testimonial_key` dicek tetap sama (tidak berubah), `rating` di DB terkonfirmasi `5`.
  - `GET /admin/testimonials?rating=5` → hanya testimoni dgn rating 5 yang tampil (termasuk testimoni test yang baru diedit).
  - `DELETE /admin/testimonials/{id}` untuk testimoni test → 302; `Testimonial::count()` kembali 3; `GET /` → 0 match — bukti hapus langsung hilang.
  - `storage/logs/laravel.log` dicek setelah seluruh rangkaian — kosong, tidak ada exception baru.
  - Server dimatikan setelah verifikasi (dikonfirmasi request gagal connect).

### Commit
- (diisi setelah commit dibuat)

### Catatan untuk review
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` diupdate hanya di bagian "Riwayat perubahan skema".

---

## Iterasi 6 — Blog (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "Blog" (placeholder sejak Iterasi 0) sekarang CRUD penuh — sesuai perkiraan di `RENCANA-PENGEMBANGAN.md`, ini iterasi paling kompleks di sisi form karena kolom `sections` adalah array objek dengan sub-objek opsional (`codeSnippet`) dan field opsional (`tip`). Diselesaikan dalam satu langkah (tidak perlu dipecah sub-langkah terpisah seperti diantisipasi rencana — kompleksitasnya masih tertangani dengan pola repeater Alpine yang sama dipakai di Iterasi 4/5). `post_key` **dan** `slug` sama-sama dibuat otomatis dari judul saat create dan immutable setelahnya (pola sama dengan `project_key`/`experience_key`).

### File/area utama yang berubah
- `app/Http/Controllers/Admin/BlogPostController.php` (baru) — CRUD penuh + `move()` + `index()` dgn search judul + filter kategori + pagination. Bagian paling rumit: `validated()` merekonstruksi tiap baris `sections[i]` dari field flat form (`heading`, `body`, `tip`, `code_language`, `code_filename`, `code_code`) menjadi struktur JSON asli (`{heading, body, codeSnippet?: {language, filename, code}, tip?}`) — baris section kosong (heading & body sama-sama kosong) di-skip, `codeSnippet` hanya disertakan bila salah satu dari 3 field code-nya diisi, `tip` hanya disertakan bila diisi. Ini menjaga bentuk JSON yang tersimpan tetap identik dengan yang dipakai seeder (field opsional benar-benar hilang dari array saat kosong, bukan string kosong).
- **Keputusan `post_key` & `slug` immutable**: sama alasan dengan Iterasi 4/5 (`project_key`/`experience_key`) — keduanya dibuat sekali dari `Str::slug($title)` saat create (unik, dengan suffix `-2` dst bila bentrok) dan tidak bisa diedit dari form. `slug` sendiri saat ini belum dikonsumsi di mana pun pada halaman publik yang sudah ada (dicek: tidak muncul di `article-modal.blade.php`/`blog.blade.php` — kemungkinan disiapkan untuk halaman detail blog di Fase 2), tapi tetap diberi perlakuan sama (auto-generate + immutable + unique) karena kolomnya sudah `unique` di skema (lihat `docs/ERD.md`) sehingga harus diisi konsisten sejak sekarang.
- `routes/admin.php` — placeholder `blog` dihapus dari `$placeholders`; ditambah 7 route `admin.blog*` (parameter route `{post}` mengikuti nama variabel `BlogPost $post` di controller untuk implicit model binding).
- `resources/views/admin/blog/index.blade.php` (baru) — pola identik list Projects/Experience (search+filter+reorder+modal hapus+pagination), tambahan kolom info "N bagian" (jumlah `sections`) per baris.
- `resources/views/admin/blog/form.blade.php` (baru) — form terpanjang sejauh ini: Informasi Dasar, Tags (repeater flat), Penulis & Gambar (2 pasang upload+preview: cover image & avatar penulis, pola sama dgn avatar Iterasi 2), dan Sections (repeater bersarang — tiap baris adalah kartu berisi heading, body, 2 field code (bahasa+nama file), textarea code (tampil bergaya editor gelap `bg-slate-900`), dan tip). Field code/tip sengaja **selalu ditampilkan** (bukan disembunyikan di belakang toggle "punya code?") untuk menyederhanakan Alpine state — kosongkan saja bila section itu tidak butuh code snippet/tip, konsisten dgn instruksi placeholder di textarea-nya.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (skema `blog_posts` sudah lengkap sebelum Fase 1, termasuk kolom JSON `tags`/`sections`).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `php artisan route:list --path=admin/blog` — 7 route bersih, tidak ada placeholder tersisa.
- `npm run build` — sukses.
- End-to-end via `php artisan serve` + `curl` (cookie jar, login admin):
  - `GET /admin/blog` → 200, 4 artikel seed tampil (`BlogPost::count()` = 4, cocok).
  - `POST /admin/blog` menambah "Artikel Test Unik Quantum" dgn 2 sections (satu lengkap dgn code+tip, satu tanpa keduanya) → 302; dicek langsung ke DB via `tinker`: JSON `sections` tersimpan persis sesuai desain — section pertama punya `codeSnippet` & `tip`, section kedua **hanya** punya `heading`+`body` (kunci `codeSnippet`/`tip` benar-benar tidak ada, bukan kosong). `post_key` & `slug` sama-sama ter-generate `artikel-test-unik-quantum`. `GET /` sesudahnya mengandung judul artikel test (4 match) — bukti tambah artikel langsung tampil.
  - `PUT /admin/blog/{id}` mengubah judul jadi "...EDITED" → 302; `GET /` mengandung judul baru; `post_key` dicek tetap `artikel-test-unik-quantum` (tidak berubah).
  - `DELETE /admin/blog/{id}` untuk artikel test → 302; `BlogPost::count()` kembali 4; `GET /` → 0 match — bukti hapus langsung hilang.
  - `storage/logs/laravel.log` dicek setelah seluruh rangkaian — kosong, tidak ada exception baru.
  - Server dimatikan setelah verifikasi (dikonfirmasi request gagal connect).

### Commit
- `0e6c87d` — Iterasi 6: CRUD Blog dengan repeater sections (heading/body/code/tip)

### Catatan untuk review
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` diupdate hanya di bagian "Riwayat perubahan skema".

---

## Iterasi 5 — Experience (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "Experience" (placeholder sejak Iterasi 0) sekarang CRUD penuh untuk tabel `experiences` — timeline karier di halaman publik. Sama seperti Iterasi 4, `experience_key` dibuat otomatis dari `company + role` saat create dan immutable setelahnya (alasan sama: dipakai sebagai DOM id `experience-item-{key}` di halaman publik). Repeater Alpine dipakai untuk `achievements` dan `skills` (keduanya array string flat, pola identik dgn `tags`/`highlights` di Iterasi 4). List admin punya search (role atau perusahaan) + filter tipe kerja (dropdown terisi otomatis dari nilai `type` yang sudah ada di DB via `distinct()`, bukan daftar hardcode) + pagination.

### File/area utama yang berubah
- `app/Http/Controllers/Admin/ExperienceController.php` (baru) — CRUD penuh + `move()` + `index()` dgn search (role/company, `orWhere`) dan filter `type` + pagination. `uniqueKey()` sama pola dgn `ProjectController@uniqueKey`.
- `routes/admin.php` — placeholder `experience` dihapus dari `$placeholders`; ditambah 7 route `admin.experience*`.
- `resources/views/admin/experience/index.blade.php` (baru) — pola identik list Projects/Social Links/Skills (search+filter, reorder naik/turun, badge Featured, modal konfirmasi hapus, pagination Tailwind bawaan).
- `resources/views/admin/experience/form.blade.php` (baru) — field `type` pakai `<input list="...">` (datalist HTML native) berisi 5 saran umum (Full-Time/Part-Time/Contract/Freelance/Internship) tapi tetap free-text (bukan `<select>` dibatasi) karena field ini murni teks tampilan tanpa logika filter publik yang bergantung padanya — beda dgn `category` Project/Skill yang memang dipakai untuk filter pill di halaman publik sehingga perlu dibatasi ke enum. Achievements & Skills masing-masing repeater Alpine sederhana (array string, `:name="achievements[${index}]"` / `skills[${index}]"`).

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (skema `experiences` sudah lengkap sebelum Fase 1).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `php artisan route:list --path=admin/experience` — 7 route bersih, tidak ada placeholder tersisa.
- `npm run build` — sukses.
- End-to-end via `php artisan serve` + `curl` (cookie jar, login admin):
  - `GET /admin/experience` → 200, 3 experience seed tampil (`Experience::count()` = 3, cocok).
  - `POST /admin/experience` menambah "Experience Test Unik Engineer" (company "Test Company Unik", 1 achievement, 1 skill) → 302; `GET /` sesudahnya mengandung teks itu (2 match) — bukti tambah langsung tampil di timeline.
  - `PUT /admin/experience/{id}` mengubah role jadi "...EDITED" → 302; `GET /` mengandung role baru.
  - `GET /admin/experience?search=Unik` → hanya experience test yang cocok tampil.
  - `DELETE /admin/experience/{id}` untuk experience test → 302; `Experience::count()` kembali 3; `GET /` → 0 match — bukti hapus langsung hilang.
  - `storage/logs/laravel.log` dicek setelah seluruh rangkaian — kosong, tidak ada exception baru.
  - Server dimatikan setelah verifikasi (dikonfirmasi request gagal connect).

### Commit
- `5c086bd` — Iterasi 5: CRUD Experience dengan repeater achievements & skills

### Catatan untuk review
- Field `featured` di `experiences` ada di skema & form admin, tapi **tidak dipakai untuk badge apapun** di `experience.blade.php` publik saat ini (beda dgn Project yang punya badge "Featured" jelas di kartu). Ini bukan bug yang diperkenalkan iterasi ini — sudah begitu sejak sebelum Fase 1 (kolom sudah ada di skema tapi partial publik tidak pernah merendernya). Dicatat sebagai temuan; tidak diperbaiki karena mengubah tampilan publik di luar scope "CRUD admin" iterasi ini — bisa jadi rekomendasi kecil untuk Iterasi 9 (Polish & QA) bila ingin dikonsistenkan dengan Project.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` diupdate hanya di bagian "Riwayat perubahan skema".

---

## Iterasi 4 — Projects (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "Projects" (placeholder sejak Iterasi 0) sekarang CRUD penuh — iterasi paling kompleks di sisi form sejauh ini karena harus menampung 4 struktur data berulang (`tags`, `metrics`, `highlights`, `tech_stack` per 4 kelompok: frontend/backend/database/cloudAndDevOps), sesuai persis field yang dipakai kartu grid & modal case-study publik. List admin dilengkapi search judul, filter kategori, dan pagination Laravel bawaan (Tailwind). Upload gambar mendukung URL langsung atau file dengan preview, sama seperti pola avatar Iterasi 2.

### File/area utama yang berubah
- `app/Http/Controllers/Admin/ProjectController.php` (baru) — CRUD penuh + `move()` (pola sama Iterasi 2/3) + `index()` dengan `search`/`category` query filter dan `paginate(10)->withQueryString()`. Konstanta `CATEGORIES` (5 kategori nyata: Full-Stack, Frontend, UI/UX & Systems, Open Source, AI & Tools — sengaja **tidak** termasuk `"All"` karena itu murni pseudo-kategori filter di `projects.blade.php`, bukan kategori project sungguhan) dipakai bersama oleh dropdown filter admin & form create/edit supaya selalu sinkron. `validated()` privat membersihkan baris repeater kosong (mis. baris tag yang ditambah lalu tidak diisi) sebelum disimpan sebagai JSON.
- **Keputusan `project_key` immutable**: dibuat sekali otomatis dari `Str::slug($title)` saat create (dengan suffix `-2`, `-3`, dst bila bentrok), **tidak bisa diedit** dari form sama sekali (field tidak ditampilkan sebagai input, hanya ditampilkan read-only di form edit). Alasan: `project_key` adalah "referensi stabil ... terpisah dari id auto-increment" (lihat `docs/ERD.md` bagian Catatan penting) yang dipakai di banyak tempat sebagai DOM id (`project-card-{key}`, `view-study-{key}`, dst) — mengizinkannya berubah saat judul diedit berisiko memutus referensi itu tanpa manfaat nyata bagi user admin.
- `routes/admin.php` — placeholder `projects` dihapus dari `$placeholders`; ditambah 7 route `admin.projects*` (index/create/store/edit/update/destroy/move).
- `resources/views/admin/projects/index.blade.php` (baru) — search box + dropdown kategori (form GET, query string dipertahankan lewat `withQueryString()`), list dgn thumbnail gambar, badge Featured, tombol naik/turun, modal konfirmasi hapus, dan `{{ $projects->links() }}` (pagination Tailwind bawaan Laravel — tidak perlu publish/kustomisasi view vendor, sudah Tailwind secara default).
- `resources/views/admin/projects/form.blade.php` (baru) — form terbesar sejauh ini: 6 kartu (Informasi Dasar, Gambar, Tags, Metrics, Highlights, Tech Stack 4-grup, Link & Tampilan). Semua repeater pakai Alpine `x-for` di atas array biasa (`tags`, `highlights`) atau array objek (`metrics: [{label,value}]`, `tech: {frontend:[],backend:[],database:[],cloudAndDevOps:[]}`), dengan `:name` dibind eksplisit per-index (`tags[${index}]`, `metrics[${index}][label]`, `tech_stack[frontend][${index}]`) — sengaja pakai indeks eksplisit (bukan `name="tags[]"` polos) supaya urutan array & pemetaan label↔value pada `metrics` selalu benar terlepas dari urutan field dalam body request. Warna aksen pakai `<input type="color">` & `<input type="text">` yang sama-sama `x-model="accentColor"` supaya selalu sinkron dua arah.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (skema `projects` sudah lengkap sejak sebelum Fase 1, termasuk kolom JSON `tags`/`metrics`/`highlights`/`tech_stack`).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `php artisan route:list --path=admin/projects` — 7 route bersih, tidak ada placeholder tersisa.
- `npm run build` — sukses.
- End-to-end via `php artisan serve` + `curl` (cookie jar, login admin):
  - `GET /admin/projects` → 200, 5 project seed tampil (`Project::count()` = 5, cocok). `GET /admin/projects/create` → 200.
  - `POST /admin/projects` menambah "Project Test Unik Nebula" dgn 2 tags, 1 metric, 1 highlight, tech_stack frontend+backend → 302; dicek langsung ke DB via `tinker`: `project_key` ter-generate otomatis jadi `project-test-unik-nebula`, `tags`/`metrics`/`highlights`/`tech_stack` tersimpan sebagai JSON persis sesuai input (termasuk 2 grup tech_stack kosong `database`/`cloudAndDevOps` otomatis `[]`, bukan `null`). `GET /` sesudahnya mengandung teks "Project Test Unik Nebula" (3 match: judul kartu, JSON `x-data`, DOM id) — bukti tambah project langsung tampil.
  - `PUT /admin/projects/{id}` mengubah judul jadi "...EDITED" → 302; `GET /` mengandung judul baru; **`project_key` dicek tetap `project-test-unik-nebula`** (tidak berubah walau judul diedit) — sesuai keputusan immutability di atas.
  - `GET /admin/projects?search=Nebula` → hanya 1 project cocok tampil (project lain seperti "Lumina Analytics" tidak muncul); `GET /admin/projects?category=Frontend` → menampilkan kedua project kategori Frontend (Aurora + project test) — filter & search berfungsi independen maupun bersamaan.
  - `DELETE /admin/projects/{id}` untuk project test → 302; `Project::count()` kembali 5; `GET /` → 0 match — bukti hapus langsung hilang.
  - `PATCH /admin/projects/{id}/move` (`direction=up`) → `sort_order` bertukar dgn tetangga, dicek via `tinker`; dikembalikan manual ke urutan seed semula setelah verifikasi.
  - `storage/logs/laravel.log` dicek setelah seluruh rangkaian — kosong, tidak ada exception baru.
  - Server dimatikan setelah verifikasi (dikonfirmasi request gagal connect).

### Commit
- `ec7d49b` — Iterasi 4: CRUD Projects dengan repeater tags/metrics/highlights/tech_stack

### Catatan untuk review
- Field `client` sengaja dibiarkan nullable di form (beberapa project publik memang tanpa client / "Open Project" ditampilkan di modal via fallback `x-text="... || 'Open Project'"` yang sudah ada di `project-modal.blade.php` — tidak diubah, form admin tinggal mengikuti perilaku itu).
- Reorder pakai tombol naik/turun (bukan drag-and-drop), konsisten dengan Social Links (Iterasi 2) & Skills (Iterasi 3) — pola yang sama dipertahankan di seluruh CRUD admin untuk konsistensi UX antar menu (relevan untuk audit konsistensi di Iterasi 9 nanti, walau Iterasi 9 di luar cakupan permintaan saat ini).
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` diupdate hanya di bagian "Riwayat perubahan skema".

---

## Iterasi 3 — Skills / About (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "About & Skills" (placeholder sejak Iterasi 0) sekarang CRUD penuh untuk tabel `skills` — dipakai grid "Tech Stack & Skills Matrix" di section About halaman publik. Form skill: kategori (dropdown dibatasi ke 4 nilai yang benar-benar dipakai filter pill publik: frontend/backend/devops/tools), level 0-100 lewat slider **dan** input angka yang saling sinkron via Alpine (`x-model.number`), dropdown ikon dibatasi ke 12 ikon yang benar-benar didukung `x-icon` (identik dengan `$skillIconMap` di `about.blade.php`), dan reorder naik/turun (pola sama seperti Social Links di Iterasi 2). Karena `PortfolioController@index` sudah mengambil `$skills` dari `Skill::orderBy('sort_order')->get()` sejak sebelum Fase 1 dimulai (bukan dari config), CRUD ini langsung tersambung ke halaman publik tanpa perlu perubahan apa pun di controller publik atau partial `about.blade.php`.

### File/area utama yang berubah
- `app/Http/Controllers/Admin/SkillController.php` (baru) — CRUD penuh + `move()` (swap `sort_order` dgn tetangga, identik pola dgn `SocialLinkController@move` Iterasi 2).
- `routes/admin.php` — placeholder `about-skills` dihapus dari `$placeholders`; ditambah 7 route `admin.about-skills*` (index/create/store/edit/update/destroy/move).
- `resources/views/admin/skills/index.blade.php` (baru) — list dgn badge kategori berwarna, tombol naik/turun, modal konfirmasi hapus (pola identik Iterasi 2). Ditambah banner info yang menjelaskan status 4 kartu "Prinsip Kerja" (lihat Catatan di bawah).
- `resources/views/admin/skills/form.blade.php` (baru) — dipakai bersama create & edit; slider `<input type="range">` dan `<input type="number">` sama-sama `x-model.number="level"` sehingga selalu sinkron dua arah, progress bar preview live di bawahnya mengikuti `:style="width: ${level}%"`.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (skema `skills` sudah lengkap sejak sebelum Fase 1).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `php artisan route:list --path=admin/about-skills` — 7 route bersih, tidak ada placeholder tersisa.
- `npm run build` — sukses.
- End-to-end via `php artisan serve` + `curl` (cookie jar, login admin):
  - `GET /admin/about-skills` → 200, 12 skill seed tampil (dicek jumlah via `Skill::count()` = 12, cocok).
  - `POST /admin/about-skills` menambah "Skill Test Unik Rust" (kategori backend) → 302; `GET /` sesudahnya mengandung teks itu (1 match) — bukti tambah skill langsung tampil di grid.
  - `PUT /admin/about-skills/{id}` mengubah nama jadi "...EDITED" & level 88 → 302; `GET /` mengandung nama baru (1 match) — bukti edit langsung tampil.
  - Filter pill kategori (`skill-filter-backend`, dll di halaman publik) dicek masih ada & utuh sebelum dan sesudah operasi CRUD — tidak rusak.
  - `DELETE /admin/about-skills/{id}` untuk skill test → 302; `Skill::count()` kembali 12; `GET /` → 0 match untuk nama skill test — bukti hapus langsung hilang.
  - `PATCH /admin/about-skills/{id}/move` (`direction=up`) pada skill urutan ke-2 → `sort_order` bertukar dgn skill pertama, dicek via `tinker`; dikembalikan manual ke urutan semula (`sort_order` 0/1) setelah verifikasi supaya urutan Skills di halaman publik tidak berubah dari baseline seed.
  - `storage/logs/laravel.log` dicek — kosong, tidak ada exception baru.
  - Server dimatikan setelah verifikasi (dikonfirmasi request gagal connect).

### Commit
- `a84d593` — Iterasi 3: CRUD Skills (About & Skills) dengan reorder dan slider level

### Catatan untuk review
- **Keputusan 4 kartu "Prinsip Kerja"**: sesuai pertanyaan terbuka di `RENCANA-PENGEMBANGAN.md` bagian 8 ("dikonfirmasi di Iterasi 3"), diputuskan **tetap statis** (opsi default yang disebutkan di rencana), tidak dijadikan CRUD. Alasan: konten itu (Performance-First, Aksesibilitas & WAI-ARIA, Arsitektur Modular, Micro-Interactions) adalah pernyataan filosofi kerja yang jarang berubah dan bukan "data" dalam pengertian CRUD biasa (tidak ada listing/kategori/relasi) — membuatnya jadi tabel terpisah akan menambah kompleksitas skema untuk 4 baris yang praktis statis. Ditambahkan banner info di halaman admin About & Skills yang menjelaskan status ini secara eksplisit ke pengguna admin, supaya tidak membingungkan kenapa 4 kartu itu tidak muncul di CRUD. Bisa direvisit di iterasi lanjutan bila diminta.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` diupdate hanya di bagian "Riwayat perubahan skema".

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
- `0ba5fcf` — Iterasi 2: form Profil & Hero + CRUD Social Links, halaman publik baca DB

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
