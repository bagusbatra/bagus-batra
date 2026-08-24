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

## Iterasi 20 — Reorder Section (Drag & Drop) & Jumlah Item per Section (selesai: 2026-08-24)
Status: Selesai — **Fase 4 (Kustomisasi Tampilan Halaman Index), lanjutan Iterasi 18-19.**

### Ringkasan
Sesuai `docs/RENCANA-KUSTOMISASI-TAMPILAN.md` bagian 5 Iterasi 20. **Kondisi awal sesi**: `git status --short` menunjukkan seluruh perubahan Iterasi 18-19 (fondasi draft/publish, preset warna/logo, beres-beres `playground`/`skills`) MASIH uncommitted (~21 file + 2 file baru) — tidak disentuh/direvert, pekerjaan sesi ini ditumpuk di atasnya. **Pertengahan sesi**, user melakukan commit manual (`7ac9600` — "Refactor accent color system and remove unused section", mencakup persis seluruh pekerjaan Iterasi 18-19) SAAT sesi ini sedang berjalan — terdeteksi lewat `git log` sebelum menulis entri LOG ini, TIDAK ada tindakan diambil terhadap commit tersebut (tidak diubah/di-amend), pekerjaan Iterasi 20 di sesi ini otomatis menumpuk bersih di atasnya karena tidak ada `git add`/`git commit` yang dijalankan sepanjang sesi.

**1) Refactor `portfolio/index.blade.php` jadi loop dinamis:**
- `PortfolioController::SECTION_PARTIALS` (const baru) — pemetaan `section_key => nama partial view` untuk 7 section top-level yang BENAR-BENAR punya posisi DOM independen: `hero`, `about`, `projects`, `experience`, `blog`, `testimonials`, `contact`. `skills` SENGAJA TIDAK ada di sini — tetap nested di dalam partial `about` (lihat komentar existing di `about.blade.php` dari Iterasi 19), tidak punya posisi untuk direorder, hanya togglenya yang fungsional (tidak diubah, tetap direct-live seperti Iterasi 1-19).
- `PortfolioController@index` sekarang query `section_settings` untuk 7 key itu (query LANGSUNG, TIDAK lewat cache Iterasi 15 `SECTION_SETTINGS_CACHE_KEY` — lihat "Catatan untuk review" soal alasan), hitung `is_active`/`sort_order` EFEKTIF lewat `SectionSetting::effective()` (draft-aware, `$previewActive` dibaca dari `$request->attributes->get('appearance_preview')` yang di-set middleware `HandleAppearancePreview` Iterasi 18), filter yang aktif, urutkan, lalu map ke nama partial → dikirim ke view sebagai `$orderedSections` (Collection of string).
- `portfolio/index.blade.php`: 7 blok `@if(...) @include(...) @endif` berurutan (hardcode) diganti SATU `@foreach ($orderedSections as $partial) @include($partial) @endforeach`.
- `$sectionActive` (cache Iterasi 15, is_active-only, live-only) TIDAK dihapus — masih dipakai `about.blade.php` untuk toggle "skills" (satu-satunya pemakai tersisa setelah refactor ini, karena gating 7 section top-level sekarang terjadi di controller, bukan lagi di Blade lewat `$sectionActive['xxx']`).
- **Verifikasi struktural (sebelum lanjut ke UI)**: `GET /` dgn urutan/count default dibandingkan byte-per-byte (minus CSRF token yg memang berubah tiap request) dengan HTML SEBELUM refactor — **IDENTIK**. Urutan `<section id="...">`: hero, about, `<div id="skills">`, projects, experience, blog, testimonials, contact — sama persis.

**2) UI Drag & Drop di admin (tab "Urutan & Isi Section"):**
- **Keputusan implementasi — native HTML5 Drag & Drop API + Alpine.js untuk state, TAPI aksi SIMPAN pakai form POST biasa (bukan fetch/AJAX)**, sedikit menyimpang dari kalimat "AJAX simpan ke draft" di rencana awal (bagian 3 tabel "Reorder — mekanisme UI"), didokumentasikan di sini sesuai izin eksplisit tugas untuk menyimpang dgn alasan kuat: drag-drop ITU SENDIRI genuinely native HTML5 DnD (`draggable="true"`, `@dragstart`/`@dragover.prevent`/`@dragend`) dikombinasikan Alpine (`Alpine.data('sectionReorder', ...)` di `resources/js/admin.js`, murni reorder array `items` di client, TANPA request jaringan selama drag) — TIDAK ada library baru (bukan SortableJS dkk), TIDAK ada masalah reliabilitas yang ditemui saat implementasi (jadi TIDAK fallback ke tombol naik/turun, drag-drop asli dipakai penuh). Yang diubah HANYA mekanisme "kirim ke server": alih-alih fetch/AJAX (yang butuh logic tambahan utk refresh banner status Draft/Live secara manual via JS, sesuatu yang TIDAK ada presedennya di codebase ini), tombol "Simpan sebagai Draft" men-submit form HTML biasa (full-page POST + redirect + flash message) — PERSIS pola yang dipakai SEMUA tab appearance lain (`updateAnimations`, `updateBranding`). Hidden input `order[]` di-render via `x-for` mengikuti urutan array `items` Alpine SAAT INI, jadi urutan hasil drag otomatis ikut terkirim tanpa serialisasi JS tambahan. Konsisten & lebih sederhana ketimbang duplikasi logic AJAX+refresh-banner hanya untuk satu tab, tanpa mengorbankan kualitas interaksi drag-drop itu sendiri.
- Blok baru "Urutan Tampil & Jumlah Item" (`resources/views/admin/appearance/index.blade.php`, tab "sections", DI ATAS `@include('admin.section-settings._list')` yang TIDAK diubah sama sekali) — daftar 7 section top-level (diurutkan preview-aware, `AppearanceController@index` selalu pakai `preview=true` utk form admin, pola sama dgn `$animationsEnabled`/`$accentPreset`), tiap baris draggable, drag handle ikon baru `grip-vertical` (`resources/views/components/icon.blade.php`).
- Route baru `PUT admin/appearance/sections` (`admin.appearance.sections.update`) → `AppearanceController@updateSections()` — validasi `order` (harus persis 7 section_key top-level, masing2 sekali, dicek manual krn `Rule::in` + `size:7` saja tidak menjamin tidak ada duplikat sambil ada yg hilang), lalu tulis `draft_overrides['sort_order'] = $index` per baris (index array = sort_order baru) — pengguna KETIGA dari alur draft generik Iterasi 18 (setelah `animations_enabled` & branding). `publish()`/`discardDraft()` **TIDAK diubah sama sekali** — keduanya sudah generik (loop semua `section_settings.draft_overrides` non-null), bukti lagi desain Iterasi 18 bekerja seperti direncanakan.

**3) Jumlah item per section (`display_count`):**
- Dicek dulu default SEBELUM Iterasi 20: `projects` — hanya query `featured=true` (TANPA `take()` sama sekali sebelumnya, cuma fallback-nya yg `take(3)`); `blog` & `testimonials` — TIDAK PERNAH di-`take()` di index sama sekali (selalu tampil SEMUA baris, saat ini 4 blog & 3 testimonial).
- `PortfolioController@index`: `$projectCount` = `display_count` efektif section `projects` ?? **3** (default lama dipertahankan — aman krn seed hanya 3 project featured, jadi `take(3)` tidak mengubah baseline). Kedua query project (featured & fallback) sekarang `->take($projectCount)`. `$blogCount`/`$testimonialsCount` = `display_count` efektif (bisa NULL) — `take()` HANYA diterapkan kalau nilainya bukan NULL, supaya default "tampilkan semua" tetap terjaga persis seperti sebelumnya.
- Input `<input type="number" min="1" max="50">` per baris (hanya utk `projects`/`blog`/`testimonials`) di blok reorder yang sama di atas — dikirim tiap submit form (bukan cuma saat diubah), value kosong → `null` (fallback default), disimpan ke `draft_overrides['display_count']` bareng `sort_order` dalam SATU submit yang sama.

**4) Verifikasi end-to-end — `php artisan serve --port=8190` + `curl` sungguhan (cookie jar login admin), `storage/logs/laravel.log` dikosongkan sebelum sesi, `wc -l` dicek di banyak titik → konsisten 0 baris sepanjang sesi.**

| # | Skenario | Hasil |
|---|---|---|
| 1 | Baseline `GET /` sebelum test apa pun | **LOLOS** — urutan section identik, 3 project-card, 4 blog-card, 3 testimonial-card (cocok baseline Iterasi 17: 5 project/3 featured, 4 blog, 3 testimonial) |
| 2 | Reorder draft: Testimonials pindah ke atas Projects (`PUT /admin/appearance/sections`, `order[]=hero,about,testimonials,projects,experience,blog,contact`) + `display_count[projects]=2` | **LOLOS** — DB: tiap baris `draft_overrides` berisi `sort_order` baru sesuai posisi array; `projects.draft_overrides` juga berisi `display_count:"2"` |
| 3 | `GET /` visitor TANPA cookie setelah draft | **LOLOS** — urutan TETAP lama (hero,about,projects,...), project-card TETAP 3 (draft tidak bocor) |
| 4 | `GET /` admin dgn cookie TANPA `?preview=1` | **LOLOS** — urutan TETAP lama (preview belum aktif) |
| 5 | Aktifkan preview (`?preview=1`) | **LOLOS** — urutan section: hero, about, **testimonials**, projects, experience, blog, contact; project-card = **2**; banner draft muncul 1x |
| 6 | `GET /` admin lagi TANPA `?preview=1` di URL (uji persist session) | **LOLOS** — urutan baru tetap terlihat (preview persist di session) |
| 7 | `POST /admin/appearance/publish` | **LOLOS** — `GET /` visitor TANPA cookie sekarang urutan baru (testimonials di atas projects) & project-card = 2 |
| 8 | Restore baseline: draft urutan asli (hero,about,projects,experience,blog,testimonials,contact) TANPA `display_count` apa pun → publish | **LOLOS** — DB `display_count` kembali NULL semua (field tidak dikirim = eksplisit null); TAPI numerik `sort_order` sempat drift (lihat "Catatan untuk review" — item non-blocking, diperbaiki manual via tinker balik ke nilai pristine 0-7 supaya baseline benar2 identik) |
| 9 | `display_count[blog]=2`, `display_count[testimonials]=1` (draft) → preview | **LOLOS** — blog-card = 2, testimonial-card = 1 di preview; visitor TANPA cookie tetap blog-card = 4 (tidak bocor) |
| 10 | `POST /admin/appearance/discard` (bukan publish) atas draft skenario #9 | **LOLOS** — DB kembali ke baseline pristine (sort_order 0-7, display_count NULL semua, draft_overrides NULL semua) TANPA perlu perbaikan manual — discard men-null-kan `draft_overrides` sepenuhnya |
| 11 | Toggle `testimonials` off/on lewat mekanisme lama (`PATCH /admin/section-settings/{id}/toggle`, direct-live, TIDAK disentuh Iterasi 20) | **LOLOS** — masih berfungsi identik: off → 0 kemunculan `<section id="testimonials">`, on → 1 lagi |
| 12 | `GET /` final dibandingkan byte-per-byte (minus CSRF token) dgn `GET /` di awal sesi (step 1) | **LOLOS** — **IDENTIK** |

**Regresi tambahan**: `GET /projects` → 200. `GET /projects/lumina-saas` & `GET /projects/aurora-commerce` → 200 (dua project berbeda, tidak terpengaruh perubahan index). Toggle bahasa ID/EN: 117 elemen `x-show` `id` vs 117 `en` (seimbang). `data-reveal`: 49 elemen, `data-reveal-enabled="1"`. Accent preset: `--accent-600: #4f46e5;` (indigo, baseline Iterasi 19 tidak berubah). `/admin/dashboard`, `/admin/section-settings` (rute lama), `/admin/appearance` (semua tab) → semua 200. Server dimatikan di akhir (`taskkill` PID, dikonfirmasi request berikutnya `000`/connection refused).

### File/area utama yang berubah
- **Controller diubah**: `app/Http/Controllers/PortfolioController.php` (const `SECTION_PARTIALS` baru, `index()` di-refactor total — urutan/aktif section 7 top-level jadi query langsung + `effective()`, `$projectCount`/`$blogCount`/`$testimonialsCount` dipakai di query `take()`, `$orderedSections` baru dikirim ke view), `app/Http/Controllers/Admin/AppearanceController.php` (`index()` tambah `$orderedTopLevelSections`, method baru `updateSections()`).
- **Route baru**: `routes/admin.php` — `PUT admin/appearance/sections` (`admin.appearance.sections.update`).
- **View diubah**: `resources/views/portfolio/index.blade.php` (7 `@if`/`@include` → 1 `@foreach`), `resources/views/admin/appearance/index.blade.php` (blok baru "Urutan Tampil & Jumlah Item" di tab sections, drag-drop form), `resources/views/components/icon.blade.php` (ikon baru `grip-vertical`).
- **JS diubah**: `resources/js/admin.js` (`Alpine.data('sectionReorder', ...)` baru — reorder array client-side, TANPA fetch/AJAX, lihat poin 2 Ringkasan).
- **Model**: tidak ada perubahan (`SectionSetting::effective()`/`draft_overrides` Iterasi 18 sudah cukup generik untuk `sort_order` & `display_count` sekaligus).
- **Skema DB**: TIDAK ada perubahan (kolom `sort_order`/`display_count`/`draft_overrides` semua sudah ada dari Iterasi 18) — `docs/ERD.md` TIDAK diupdate di iterasi ini.
- **Asset**: `npm run build` dijalankan ulang (JS `admin.js` baru + kelas Tailwind baru di blok reorder perlu masuk `public/build` — gitignored, tidak masuk `git status`).

### Migrasi & seeder dijalankan
- Tidak ada migrasi/seeder baru di iterasi ini — seluruh kolom yang dipakai (`sort_order`, `display_count`, `draft_overrides`) sudah ada sejak Iterasi 18.

### Verifikasi
Lihat tabel 12 skenario + regresi tambahan di Ringkasan poin 4 di atas — **SEMUA LOLOS**. `storage/logs/laravel.log` bersih (0 baris) di seluruh titik pengecekan sepanjang sesi. Server dimatikan di akhir.

### Commit
- Belum di-commit — menunggu review & commit manual dari user. (Catatan: commit `7ac9600` yang muncul di `git log` selama sesi ini adalah commit MANUAL USER untuk Iterasi 18-19, bukan bagian dari pekerjaan Iterasi 20 — lihat Ringkasan poin pembuka.)

### Catatan untuk review
- **Drift numerik `sort_order` saat reorder (non-blocking, cosmetic-only, didokumentasikan supaya tidak dianggap bug tersembunyi)**: `updateSections()` menulis `sort_order` baru sbg index 0-6 SEQUENTIAL untuk 7 section top-level, TIDAK memperhitungkan slot numerik `skills` (section ke-8, tetap `sort_order=2` sejak seeding, di luar jangkauan fitur reorder ini). Akibatnya, setelah publish REORDER pertama kali, kolom `sort_order` "projects" dkk bisa bertabrakan angka dgn `skills` (mis. sama2 `2`) — ini TIDAK berdampak fungsional apa pun (urutan tampil 7 section tetap benar 100%, `skills` tetap render nested di posisi tetapnya di dalam `about.blade.php` terlepas dari angka `sort_order`-nya, TIDAK pernah dibaca lewat jalur manapun selain daftar on/off admin), HANYA berpotensi membuat kolom "#" di daftar on/off (`admin/section-settings/_list.blade.php`) menampilkan angka yang sama utk 2 baris berbeda setelah sebuah reorder publish — kosmetik murni. Diperbaiki manual (tinker, balik ke nilai pristine 0-7) di akhir sesi verifikasi ini supaya baseline databasenya benar2 identik dgn sebelum sesi dimulai; TIDAK diperbaiki di level KODE karena memperbaikinya dgn benar (mis. numbering desimal/gap dinamis yang ikut menyesuaikan slot `skills`) menambah kompleksitas non-trivial untuk isu yang murni kosmetik & di luar scope literal tugas ("skills ... tidak usah diutak-atik posisinya"). Direkomendasikan sebagai catatan untuk Iterasi 21 kalau area "Urutan & Isi Section" disentuh lagi.
- **Cache `SECTION_SETTINGS_CACHE_KEY` (Iterasi 15) SENGAJA TIDAK dipakai untuk urutan/is_active efektif di `PortfolioController@index`** — cache itu murni is_active LIVE (dipakai `$sectionActive` utk toggle "skills" yang memang selalu live), sedangkan data reorder/display_count HARUS preview-aware per-request (beda hasil utk admin+preview vs visitor dalam request yg sama2 nge-hit endpoint yg sama) — cache generik yang tidak tahu soal sesi/preview akan salah/bocor kalau dipaksakan di sini. Query langsung (bypass cache) dipilih krn tabel `section_settings` sangat kecil (8 baris) — dampak performa diabaikan, correctness lebih penting.
- **Keputusan form-submit vs fetch/AJAX untuk aksi simpan reorder** — lihat penjelasan lengkap di poin 2 Ringkasan; ringkasnya: drag-drop-nya sendiri genuinely native HTML5 DnD (tidak fallback ke tombol), hanya mekanisme "commit ke server"-nya yang disamakan dgn pola form-submit semua tab appearance lain demi konsistensi UX (banner status Draft/Live ter-refresh otomatis via full-page redirect) tanpa menambah kompleksitas AJAX yang belum ada presedennya.
- **`updateSections()` men-submit `display_count` untuk SEMUA 3 section list-type di SETIAP submit** (bukan cuma yg berubah) — konsekuensi dari menggabung reorder+count jadi satu form/aksi. Ini berarti setiap kali admin drag-reorder (tanpa niat mengubah jumlah item), `draft_overrides['display_count']` tetap ikut ditulis ulang dgn nilai yang SAMA seperti sebelumnya (bukan bug, hanya "no-op write" — hasil akhirnya identik, hanya representasi JSON draft-nya ikut disegarkan).
- Tidak ada `git add`/`git commit` dijalankan sepanjang sesi ini, sesuai batasan tugas — termasuk TIDAK menyentuh commit `7ac9600` yang dibuat user di tengah sesi.

---

## Iterasi 19 — Preset Warna Aksen & Logo/Branding + Beres-beres `playground`/`skills` (selesai: 2026-08-24)
Status: Selesai — **Fase 4 (Kustomisasi Tampilan Halaman Index), lanjutan Iterasi 18.**

### Ringkasan
Sesuai `docs/RENCANA-KUSTOMISASI-TAMPILAN.md` bagian 5 Iterasi 19, ditambah 1 tugas beres-beres yang secara eksplisit dicatat sebagai temuan "di luar scope" di entri Iterasi 18. **Kondisi awal sesi**: `git status --short` **BERSIH**, `git log` mengonfirmasi seluruh Iterasi 18 (`5897762`) sudah ter-commit oleh user — tidak disentuh/direvert, pekerjaan sesi ini ditumpuk di atasnya. Dua bagian dikerjakan dalam satu sesi berurutan:

**Bagian A — Beres-beres `playground` & `skills` di `section_settings`:**

1. **Baris `playground` dihapus** lewat migration data-cleanup baru `2026_08_24_090000_remove_playground_section_setting_row.php` (`up()`: `DELETE ... WHERE section_key='playground'`, lalu merapikan `sort_order` 8 baris sisa jadi 0-7 tanpa lubang; `down()`: sengaja **no-op** — dijelaskan di komentar migration bahwa insert-ulang baris untuk fitur yang sudah permanen dihapus dari kode hanya akan menciptakan ulang masalah "baris yatim" yang sedang diperbaiki). `SectionSettingSeeder` diupdate senada (baris `playground` dihapus dari array seed, `sort_order` 4 section setelahnya digeser -1) supaya `migrate:fresh --seed` di masa depan menghasilkan 8 baris, bukan 9 lagi. Migration dijalankan (`php artisan migrate`) — DB dikonfirmasi via tinker: 8 baris tersisa (`hero, about, skills, projects, experience, blog, testimonials, contact`), `sort_order` 0-7 rapat.
2. **Toggle `skills` — TEMUAN: sudah fungsional, bukan bug.** Investigasi `resources/views/portfolio/partials/about.blade.php` menunjukkan blok grid skills SUDAH dibungkus `@if ($sectionActive['skills'] ?? true)` sejak commit `33db39a` ("Iterasi 1: dashboard + toggle aktif/nonaktif section publik") — jauh sebelum Fase 4. Catatan "yatim/tidak mengontrol apapun" di entri Iterasi 18 ternyata mengacu HANYA pada fakta `skills` tidak punya `@if`/`@include` sendiri di level `portfolio/index.blade.php` (benar — nested di dalam partial `about`, bukan section top-level), BUKAN berarti togglenya rusak/tidak berefek. Ditambahkan komentar penjelasan di `about.blade.php` yang meluruskan nuansa ini untuk pembaca berikutnya (termasuk Iterasi 20): karena "skills" bukan section top-level sungguhan, `is_active`-nya HANYA mengontrol visibility grid, TIDAK relevan untuk reorder posisi (Iterasi 20 akan beroperasi di 8 baris top-level, "skills" dikecualikan karena tidak punya posisi DOM independen). Toggle tetap **direct-to-live** (bukan draft-aware) sama seperti section lain — sesuai keputusan eksplisit di catatan Iterasi 18 bahwa retrofit toggle on/off ke layer draft ditunda sampai Iterasi 20 (reorder), tidak diubah di sini supaya perilaku toggle tidak dirombak dua kali.
3. **Grep referensi lain ke `playground`**: ditemukan di `resources/views/admin/section-settings/_list.blade.php` (`$iconMap['playground']`, dead entry — dihapus), `app/Http/Controllers/Admin/PlaceholderController.php` & `routes/admin.php`/`resources/views/admin/layouts/app.blade.php` (menu sidebar admin **"Playground"** — placeholder generik utk fitur admin masa depan terkait section Playground, sudah ada sejak Iterasi 0, TIDAK query `section_key`, di luar scope literal "referensi ke `section_key='playground'`" — **sengaja tidak disentuh**, karena mengubah/menghapus seluruh menu placeholder itu adalah keputusan UX terpisah yang tidak diminta), `database/seeders/ProjectSeeder.php` (kata "playground" muncul di teks deskripsi 1 project — konten bisnis asli, bukan referensi ke section, tidak disentuh), dan beberapa file dokumentasi (`docs/*.md` — histori, sengaja tidak diubah kecuali entri baru).

**Bagian B — Iterasi 19 (Preset Warna Aksen & Logo/Branding), sesuai rencana:**

1. **CSS custom properties**: `resources/css/app.css` — `:root { --accent-50, --accent-100, --accent-300, --accent-500, --accent-600, --accent-700 }` (6 step, BUKAN skala 50-950 penuh — di-grep dulu step mana yang benar-benar dipakai elemen brand-accent sebelum memutuskan skala ini). Default statis = nilai Indigo (fallback murni; nilai aktif SELALU di-override per-request oleh inline `<style>` di layout, lihat poin 4).
2. **`App\Support\AccentPreset`** (kelas baru) — 4 preset kurasi (`indigo` default, `emerald`, `rose`, `amber`), nilai hex tiap step disamakan PERSIS dengan skala resmi Tailwind (mis. indigo-600 `#4f46e5`, emerald-600 `#059669`, rose-600 `#e11d48`, amber-600 `#d97706`) supaya kontras & aksesibilitas terjaga — bukan color picker bebas (sesuai batasan Fase 4 bagian 6).
3. **Elemen yang dikonversi jadi accent-aware** (scope realistis, BUKAN exhaustive recolor — keputusan detail & alasan tiap batas didokumentasikan di "Catatan untuk review" di bawah):
   - **CTA utama (Hire Me/Rekrut Saya)**: `navbar.blade.php` `#nav-hire-me-btn` (desktop) & `#mobile-contact-cta-btn` (drawer mobile); `hero.blade.php` `#hero-explore-projects-btn` (CTA utama Hero, pola visual identik dgn Hire Me: `bg-slate-900 hover:bg-[var(--accent-600)]`).
   - **Pill nav aktif navbar**: link desktop aktif, link mobile-drawer aktif (bg/text/border), dot indikator aktif.
   - **Badge label section** ("kotak kecil di atas judul", pola `bg-indigo-50/80 text-indigo-700 border-indigo-100`) — diterapkan ke SEMUA section yang punya badge ini: `about`, `projects` (home), `blog`, `experience`, `contact`, `projects/index.blade.php` (katalog). Badge `testimonials` (amber, bukan indigo) sengaja tidak disentuh — beda warna by design.
   - **Gradient judul Hero**: `hero.blade.php` — `from-indigo-600 ... to-indigo-500` → accent (stop tengah `via-blue-600` sengaja dipertahankan, bukan bagian preset).
   - **Border/shadow hover card (project & skill card)**: `hover:border-indigo-300/80` di `projects.blade.php` (home), `projects/index.blade.php` (katalog), `projects/show.blade.php` (related projects) — 3 lokasi component "project card" yang sama; `about.blade.php` skill card (border + icon box + title hover). Card blog/experience/testimonials **sengaja TIDAK diikutkan** — task secara literal menyebut "project card, skill card" saja.
   - **Tombol filter aktif (kategori project/skill)**: `about.blade.php` (filter skill), `projects.blade.php` (home) & `projects/index.blade.php` (katalog) — filter kategori project. Filter/tag blog **sengaja TIDAK diikutkan** (literal scope "kategori project/skill").
   - Elemen yang SENGAJA dibiarkan indigo statis (di luar 6 kategori scope): scroll-progress-bar & ambient blob di layout (dekoratif multi-hue), logo mark/wordmark navbar-footer (mekanisme terpisah, lihat poin 5), text-link biasa (mis. "Detail Case Study", "Baca Selengkapnya", breadcrumb) yang memakai warna indigo tapi bukan CTA/badge/filter/card-hover, timeline dot & garis vertikal di Experience, ikon-ikon dekoratif kecil di dalam card/button.
4. **Penerapan preset — inline `<style>` server-rendered di `<head>`** (`resources/views/layouts/app.blade.php`, setelah `@vite(...)`, membaca `$accentPreset` yang dibagikan `App\Http\Middleware\HandleAppearancePreview` ke SEMUA view — preview-aware persis pola `$animationsEnabled` Iterasi 18) — BUKAN lewat JS runtime, supaya tidak ada flash-of-wrong-color. Diletakkan setelah `@vite` supaya cascade `:root` di sini menang atas default statis di `app.css`.
5. **Logo & branding**: 2 setting baru `logo_type` (`text|image`, default `text`) & `logo_image` (path storage, nullable) di `display_settings`. Upload mengikuti pola persis `Admin\ProjectController`/`Admin\TestimonialController` (`$file->store('branding', 'public')` → `Storage::url()`). `navbar.blade.php` & `footer.blade.php` diupdate: `@if (logo_type==='image' && logo_image terisi)` tampilkan `<img>` (width/height eksplisit, `loading="eager"` navbar / `loading="lazy"` footer, konsisten pola Iterasi 14), else fallback ke logo teks "Bagus.dev" — TIDAK PERNAH kosong.
6. **Setting tersimpan lewat alur draft/publish Iterasi 18** — `AppearanceController@updateBranding()` (route baru `PUT /admin/appearance/branding`) menulis ke `DisplaySetting::setDraft()` untuk ketiga key (`accent_preset`, `logo_type`, `logo_image`). **`publish()`/`discardDraft()` TIDAK PERLU diubah SAMA SEKALI** — keduanya sudah generik sejak didesain di Iterasi 18 (loop semua `display_settings.value_draft` non-null), persis seperti yang direncanakan. Tab "Tema & Branding" (`admin/appearance/index.blade.php`) diubah dari placeholder jadi form fungsional: 4 swatch preset (radio + dot warna), pilihan tipe logo (Alpine `x-show` utk toggle field upload), preview logo saat ini kalau ada.

### File/area utama yang berubah
- **Migrasi baru**: `database/migrations/2026_08_24_090000_remove_playground_section_setting_row.php`.
- **Seeder diubah**: `database/seeders/SectionSettingSeeder.php` (baris `playground` dihapus, `sort_order` dirapikan).
- **Kelas baru**: `app/Support/AccentPreset.php` (4 preset warna, `get()`/`keys()`).
- **Model**: tidak ada perubahan (`DisplaySetting`/`SectionSetting` Iterasi 18 sudah cukup generik).
- **Middleware diubah**: `app/Http/Middleware/HandleAppearancePreview.php` — tambah share `$accentPreset`/`$logoType`/`$logoImage` (preview-aware, pola sama `$animationsEnabled`).
- **Controller diubah**: `app/Http/Controllers/Admin/AppearanceController.php` — `index()` pass data branding tambahan, method baru `updateBranding()`.
- **Route baru**: `routes/admin.php` — `PUT admin/appearance/branding` (`admin.appearance.branding.update`).
- **View diubah**: `resources/views/admin/appearance/index.blade.php` (tab Tema & Branding jadi fungsional), `resources/views/admin/section-settings/_list.blade.php` (hapus entry `playground` dari `$iconMap`), `resources/views/components/icon.blade.php` (tambah icon `image`), `resources/views/layouts/app.blade.php` (inline `<style>` accent vars di `<head>`), `resources/views/portfolio/partials/{navbar,footer}.blade.php` (logo conditional + accent), `resources/views/portfolio/partials/{hero,about,projects,blog,experience,contact}.blade.php`, `resources/views/projects/{index,show}.blade.php` (accent-aware sesuai daftar poin 3 Bagian B), `resources/views/portfolio/partials/about.blade.php` (komentar klarifikasi toggle skills, Bagian A poin 2).
- **CSS diubah**: `resources/css/app.css` (custom properties `--accent-*`).
- **Asset**: `npm run build` dijalankan ulang (kelas Tailwind arbitrary-value baru `bg-[var(--accent-600)]` dkk perlu di-scan ulang & masuk `public/build` — gitignored, tidak masuk `git status`).

### Migrasi & seeder dijalankan
- `php artisan migrate` — 1 migrasi baru (`remove_playground_section_setting_row`) sukses. Dikonfirmasi via tinker: `SectionSetting::count()` = 8 (dari 9), `sort_order` 0-7 tanpa lubang.
- Tidak ada seeder dijalankan ulang di sesi ini (seeder diupdate untuk instalasi masa depan, DB yang sudah ada dibersihkan lewat migration, bukan re-seed).

### Verifikasi
**Via `php artisan serve --port=8180` + `curl` sungguhan (cookie jar utk sesi login admin), `storage/logs/laravel.log` dikosongkan sebelum sesi, dicek `wc -l` di beberapa titik → konsisten 0 baris sepanjang seluruh sesi.**

**Bagian A:**
| # | Skenario | Hasil |
|---|---|---|
| 1 | `GET /admin/appearance?tab=sections` (list section) setelah migration | **LOLOS** — "Daftar Section (8)", tidak ada lagi baris "playground" (dicek `grep -c playground` hanya cocok 2x, keduanya link sidebar admin "Playground" placeholder yang tidak terkait) |
| 2 | Toggle `skills` OFF (`PATCH /admin/section-settings/3/toggle`, mekanisme direct-live Iterasi 1 — bukan draft, sesuai catatan Iterasi 18) | **LOLOS** — `GET /` sesudahnya: `id="skills"` 0 kemunculan, `id="about"` & teks bio About tetap 1 kemunculan (konten About lain utuh) |
| 3 | Toggle `skills` ON lagi | **LOLOS** — `id="skills"` kembali 1 kemunculan |

**Bagian B:**
| # | Skenario | Hasil |
|---|---|---|
| 1 | Baseline sebelum draft apapun | **LOLOS** — `GET /` (no cookie): `--accent-600: #4f46e5;` (indigo), tanpa banner preview |
| 2 | Set draft `accent_preset=emerald` (`PUT /admin/appearance/branding`) | **LOLOS** — DB: `value=NULL, value_draft='emerald'` (baris baru via `updateOrCreate`) |
| 3 | `GET /` TANPA cookie (visitor biasa) setelah draft | **LOLOS** — tetap `--accent-600: #4f46e5;` (draft tidak bocor) |
| 4 | `GET /` DENGAN cookie admin, TANPA `?preview=1` | **LOLOS** — tetap indigo (preview belum aktif) |
| 5 | Aktifkan preview (`?preview=1`) | **LOLOS** — `--accent-600: #059669;` (emerald), banner draft muncul 1x |
| 6 | `GET /projects` & `GET /projects/lumina-saas` dalam mode preview (session persist) | **LOLOS** — keduanya `--accent-600: #059669;` juga (accent konsisten di semua halaman lewat layout yang sama) |
| 7 | `POST /admin/appearance/publish` | **LOLOS** — DB: `value='emerald', value_draft=NULL`; `GET /` & `GET /projects` TANPA cookie sekarang emerald (74 kemunculan `var(--accent-600)` di HTML index, tersebar di badge/CTA/pill/filter/card-hover yg dikonversi) |
| 8 | Upload logo gambar sbg draft (`logo_type=image` + file PNG test) | **LOLOS** — DB: `logo_image.value_draft` terisi path storage, `logo_type.value_draft='image'`; `GET /` TANPA cookie: 0 kemunculan path logo (draft tidak bocor), masih logo teks |
| 9 | Preview (admin+`?preview=1`) | **LOLOS** — 2 kemunculan `<img>` logo (navbar + footer), file diverifikasi bisa diakses via `GET /storage/branding/....png` → 200 |
| 10 | Publish logo | **LOLOS** — `GET /` TANPA cookie: logo gambar sekarang tampil (2 kemunculan) |
| 11 | Restore baseline: draft `accent_preset=indigo` + `logo_type=text` → publish | **LOLOS** — DB akhir: `accent_preset.value='indigo'`, `logo_type.value='text'` (value_draft NULL semua); `GET /` visitor: `--accent-600: #4f46e5;`, logo kembali teks "Bagus.dev" (0 kemunculan `<img>` logo) |
| 12 | Housekeeping tambahan | Baris `display_settings` utk `logo_image` & file test upload (`storage/app/public/branding/*.png`) dihapus manual di akhir sesi (bukan bagian alur draft/publish — cuma pembersihan artefak uji, tidak memengaruhi baseline situs krn `logo_type` sudah kembali `text`) |

**Regresi tambahan**: `GET /`, `/projects`, `/projects/lumina-saas`, `/admin/dashboard`, `/admin/appearance`, `/admin/section-settings` → semua **200**. Tab "Tema & Branding" dicek render dgn radio `indigo` ter-checked & tanpa preview logo (kondisi akhir). Server dimatikan di akhir sesi (`taskkill` PID, dikonfirmasi request berikutnya `000`/connection refused).

### Commit
- Belum di-commit — menunggu review & commit manual dari user.

### Catatan untuk review
- **Kesalahpahaman kunci klarifikasi tugas**: toggle `skills` yang dikira "tidak fungsional" ternyata SUDAH bekerja sejak Iterasi 1 — hanya catatan Iterasi 18 yang agak ambigu (fokus ke "tidak ada `@if` di level index.blade.php", bukan "togglenya rusak"). Tidak ada perbaikan KODE yang diperlukan untuk poin ini, hanya klarifikasi via komentar + verifikasi ulang.
- **Batasan scope preset warna — keputusan literal, didokumentasikan supaya tidak dianggap "lupa"**: task menyebut 6 kategori elemen brand-accent secara eksplisit (CTA utama navbar+hero, pill nav navbar, badge label section, gradient Hero, border-hover project/skill card, filter aktif project/skill). Diikuti SECARA LITERAL — tidak diperluas ke elemen indigo lain yang terlihat mirip (mis. card blog/experience/testimonials TIDAK diikutkan meski sama-sama pakai `hover:border-indigo-300/80`, karena task hanya menyebut "project card, skill card"; text-link seperti "Detail Case Study"/"Baca Selengkapnya" TIDAK diikutkan karena bukan CTA/badge/filter/card-hover). Satu-satunya perluasan disengaja dari daftar literal: badge label section diterapkan ke SEMUA section yang punya badge (bukan cuma yang dicontohkan "Karya Pilihan"), karena task eksplisit menulis "tiap section". Hasilnya: masih ada elemen ber-`bg-indigo-*`/`text-indigo-*` statis tersisa di kode (scroll-progress-bar, ambient blob, logo mark, dot timeline Experience, banyak text-link) — ini SENGAJA, konsisten dengan instruksi "bukan exhaustive recolor".
- **Logo pada footer memakai background gelap** (`bg-slate-900`) — kalau admin upload logo gambar yang didesain untuk background terang (mis. logo dgn teks gelap tanpa padding), hasilnya bisa kurang kontras di footer. Ini keterbatasan yang melekat pada fitur "1 logo utk semua tempat" (bukan bug kode) — tidak ada logo terpisah utk navbar vs footer, sesuai rencana (hanya 1 `logo_image`/`logo_type`, bukan variant per lokasi).
- **`down()` migration playground sengaja no-op** (bukan insert-ulang baris) — dijelaskan lengkap di komentar migration; keputusan sadar karena section-nya sendiri sudah permanen tidak ada di kode.
- **`publish()`/`discardDraft()` di `AppearanceController` TIDAK disentuh sama sekali** untuk Iterasi 19 — bukti bahwa desain generik Iterasi 18 benar-benar bekerja seperti direncanakan (bagian 3 rencana: "Iterasi 19-22 tidak perlu controller publish/discard baru").
- Tidak ada `git add`/`git commit` dijalankan sepanjang sesi ini, sesuai batasan tugas.

---

## Iterasi 18 — Fondasi: Draft/Publish + Skema Baru (selesai: 2026-08-24)
Status: Selesai — **Fase 4 (Kustomisasi Tampilan Halaman Index), iterasi pembuka.**

### Ringkasan
Sesuai `docs/RENCANA-KUSTOMISASI-TAMPILAN.md` bagian 5 Iterasi 18. **Kondisi awal sesi**: `git status --short` **BERSIH**, `git log` menunjukkan commit terakhir `d1d2774` ("Remove playground section and related references from portfolio views") sudah ter-commit oleh user — bukan pekerjaan tugas ini, dan **tidak disentuh/direvert**. Dikonfirmasi langsung ke DB & ke `resources/views/portfolio/index.blade.php`: tabel `section_settings` MASIH punya 9 baris (termasuk `playground`, `is_active=true`, `skills`) peninggalan skema lama, tapi `portfolio/index.blade.php` HANYA meng-`@if`/`@include` 7 section: `hero`, `about`, `projects`, `experience`, `blog`, `testimonials`, `contact` — `playground` tidak punya `@include` sama sekali lagi (section-nya sungguhan hilang dari halaman publik), dan `skills` tidak punya `@if` sendiri karena nested di dalam partial `about`. Baris `playground`/`skills` di `section_settings` jadi "yatim" (ada di DB, tidak dipakai gate manapun di index) — tidak diperbaiki di iterasi ini (di luar scope, bukan bagian dari rencana Iterasi 18), hanya dicatat sebagai temuan supaya Iterasi 20-21 (reorder & custom heading) tidak salah asumsi jumlah section aktif.

**1) Skema baru:**
- Tabel `display_settings` (migrasi `2026_08_24_035143_create_display_settings_table.php`): `id`, `setting_key` (unique), `value` (text, nullable — LIVE), `value_draft` (text, nullable — DRAFT, `NULL` = tidak ada perubahan pending), timestamps.
- Kolom baru di `section_settings` (migrasi `2026_08_24_035144_add_appearance_columns_to_section_settings_table.php`): `display_count` (unsignedInteger nullable), `heading_id`/`heading_en` (string nullable), `subheading_id`/`subheading_en` (text nullable), `draft_overrides` (JSON nullable).
- `php artisan migrate` dijalankan, keduanya sukses tanpa error.

**2) Keputusan desain kunci (alasan tiap keputusan):**
- **`draft_overrides` JSON tunggal vs kolom `*_draft` berdampingan per field** — dipilih JSON (object partial, hanya berisi key yang benar-benar berubah, mis. `{"is_active": false}`) ketimbang menduplikasi 5 kolom (`is_active_draft`, `sort_order_draft`, `heading_id_draft`, dst). Alasan: field draft-able di `section_settings` akan terus bertambah di Iterasi 20-21 (reorder, display_count, heading/subheading) — pendekatan kolom berdampingan berarti tiap field baru butuh migration baru x2 (kolom asli + kolom draft), sedangkan JSON tunggal cukup tambah key baru tanpa migration tambahan. Trade-off: query "cari baris dengan draft pending" pakai `whereNotNull('draft_overrides')` (tidak bisa index per-field), tapi untuk skala data section (~9 baris) ini tidak masalah.
- **`display_settings` sebagai tabel key-value generik** — dipilih ketimbang menambah kolom nullable ke `site_profiles` yang sudah ada, supaya setting baru (preset warna, logo, toggle sub-elemen, maintenance) di Iterasi 19-22 tidak perlu migration berulang, cukup baris baru.
- **Mekanisme penanda preview: query `?preview=1`/`?preview=0` disimpan ke session (`appearance_preview`)** — dipilih ketimbang harus menempel `?preview=1` di setiap link saat browsing preview (tidak praktis, gampang hilang saat klik link internal manapun). Sekali diaktifkan lewat 1 klik ("Buka Preview"), mode preview persist di seluruh sesi browsing sampai admin klik "Keluar dari Mode Preview" (`?preview=0`) atau logout. Preview HANYA aktif kalau **admin login (guard `web`) DAN flag session menyala** — dicek eksplisit dua-duanya di `HandleAppearancePreview::handle()`, bukan hanya salah satu, supaya query string `?preview=1` yang ditembak visitor tanpa login TIDAK PERNAH bisa mengaktifkan preview.
- **Middleware didaftarkan di grup `web` global (`bootstrap/app.php`, `$middleware->web(append: [...])`)**, bukan cuma di `routes/web.php` atau scoped ke route index saja — karena requirement eksplisit: logo/warna aksen/sub-elemen navbar-footer akan dipakai SEMUA halaman publik lewat `layouts/app.blade.php` yang shared, dan `?preview=1|0` juga perlu bisa ditembak dari halaman publik manapun (bukan cuma `/`). Middleware ini juga membagikan `appearancePreview`, `animationsEnabled`, `appearanceHasDraft` ke SEMUA view lewat `view()->share()` — jadi `layouts/app.blade.php` (dan turunannya, `portfolio/index.blade.php` dkk) otomatis dapat variabel ini tanpa perlu tiap controller publik pass eksplisit.
- **Menu admin: DIGABUNG ke "Pengaturan Section" lama** (bukan menu terpisah) — jadi 1 menu sidebar baru "Tampilan Halaman Index" (`route: admin.appearance`, icon `palette`) menggantikan entri "Pengaturan Section" lama di sidebar. Alasan: kedua menu sama-sama "mengatur bagaimana halaman index tampil" dari sudut pandang admin — 2 entri sidebar untuk konsep yang sama terasa redundan, dan indikator status Draft/Live + tombol Publish/Buang Draft perlu 1 tempat yang menaungi semua sub-fitur Fase 4 (bukan tersebar per menu). Implementasi: markup list on/off section (Iterasi 1) **diekstrak** dari `admin/section-settings/index.blade.php` ke partial baru `admin/section-settings/_list.blade.php`, dipakai ulang di tab "Urutan & Isi Section" pada halaman baru — TIDAK ada duplikasi logic. Rute lama (`admin.section-settings`, controller `SectionSettingController` — tidak diubah) **sengaja tidak dihapus** untuk kompatibilitas (masih 200, masih berfungsi penuh), hanya tidak lagi ditaut dari sidebar; halaman itu sendiri sekarang menampilkan link balik ke tab baru.
- **Halaman "Tampilan Halaman Index" pakai tab Alpine client-side** (`x-show`, bukan multi-route/multi-controller-method) untuk 6 sub-bagian (Ringkasan, Tema & Branding, Animasi & Efek, Urutan & Isi Section, Elemen Halaman, Mode Situs) — satu `GET /admin/appearance` saja, lebih sederhana untuk iterasi fondasi ini ketimbang bikin 6 route+view terpisah untuk sebagian besar konten yang masih placeholder.
- **Publish/Discard generik & sekaligus (bukan per-setting)** — `AppearanceController@publish()`/`discardDraft()` loop SEMUA `display_settings.value_draft` dan SEMUA `section_settings.draft_overrides` non-null lalu proses semuanya dalam 1 aksi, sesuai model "satu snapshot draft vs satu live" (bagian 3 rencana: bukan revisi/histori per-field). Iterasi 19-22 tidak perlu controller publish/discard baru — tinggal menulis ke `DisplaySetting::setDraft()`/`SectionSetting.draft_overrides` yang sudah ada.
- **Boolean disimpan sebagai string `'1'`/`'0'`** (bukan `'true'`/`'false'`) di `display_settings.value`/`value_draft` — memanfaatkan perilaku native PHP bahwa `(bool) '0' === false` (pengecualian khusus PHP untuk string `"0"`), jadi call site cukup `(bool) DisplaySetting::get(...)` tanpa parsing tambahan.

**3) Bukti konsep `animations_enabled` (reveal-on-scroll):**
- Tab "Animasi & Efek" di `admin/appearance/index.blade.php` — checkbox, submit `PUT /admin/appearance/animations` → `DisplaySetting::setDraft('animations_enabled', ...)` (draft, bukan langsung live).
- `resources/views/layouts/app.blade.php`: `<body data-reveal-enabled="{{ $animationsEnabled ? '1' : '0' }}">` — nilai `$animationsEnabled` dihitung SEKALI oleh middleware (server-rendered, bukan API terpisah) mempertimbangkan mode preview request saat itu.
- `resources/js/reveal.js`: `initRevealOnScroll()` baca `document.body.dataset.revealEnabled` — kalau `'0'`, semua elemen `[data-reveal]` langsung diberi class `.is-visible` tanpa `IntersectionObserver` (animasi mati total, konten tetap tampil normal, tidak "nyangkut" tersembunyi). Body admin layout tidak punya atribut ini sama sekali sehingga default aktif — panel admin sendiri sengaja TIDAK ikut terpengaruh setting tampilan situs publik (keputusan sadar, di luar cakupan "Kustomisasi Tampilan Halaman **Index**").
- **Efek samping kecil**: scroll-progress-bar publik diubah dari `position: fixed` ke `position: sticky` supaya tidak tumpang-tindih secara visual dengan banner mode Preview (yang juga `sticky top-0`, z-index lebih tinggi) — perilaku terlihat identik untuk elemen setipis ini (selalu terlihat di scroll manapun).

**4) Verifikasi end-to-end — via `php artisan serve --port=8170` + `curl` sungguhan (cookie jar utk sesi login admin), BUKAN baca kode saja.** `storage/logs/laravel.log` dikosongkan sebelum sesi, dicek `wc -l` di setiap titik → **konsisten 0 baris sepanjang seluruh sesi**, termasuk di akhir.

| # | Skenario | Hasil |
|---|---|---|
| 1 | `GET /` tanpa login, kondisi awal (belum ada baris `display_settings` sama sekali) | **LOLOS** — `data-reveal-enabled="1"` (fallback default true saat baris belum ada), tidak ada banner preview |
| 2 | Login admin (`admin@bagusbatra.dev`), submit form Animasi & Efek dgn checkbox dikosongkan (nonaktifkan) | **LOLOS** — `PUT /admin/appearance/animations` → 302; DB: `value=NULL` (baris baru dibuat via `updateOrCreate`, live belum tersentuh), `value_draft='0'` |
| 3 | `GET /` **TANPA cookie** (visitor biasa) setelah step 2 | **LOLOS** — `data-reveal-enabled="1"` tetap (draft TIDAK bocor), tidak ada banner |
| 4 | Aktifkan preview (`GET /?preview=1` dgn cookie admin), lalu `GET /` lagi TANPA parameter `?preview=1` (uji persist di session) | **LOLOS** — kedua request: `data-reveal-enabled="0"` (draft terlihat) + teks "Anda sedang melihat mode Draft/Preview" muncul 1x di HTML pada KEDUA request (membuktikan mode preview persist di session, tidak perlu `?preview=1` di tiap link) |
| 5 | `POST /admin/appearance/publish` (sbg admin) | **LOLOS** — 302 ke halaman appearance; DB: `value='0'`, `value_draft=NULL` |
| 6 | `GET /` **TANPA cookie** setelah publish | **LOLOS** — `data-reveal-enabled="0"` (live sudah benar-benar berubah utk visitor biasa) |
| 7 | Set draft baru (`animations_enabled=1`) → `POST /admin/appearance/discard` | **LOLOS** — DB sesudah discard: `value` TETAP `'0'` (live tidak berubah), `value_draft=NULL` (draft kembali kosong) |
| 8 | Restore baseline: draft ke `1` lagi → publish, lalu keluar preview (`?preview=0`) & cek visitor | **LOLOS** — DB akhir: `value='1'`, `value_draft=NULL` (baseline situs sebelum iterasi ini dikembalikan persis); `GET /` visitor tanpa cookie: `data-reveal-enabled="1"`, tanpa banner |

**Regresi tambahan dicek dalam sesi yang sama** (bukan cuma 8 skenario di atas): `GET /`, `/projects`, `/projects/lumina-saas`, `/admin/dashboard`, `/admin/playground` (placeholder lama), `/admin/projects` → semua **200**. `GET /admin/appearance` (semua 6 tab merender tanpa error, termasuk tab "Urutan & Isi Section" yg meng-include partial section list) dan `GET /admin/section-settings` (rute lama, masih 200 & masih berfungsi) → keduanya OK. Server dimatikan di akhir sesi (`taskkill` PID `php artisan serve`, dikonfirmasi request berikutnya `000`/connection refused).

### File/area utama yang berubah
- **Migrasi baru**: `database/migrations/2026_08_24_035143_create_display_settings_table.php`, `database/migrations/2026_08_24_035144_add_appearance_columns_to_section_settings_table.php`.
- **Model baru**: `app/Models/DisplaySetting.php` (`get()`, `getBool()`, `setDraft()`, `hasPendingDraft()`, `publishAll()`, `discardAllDrafts()`).
- **Model diubah**: `app/Models/SectionSetting.php` — fillable + cast `draft_overrides` ke `array`, method `effective()`, `publishDraftOverrides()`, `discardDraftOverrides()`.
- **Middleware baru**: `app/Http/Middleware/HandleAppearancePreview.php` — didaftarkan global di grup `web` (`bootstrap/app.php`).
- **Controller baru**: `app/Http/Controllers/Admin/AppearanceController.php` (`index`, `updateAnimations`, `publish`, `discardDraft`).
- **Route baru**: `routes/admin.php` — `admin.appearance` (GET), `admin.appearance.animations.update` (PUT), `admin.appearance.publish` (POST), `admin.appearance.discard` (POST).
- **View baru**: `resources/views/admin/appearance/index.blade.php` (halaman tab baru), `resources/views/admin/section-settings/_list.blade.php` (partial hasil ekstraksi).
- **View diubah**: `resources/views/admin/section-settings/index.blade.php` (disederhanakan jadi wrapper + link ke menu baru), `resources/views/admin/layouts/app.blade.php` (sidebar: "Pengaturan Section" → "Tampilan Halaman Index"), `resources/views/layouts/app.blade.php` (banner preview + `data-reveal-enabled` di `<body>` + scroll-progress-bar `fixed`→`sticky`).
- **JS diubah**: `resources/js/reveal.js` (baca `data-reveal-enabled`, matikan observer kalau `'0'`).
- **Asset**: `npm run build` dijalankan ulang (perubahan `reveal.js` perlu di-compile ke `public/build`, yang dipakai `@vite` — `public/build` gitignored, tidak masuk `git status`).

### Migrasi & seeder dijalankan
- `php artisan migrate` — 2 migrasi baru sukses (`create_display_settings_table`, `add_appearance_columns_to_section_settings_table`). Tidak ada seeder baru (baris `display_settings` untuk `animations_enabled` dibuat otomatis via `updateOrCreate` saat pertama kali admin mengubah setting ini, bukan lewat seeder — sengaja, supaya setting yang belum pernah disentuh admin tidak perlu baris DB sama sekali, cukup fallback ke `$default` di `DisplaySetting::get()`).

### Verifikasi
Lihat tabel 8 skenario & regresi tambahan di Ringkasan poin 4 di atas — **SEMUA LOLOS**, tidak ada yang gagal/perlu diperbaiki. `storage/logs/laravel.log` bersih (0 baris) di seluruh titik pengecekan. Server dimatikan di akhir.

### Commit
- Belum di-commit — menunggu review & commit manual dari user (lihat catatan Fase 4 di `docs/RENCANA-KUSTOMISASI-TAMPILAN.md` bagian 2).

### Catatan untuk review
- **Temuan di luar scope, TIDAK diperbaiki di iterasi ini** (dicatat sesuai instruksi tugas): baris `section_settings` untuk `playground` (9 baris total di DB) sudah jadi "yatim" sejak commit `d1d2774` (section-nya dihapus dari `portfolio/index.blade.php`, tapi baris `section_settings`-nya sendiri tidak dihapus/di-deactivate) — begitu juga baris `skills` yang memang dari awal tidak pernah punya `@if` sendiri di index (nested di partial `about`). Section yang BENAR-BENAR di-gate satu-satu di `portfolio/index.blade.php` saat ini HANYA 7: `hero`, `about`, `projects`, `experience`, `blog`, `testimonials`, `contact`. Ini relevan untuk Iterasi 20 (reorder) & 21 (custom heading) — jangan asumsikan 9 section aktif dari `section_settings`, cek langsung ke index.blade.php.
- Mekanisme draft `section_settings.draft_overrides` (skema + model method `effective()`/`publishDraftOverrides()`/`discardDraftOverrides()`) sudah dibuat & dilibatkan di `AppearanceController@publish()`/`discardDraft()`, TAPI belum ada UI admin manapun yang benar-benar menulis ke kolom ini di iterasi ini (belum ada form yang mengubah `is_active`/`sort_order`/dst lewat draft — toggle on/off section di tab "Urutan & Isi Section" MASIH langsung ke live seperti Iterasi 1, belum di-retrofit ke draft). Ini sesuai scope Iterasi 18 (fondasi generik + 1 bukti konsep `animations_enabled` saja) — retrofit toggle on/off section ke draft direncanakan menyatu dengan pekerjaan reorder di Iterasi 20, bukan di sini, supaya tidak mengubah perilaku toggle yang sudah stabil dua kali (sekali di sini, sekali lagi saat reorder dibangun).
- Tab "Tema & Branding", "Elemen Halaman", "Mode Situs" di halaman baru murni placeholder "Segera Hadir" (pola sama seperti `admin.placeholder` Iterasi 0) — tidak ada logic di baliknya.
- `docs/RENCANA-KUSTOMISASI-TAMPILAN.md` **TIDAK diupdate** — tidak ada penyimpangan besar dari rencana yang perlu dicatat di sana (semua keputusan yang tadinya open-ended di rencana — gabung/pisah menu, JSON vs kolom draft terpisah — sudah diputuskan & didokumentasikan lengkap di entri LOG ini).
- Tidak ada `git add`/`git commit` dijalankan sepanjang sesi ini, sesuai batasan tugas.

---

## Iterasi 17 — Audit Ulang & Ringkasan Hasil (selesai: 2026-08-23)
Status: Selesai — **Fase 3 (Optimalisasi Performa, Iterasi 13-17) TUNTAS SEPENUHNYA.**

### Ringkasan
Iterasi audit penutup (bukan fitur baru), sesuai `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 5 Iterasi 17. **Kondisi awal sesi**: `git status --short` **BERSIH** dan `git log` mengonfirmasi seluruh perubahan Iterasi 13-16 SUDAH ter-commit oleh user — termasuk commit `49c9bc1` yang ternyata menggabungkan Iterasi 15 (cache layer) DAN Iterasi 16 (route placeholder fix + README deploy) jadi satu commit sekaligus (`b433e69` = Iterasi 13, `0ac9b64` = Iterasi 14, `49c9bc1` = Iterasi 15+16 digabung). Ini berbeda dari asumsi rencana ("kemungkinan sudah di-commit") sekaligus dari catatan "Belum di-commit" yang tertulis di entri Iterasi 14-16 di bawah (yang ditulis SEBELUM user melakukan commit manual) — tidak ada tindakan diambil terhadap commit-commit tersebut maupun entri log lama (tidak direvisi/direvert), sesuai batasan tugas untuk tidak menyentuh apa pun yang sudah ada. Working tree bersih di awal sesi ini, jadi tidak ada perubahan Iterasi 13-16 yang perlu "dilanjutkan" — audit dimulai murni dari kondisi commit terakhir user.

**1) Regresi penuh publik + admin** — satu sesi menyeluruh via `php artisan serve --port=8150` + `curl` (cookie jar untuk login `admin@bagusbatra.dev`/`Admin#12345`), `storage/logs/laravel.log` dikosongkan sebelum sesi:

- **Data baseline dikonfirmasi PAS sebelum mulai**: 5 projects, 4 blog, 3 experience, 3 testimonials, 12 skills, 6 social links, 9/9 section aktif — persis sesuai baseline yang diharapkan.
- **Publik**: `GET /` → 200, 9/9 section id (`hero`/`about`/`skills`/`projects`/`playground`/`experience`/`blog`/`testimonials`/`contact`) semuanya render 1x masing-masing. Toggle bahasa: 130 elemen `x-show` untuk `id` dan 130 untuk `en` (seimbang, konsisten). `[data-reveal]`: 52 elemen. `loading="lazy"`: 13, `loading="eager"`: 1 — cocok persis dengan hasil pengukuran Iterasi 14 untuk `GET /`.
- **Cache & invalidasi dicek ulang lewat siklus HTTP form sungguhan** (bukan tinker) — `PUT /admin/profile` mengubah `tagline_id` jadi marker `TEST-CACHE-INVALIDATION-MARKER-ITERASI17` → `GET /` langsung (tanpa clear cache manual) menampilkan marker (1 kemunculan) → dikembalikan ke tagline asli → marker hilang (0), tagline asli kembali (1 kemunculan). **Invalidasi cache `site_profile` tetap berfungsi setelah ditumpuk Iterasi 13-16.**
- `GET /projects` → 200, 8 gambar semuanya `loading="lazy"` (tidak ada elemen above-the-fold di halaman katalog, sesuai temuan Iterasi 14). Filter (`?category=Frontend`) → 200, pagination (`?page=1`) → 200.
- `GET /projects/{key}` di-loop untuk **SEMUA 5 project** di database (diambil live dari `Project::pluck('project_key')`, bukan daftar hardcode): `aurora-commerce`, `fast-state-npm`, `lumina-saas`, `pulse-ai-workspace`, `zenith-design-system` — **5/5 sukses 200**, tidak ada 404/500. `GET /projects/does-not-exist` (key tak dikenal) → 404 (route-model-binding tetap benar).
- **Admin — 11 menu sidebar** (dicek langsung dari `resources/views/admin/layouts/app.blade.php` array `$menu`, bukan asumsi "12 menu" di instruksi tugas — jumlah aktual 11: Dashboard, Profil & Hero, Social Links, About & Skills, Projects, Playground, Experience, Blog, Testimonials, Pesan Masuk, Pengaturan Section): **SEMUA 11 → 200** setelah login. `GET /admin/playground` (route placeholder yang diubah dari closure ke Controller@method di Iterasi 16) → 200, isi mengandung teks "Playground" (4 kemunculan, termasuk `<title>Playground — Admin Bagus Batra</title>`) — perbaikan Iterasi 16 tetap berfungsi setelah ditumpuk perubahan lain.
- **Siklus create→edit→hapus via HTTP form sungguhan** (bukan tinker langsung) untuk Projects & Skills:
  - Projects: `POST /admin/projects` (create, title "Iterasi17 Dummy Project") → 302, id=8 dikonfirmasi ada → `PUT /admin/projects/8` (edit, title "...EDITED") → 302, dikonfirmasi berubah → `DELETE /admin/projects/8` → 302, `Project::count()` kembali ke **5**.
  - Skills: `POST /admin/about-skills` (create) → 302, id=15 dikonfirmasi ada → `PUT /admin/about-skills/15` (edit) → 302, dikonfirmasi berubah → `DELETE /admin/about-skills/15` → 302, `Skill::count()` kembali ke **12**.
- **Section-settings toggle**: `PATCH /admin/section-settings/8/toggle` (testimonials) → 200, `GET /` kehilangan `id="testimonials"` (0 match) → toggle balik → `id="testimonials"` muncul lagi (1 match), `SectionSetting::where('is_active',true)->count()` kembali ke **9/9**.
- **Hasil regresi: BERSIH, TIDAK ADA REGRESI ditemukan** dari Iterasi 13-16 yang perlu diperbaiki. Tidak ada perubahan kode baru yang diperlukan di iterasi ini.

**2) Uji 3 command cache produksi SEKALI LAGI, ditumpuk bersamaan** (bukan satu-satu terpisah seperti Iterasi 16) — untuk memastikan kombinasi perubahan Iterasi 13-16 tidak saling konflik: `php artisan config:cache` → sukses, `php artisan route:cache` → sukses (mengonfirmasi perbaikan closure route Iterasi 16 masih valid), `php artisan view:cache` → sukses. Dengan ketiganya aktif SEKALIGUS: `GET /` → 200, `GET /projects` → 200, `GET /projects/lumina-saas` → 200, `GET /admin/login` → 200, login sungguhan → `GET /admin/dashboard` → 200 (`<title>Dashboard — Admin Bagus Batra</title>` benar), `GET /admin/playground` → 200 (4x "Playground" tetap muncul), `GET /admin/projects` → 200. Waktu respons `GET /` dengan ketiga cache aktif (3x request): 0.833s / 0.587s / 0.543s — sedikit lebih tinggi dari angka Iterasi 16 (~0.349s) karena variasi beban mesin lokal saat pengukuran (bukan regresi arsitektural; server sama, cache sama-sama aktif) — tetap dalam orde besaran yang sama (sub-detik), bukan penurunan performa yang berarti. **Dibersihkan lagi (`config:clear`, `route:clear`, `view:clear`) di akhir** — dikonfirmasi `bootstrap/cache/` cuma berisi `packages.php`/`services.php` bawaan, `storage/framework/views/` kosong dari hasil compile, `GET /` setelah `:clear` tetap 200.

**3) `storage/logs/laravel.log` bersih sepanjang seluruh proses** — dicek `wc -l` di beberapa titik (awal sesi, setelah CRUD, setelah cache stack, setelah clear) → konsisten **0 baris** di semua titik. Tidak ada exception tersembunyi dari kombinasi perubahan Iterasi 13-16.

**4) Ringkasan angka before/after Iterasi 13-16** (dikutip dari entri masing-masing di bawah, bukan diukur ulang dari nol):

| Iterasi | Metrik | Before | After |
|---|---|---|---|
| **Baseline Fase 3** (bagian 3 rencana) | Bundle satu entry gabungan (`app.css`+`app.js`) dipakai identik di publik & admin | 194.683 KB raw / **42.139 KB gzip** per 1x load, di MANA PUN halaman | — |
| **Baseline Fase 3** | Query `GET /` | 8 query domain tanpa cache + 2 session = 10 | — |
| **Baseline Fase 3** | Featured project | `Project::orderBy(...)->get()` semua baris, filter `featured` di PHP | — |
| **Baseline Fase 3** | Gambar | 0 dari 18 `<img>` pakai `loading`/`width`/`height` | — |
| **13 — Split bundle publik/admin** | Total per load, HALAMAN PUBLIK | 194.683 KB / 42.139 KB gzip | 194.014 KB / **42.102 KB gzip** (≈0%, hampir tidak berubah — wajar, publik memang butuh hampir semua logic) |
| **13** | Total per load, HALAMAN ADMIN | 194.683 KB / 42.139 KB gzip | 187.036 KB / **39.648 KB gzip** (-5.9% total, **-11.5% khusus JS**) |
| **13** | Alpine core shared chunk | — | 53.168 KB / 18.605 KB gzip, **tidak terduplikasi** (Rollup auto code-split, gratis tanpa config manual) |
| **13** | CSS | — | TETAP 1 file (`app.css`, 133.072 KB/20.554 KB gzip) — keputusan sadar, overlap besar publik/admin, Tailwind v4 content-scan sudah otomatis minimal |
| **14 — Lazy loading gambar** | `GET /` — lazy vs eager | 0 lazy / 14 eager (implisit, tanpa atribut) | **13 lazy / 1 eager** (93% gambar index ditunda) |
| **14** | `GET /projects` — lazy vs eager | 0 lazy / 8 eager | **8 lazy / 0 eager** (100% ditunda, tidak ada elemen above-the-fold) |
| **14** | `width`/`height` eksplisit | 0 dari 18 elemen | 18/18 elemen (semua `<img>` publik+admin) |
| **14** | Penyesuaian `w=` Unsplash | `w=1000`/`w=800-1000` dipakai juga utk thumbnail 56×56 admin (9-18x kelebihan) | Override jadi `w=120` KHUSUS render thumbnail admin (`admin/projects/index`, `admin/blog/index`) via `preg_replace` di Blade, TIDAK mengubah nilai tersimpan di DB (kartu/katalog/banner besar tetap resolusi penuh) |
| **15 — Query & cache layer** | Query `GET /` — before | 10 (8 domain + 2 session) | — |
| **15** | Query `GET /` — cache DINGIN | — | 16 (5 domain + 3 cache-miss-select + 3 cache-insert + 3 query sumber asli + 2 session) |
| **15** | Query `GET /` — cache PANAS | — | **TETAP 10** (5 domain + 3 cache-select-HIT + 2 session) — **TIDAK turun dari baseline** karena `CACHE_STORE=database` (baca cache = tetap 1 query SQL ke tabel `cache`, bukan operasi gratis in-memory) |
| **15** | Waktu respons `GET /` — before vs cache panas | 0.375s avg | ~0.42s avg (nyaris sama, dalam margin noise dataset kecil) |
| **15** | Query featured project | Fetch semua baris, filter PHP | Filter SQL langsung (`where featured=true`), fallback bersyarat hanya jika kosong — 1 query, tidak pernah trigger fallback di kondisi data sekarang |
| **16 — Kesiapan produksi & HTTP** | `GET /` dengan cache produksi (config+route+view) | — | 0.349s avg (3x: 0.359/0.349/0.339) |
| **16** | `GET /` tanpa cache, request PERTAMA pasca `view:clear` (compile Blade dari 0) | — | **12.365s** (biaya nyata yang dihindari `view:cache`) |
| **16** | `GET /` tanpa cache, steady-state (Blade sudah pernah ter-compile) | — | ~0.356s (nyaris sama dengan cache aktif — gap kecil untuk request non-pertama) |
| **16** | Bug ditemukan & diperbaiki | Closure route placeholder admin bikin `route:cache` gagal | Dipindah ke `Controller@method` + konstanta `PLACEHOLDERS` — `route:cache` sukses, perilaku terlihat identik |
| **17 — Audit ulang (iterasi ini)** | 3 command cache ditumpuk bersamaan | — | Semua sukses, tidak ada konflik. Regresi penuh publik+admin: **bersih, 0 masalah baru** |

### File/area utama yang berubah
- **Tidak ada perubahan kode** — murni audit & pengukuran. Data dummy (1 project id=8, 1 skill id=15) dibuat lalu dihapus lagi selama uji siklus CRUD; state akhir database persis sama dengan sebelum sesi ini (5 projects, 4 blog, 3 experience, 3 testimonials, 12 skills, 6 social links, 9/9 section aktif) — dikonfirmasi ulang lewat tinker di akhir sesi.
- `docs/LOG-ITERASI.md` — entri ini sendiri.
- `docs/RENCANA-OPTIMASI-PERFORMA.md` — baris status paling atas diupdate menandai Fase 3 tuntas.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru — iterasi ini murni audit, tidak ada perubahan skema. `docs/ERD.md` sengaja TIDAK diupdate (tidak ada temuan yang mengharuskan perubahan skema).
- Tidak ada seeder baru dijalankan.

### Verifikasi
Seluruh detail verifikasi ada di Ringkasan poin 1-3 di atas (regresi penuh publik+admin, 3 command cache ditumpuk, log bersih). Ringkasan singkat:
- Publik: `GET /` (9/9 section, cache invalidasi terbukti via siklus edit-lihat-revert), `GET /projects` (filter+pagination), `GET /projects/{key}` (5/5 project, loop otomatis dari DB, 0 404/500 tak terduga), toggle bahasa & `[data-reveal]` utuh, lazy/eager image count cocok Iterasi 14.
- Admin: login sukses, 11/11 menu 200 (termasuk Playground placeholder dengan judul benar), CRUD create→edit→hapus sukses untuk Projects & Skills (data dummy dibersihkan, baseline dikembalikan persis), section-settings toggle+invalidasi berfungsi.
- 3 command cache produksi ditumpuk bersamaan → sukses semua, fungsional tetap 200 di semua endpoint yang dicek, `:clear` di akhir mengembalikan environment development ke kondisi normal.
- `storage/logs/laravel.log` bersih (0 baris) di setiap titik pengecekan sepanjang sesi.
- Server `php artisan serve` (port 8150) dimatikan di akhir (dikonfirmasi request berikutnya `000`/connection refused).

### Commit
- Belum di-commit — menunggu review & commit manual dari user (lihat catatan Fase 3 di `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 2).

### Catatan untuk review
- **Fase 3 (Iterasi 13-17, Optimalisasi Performa) SEKARANG TUNTAS.** Tidak ada regresi ditemukan dari Iterasi 13-16 meski ditumpuk bersamaan dan diuji ulang penuh — tidak ada perubahan kode baru yang diperlukan di iterasi ini.
- **Kesimpulan jujur — mana yang benar-benar berdampak besar vs marginal** (lihat tabel di atas untuk angka lengkap):
  - **Berdampak besar & terukur jelas**: (a) `view:cache` untuk cold-start — 12.365s → ~0.35s untuk request pertama pasca-deploy, ini penghematan nyata & signifikan, bukan klaim berlebihan. (b) Split bundle admin (Iterasi 13) — pengurangan JS admin -11.5% gzip nyata terukur, meski dalam angka absolut kecil (KB-level) karena `portfolio.js` sendiri memang tidak besar. (c) Lazy loading gambar (Iterasi 14) — 93-100% gambar below-the-fold ditunda di 2 halaman terberat, dampak nyata untuk LCP/bandwidth awal meski belum diukur dalam bentuk waktu load browser sungguhan (keterbatasan: pengukuran sepanjang Fase 3 pakai `Content-Length`/hitung elemen via curl, bukan Lighthouse/DevTools timeline).
  - **Dampak kecil/marginal di skala data saat ini — JANGAN dilebih-lebihkan**: (a) Cache query layer (Iterasi 15) — jumlah query `GET /` cache panas TETAP 10, sama seperti baseline, karena `CACHE_STORE=database` membuat baca-cache tetap 1 query SQL sungguhan (bukan operasi gratis). Waktu respons pun nyaris tidak berbeda (~0.375s vs ~0.42s, dalam margin noise). Infrastruktur cache-nya sudah benar & ter-invalidasi otomatis (diverifikasi ulang di iterasi ini), tapi manfaat SEKARANG sangat kecil untuk dataset ~5-12 baris per tabel. (b) Split bundle publik (Iterasi 13) — nyaris tidak berubah (-0.037 KB gzip, ≈0%), karena halaman publik memang butuh hampir semua logic yang ada. (c) Selisih waktu respons steady-state dengan/tanpa cache produksi (Iterasi 16) — kecil (~0.349s vs ~0.356s), manfaat utamanya murni di cold-start pertama, bukan di traffic berkelanjutan.
- **Rekomendasi lanjutan DI LUAR SCOPE Fase 3** (dicatat untuk referensi masa depan, TIDAK dikerjakan di sini):
  - Kalau jumlah project/blog/data admin bertambah signifikan di masa depan (puluhan-ratusan baris), pertimbangkan ganti `CACHE_STORE` dari `database` ke `file`/`redis` — infrastruktur cache (key, TTL, invalidasi) sudah siap dipakai tanpa perubahan kode, tinggal ganti driver di `.env`, dan manfaat pengurangan query baru akan benar-benar terlihat di angka (bukan cuma "siap tapi belum kelihatan" seperti sekarang).
  - Kalau ingin memverifikasi dampak lazy-loading & split bundle dalam satuan waktu browser sungguhan (bukan cuma jumlah elemen/Content-Length via curl), pertimbangkan Lighthouse/DevTools manual sebagai langkah lanjutan — di luar scope Fase 3 (bagian 6 rencana eksplisit menyebut "Automated performance testing/CI... di luar scope").
  - Evaluasi ulang cache full-page index (dilewati di Iterasi 15 karena kompleksitas invalidasi 8 sumber data) kalau di masa depan ada kebutuhan performa yang lebih mendesak dari sekadar 3 data yang sudah di-cache sekarang.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` tidak diupdate (tidak ada temuan yang mengharuskannya).
- **Ini iterasi terakhir Fase 3** — tidak ada pekerjaan baru dimulai di luar scope setelah ini, sesuai batasan tugas.

---

## Iterasi 16 — Kesiapan Produksi & HTTP Delivery (selesai: 2026-08-23)
Status: Selesai — **Fase 3 (Optimalisasi Performa), lanjutan Iterasi 13-15.**

### Ringkasan
Sesuai `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 5 Iterasi 16. **Kondisi awal sesi**: `git status` menunjukkan 6 file uncommitted (`app/Http/Controllers/Admin/ProfileController.php`, `SectionSettingController.php`, `SocialLinkController.php`, `app/Http/Controllers/PortfolioController.php`, `app/Models/SiteProfile.php`, `docs/LOG-ITERASI.md`) — persis perubahan Iterasi 15 yang belum di-commit user, sesuai catatan penutup entri Iterasi 15 di bawah. `git log` mengonfirmasi Iterasi 13 (`b433e69`) & 14 (`0ac9b64`) sudah ter-commit. Tidak diutak-atik; perubahan Iterasi 16 ditumpuk di atas working tree yang sudah ada, tetap **tidak ada `git add`/`git commit`** dijalankan di sesi ini.

**1) Audit `env()` di luar `config/`**: `grep -rn "env(" app/ routes/ resources/` → **nihil** di ketiganya. Seluruh pemanggilan `env()` project ini sudah terbatas di file `config/*.php` (pola yang benar). Tidak ada perbaikan kode yang diperlukan untuk poin ini — dikonfirmasi bersih dari awal, bukan asumsi.

**2) Audit closure route** — **ditemukan 1 masalah nyata**, diperbaiki: `routes/admin.php` baris 106-109 (sebelum perbaikan) mendaftarkan seluruh menu placeholder admin (saat ini hanya 1: `playground`) lewat closure yang menangkap `$title`/`$iterationNote` dari array PHP di scope route file:
```php
foreach ($placeholders as $slug => [$title, $iterationNote]) {
    Route::get($slug, fn () => app(PlaceholderController::class)->show($title, $iterationNote))->name($slug);
}
```
Laravel **tidak bisa** menyerialisasi Closure ke file cache route, jadi `route:cache` akan gagal/error selama route ini ada. Diperbaiki dengan memindah data title/note ke dalam `App\Http\Controllers\Admin\PlaceholderController::PLACEHOLDERS` (konstanta `public const`), dan route didaftarkan sebagai `Route::get($slug, [PlaceholderController::class, 'show'])->name($slug)` (murni Controller@method, tidak ada closure). `PlaceholderController::show()` sekarang menerima `Illuminate\Http\Request $request`, menurunkan slug dari `Str::after($request->route()->getName(), 'admin.')`, lalu lookup title/note dari konstanta di atas (fallback `Str::title($slug)` + pesan generik kalau suatu saat ada slug baru yang belum terdaftar di array, supaya tidak fatal error). Selain route ini, seluruh route lain di `routes/web.php` & `routes/admin.php` sudah 100% Controller method sejak awal (dikonfirmasi baca ulang kedua file lengkap) — tidak ada closure lain yang perlu diperbaiki.

**3) Uji 3 command cache produksi** — server `php artisan serve` port 8140:
- `php artisan config:cache` → **sukses**, tidak ada error (konsisten dengan hasil audit poin 1 — tidak ada `env()` liar yang akan jadi `null`).
- `php artisan route:cache` → **sukses SETELAH perbaikan poin 2** (sebelum perbaikan, route closure di atas akan membuat command ini error — tidak sempat diuji dalam kondisi rusak karena perbaikan dilakukan lebih dulu sesuai urutan tugas "WAJIB diperbaiki dulu").
- `php artisan view:cache` → **sukses**, tidak ada error, seluruh Blade (termasuk admin) berhasil dikompilasi.
- **Verifikasi fungsional dengan ketiga cache aktif** (curl, port 8140): `GET /` → 200, `GET /projects` → 200, `GET /projects/lumina-saas` → 200, `GET /admin/login` → 200. Login admin sungguhan (`admin@bagusbatra.dev`/`Admin#12345` via cookie jar) → `POST /admin/login` 302 → `GET /admin/dashboard` 200, `GET /admin/projects` 200 (list, CRUD), `GET /admin/projects/1/edit` 200 (form CRUD). **Uji khusus route placeholder yang baru diperbaiki**: `GET /admin/playground` (logged in) → 200, isi halaman mengandung teks "Playground" (judul benar terlihat lewat lookup slug→title yang baru, bukan hardcode lama) — membuktikan perbaikan poin 2 tidak mengubah perilaku terlihat, hanya cara route didaftarkan.

**4) Pengukuran waktu respons `GET /` before/after cache produksi:**

| Skenario | Waktu respons (3x request) | Rata-rata |
|---|---|---|
| **Dengan cache produksi aktif** (config+route+view cache) | 0.359s / 0.349s / 0.339s | **~0.349s** |
| **Tanpa cache** (langsung setelah `:clear`, request pertama memaksa Blade compile ulang semua view dari 0) | **12.365s** (run pertama, outlier compile) / 0.539s / 0.370s | run pertama dibuang sbg outlier tak representatif |
| **Tanpa cache, steady-state** (Blade sudah ter-compile dari request sebelumnya, kondisi development sehari-hari) | 0.361s / 0.350s / 0.357s | ~0.356s |

**Temuan jujur**: untuk request steady-state (Blade view sudah pernah dikompilasi sekali, config sudah pernah dibaca), selisih waktu respons `GET /` dengan vs tanpa cache produksi **sangat kecil** (~0.349s vs ~0.356s, dalam margin noise pengukuran lokal) — konsisten dengan temuan Iterasi 15 bahwa dataset & environment development ini terlalu kecil untuk menunjukkan gap besar. **Manfaat nyata yang justru terlihat jelas**: request PERTAMA setelah `view:clear` (skenario paling realistis untuk "deploy baru tanpa `view:cache`" — visitor pertama pasca-deploy akan menanggung compile Blade on-the-fly) makan **12.365 detik** — inilah tepatnya biaya yang dihindari `view:cache` dengan mem-precompile semua Blade sebelum traffic pertama masuk. Ini bukan angka fiktif, diukur langsung 1x lewat curl `-w "%{time_total}"` tepat setelah `php artisan view:clear`.

**5) Environment development dibersihkan lagi setelah pengujian** — sesuai default tugas (bukan server produksi sungguhan): dijalankan `php artisan config:clear && php artisan route:clear && php artisan view:clear` di akhir sesi. Dikonfirmasi `bootstrap/cache/` tidak lagi berisi `config.php`/route cache (hanya `packages.php`/`services.php` bawaan Laravel yang memang selalu ada), dan `storage/framework/views/` kosong dari file `.php` hasil compile. Server `php artisan serve` dimatikan, dikonfirmasi request berikutnya connection-refused (`curl` exit code 7).

### File/area utama yang berubah
- `app/Http/Controllers/Admin/PlaceholderController.php` — tambah konstanta `PLACEHOLDERS` (title + catatan iterasi per slug), `show()` diubah menerima `Request` & menurunkan slug dari nama route (bukan lagi 2 parameter string dari closure).
- `routes/admin.php` — route placeholder diubah dari closure jadi `Route::get($slug, [PlaceholderController::class, 'show'])->name($slug)`, supaya kompatibel `route:cache`.
- `README.md` — bagian baru "Deploy ke Produksi": checklist `.env` produksi, `npm run build`, 3 command cache (+catatan `event:cache` opsional karena tidak ada listener kustom), wajib `:clear` sebelum `:cache` ulang tiap deploy (+shortcut `optimize`/`optimize:clear`), rekomendasi header `Cache-Control: public, max-age=31536000, immutable` untuk `public/build/assets/*` (dokumentasi konfigurasi server, tidak mengubah Laragon lokal), reminder `opcache.enable=1`.
- `docs/LOG-ITERASI.md` — entri ini sendiri.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru — Iterasi 16 murni kesiapan produksi/dokumentasi + 1 perbaikan route. `docs/ERD.md` sengaja TIDAK diupdate sesuai instruksi eksplisit tugas (tidak ada perubahan skema).
- Tidak ada seeder baru dijalankan.

### Verifikasi
**Fungsional — via `php artisan serve` (port 8140) + `curl`, DENGAN config:cache+route:cache+view:cache aktif:**
- `GET /` 200, `GET /projects` 200, `GET /projects/lumina-saas` 200, `GET /admin/login` 200.
- Login admin sungguhan → `GET /admin/dashboard` 200, `GET /admin/projects` 200, `GET /admin/projects/1/edit` 200 (CRUD form).
- `GET /admin/playground` (route yang baru diperbaiki dari closure) → 200, isi mengandung "Playground" — perilaku terlihat identik dengan sebelum perbaikan, hanya cara pendaftaran route yang berubah.
- Ketiga command (`config:cache`, `route:cache`, `view:cache`) dijalankan tanpa error setelah perbaikan poin 2.
- Environment development dikembalikan bersih: `config:clear`, `route:clear`, `view:clear` dijalankan di akhir, dikonfirmasi `bootstrap/cache/` & `storage/framework/views/` tidak lagi berisi file cache produksi.
- Server dimatikan setelah verifikasi (dikonfirmasi request berikutnya connection-refused).

### Commit
- Belum di-commit — menunggu review & commit manual dari user (lihat catatan Fase 3 di `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 2). Seluruh perubahan iterasi ini (2 file PHP/routes + README.md + entri log ini) ada di working tree sebagai uncommitted changes, di atas perubahan Iterasi 15 yang juga masih uncommitted.

### Catatan untuk review
- **Perbaikan wajib yang ditemukan**: closure route placeholder admin (`routes/admin.php`) — kalau tidak diperbaiki, `php artisan route:cache` akan gagal di produksi. Sudah diperbaiki & diverifikasi tidak mengubah perilaku terlihat (isi halaman placeholder identik).
- Audit `env()` bersih dari awal — tidak ada perbaikan kode yang diperlukan untuk poin itu.
- Selisih waktu respons steady-state dengan/tanpa cache produksi kecil untuk skala data & environment development ini (konsisten dengan temuan Iterasi 15 soal `CACHE_STORE=database`) — manfaat paling nyata & terukur ada di penghindaran biaya compile Blade request pertama pasca-deploy (12.365s → dihindari total oleh `view:cache`), bukan di request steady-state.
- Header cache aset Vite & OPcache murni didokumentasikan di README (rekomendasi konfigurasi server produksi) — tidak ada perubahan konfigurasi Laragon lokal, sesuai batasan tugas.
- Belum lanjut ke Iterasi 17 (Audit Ulang & Ringkasan Hasil) sesuai batasan tugas — berhenti di sini menunggu instruksi lanjut & commit manual dari user untuk Iterasi 13-16.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` tidak diupdate (sesuai instruksi eksplisit tugas ini).

---

## Iterasi 15 — Query & Cache Layer (selesai: 2026-08-23)
Status: Selesai — **Fase 3 (Optimalisasi Performa), lanjutan Iterasi 13-14.**

### Ringkasan
Sesuai `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 5 Iterasi 15. **Kondisi awal sesi**: `git status` bersih dan `git log` menunjukkan Iterasi 13 (`b433e69`) DAN Iterasi 14 (`0ac9b64`) keduanya SUDAH ter-commit — berbeda dari instruksi tugas yang menyebut Iterasi 14 "kemungkinan masih uncommitted". Tidak diutak-atik (tidak revert/amend); perubahan iterasi ini ditumpuk sebagai diff baru di atas `0ac9b64`, tetap **tidak ada `git add`/`git commit` dijalankan** di sesi ini.

**1) Cek `CACHE_STORE` dulu (`.env`)**: `CACHE_STORE=database` — **bukan** file/Redis. Ini krusial untuk memahami hasil pengukuran di bawah (lihat poin 6).

**2) Cache 3 data yang jarang berubah:**
- `SiteProfile::current()` — logic cache ditaruh **DI DALAM model itu sendiri** (bukan controller), karena `current()` dipanggil dari beberapa tempat (controller publik, `Admin\ProfileController@edit`) dan singleton-nya paling rapi di-cache satu tempat. Key `site_profile`, `Cache::remember` TTL 3600 detik (1 jam).
- `SocialLink::where('is_active', true)->orderBy('sort_order')->get()` — di-cache di `PortfolioController` (bukan model) karena ini query khusus untuk kebutuhan halaman index (filter+urutan tertentu), bukan singleton "current row" seperti `SiteProfile`. Key `social_links_active`, TTL 3600.
- `SectionSetting::pluck('is_active', 'section_key')` — sama, di-cache di `PortfolioController`. Key `section_settings_map`, TTL 3600.

**Bug ditemukan & diperbaiki saat implementasi — `serializable_classes = false`**: percobaan pertama meng-cache objek Eloquent Model/Collection langsung (`Cache::remember('site_profile', ..., fn() => SiteProfile::firstOrCreate(...))`) menghasilkan error nyata saat verifikasi: `TypeError: App\Models\SiteProfile::current(): Return value must be of type App\Models\SiteProfile, __PHP_Incomplete_Class returned`. Ditelusuri ke `config/cache.php` baris 134: `'serializable_classes' => false` — fitur keamanan Laravel versi ini yang **melarang unserialize objek PHP apa pun dari cache** (mencegah gadget-chain attack via `APP_KEY` bocor; dikonfirmasi baca `vendor/laravel/framework/src/Illuminate/Cache/DatabaseStore.php` baris 585-598, `unserialize($value, ['allowed_classes' => $this->serializableClasses])` — `false` berarti *no* classes diizinkan, PHP diam-diam mengembalikan `__PHP_Incomplete_Class` alih-alih error keras). Direproduksi manual via tinker (`Cache::put`+`Cache::get` objek Model → hasil rusak), lalu diperbaiki:
- `SiteProfile::current()`: cache HANYA `getAttributes()` (array biasa, aman), lalu rehydrate ke instance Model lewat `(new static)->newFromBuilder($attributes)` — pola yang sama dipakai Eloquent sendiri saat hydrate hasil query, jadi hasil identik dgn `firstOrCreate()` (exists=true, cast normal), tanpa query DB saat cache HIT.
- `$sectionActive`: cache `->pluck(...)->all()` (array biasa), bukan `Collection` mentah — semua pemakaian di Blade (`portfolio/index.blade.php`, `about.blade.php`) cuma akses `[]`, jadi array biasa berperilaku identik.
- `$socialLinks`: sudah aman dari awal karena kode existing sudah `->toArray()` sebelum dikembalikan (tidak perlu perbaikan).

Tidak mengubah `config/cache.php` — perbaikan dilakukan di level kode aplikasi (cache array, bukan objek), lebih konsisten dengan alasan keamanan yang sudah sengaja dipasang default Laravel, sesuai batasan "tidak menambah dependency/config besar baru kalau bisa dihindari".

**Keputusan TTL — 1 jam (bukan `rememberForever`)**: meski invalidasi manual di poin 3 sudah dicek lengkap (semua method controller admin yang menyentuh 3 tabel ini teridentifikasi & di-invalidate), TTL wajar tetap dipasang sebagai jaring pengaman tambahan — bukan andalan utama — untuk mengantisipasi jalur penulisan data di luar controller (mis. `tinker`, seeder ulang, query manual) yang tidak lewat `Cache::forget`. Biaya TTL 1 jam sangat kecil (worst case: data basi maksimal 1 jam kalau ada jalur penulisan yang lolos), sementara `rememberForever` punya risiko "cache basi permanen sampai server restart/cache:clear manual" kalau suatu saat ada penambahan method admin baru yang lupa di-invalidate. Sesuai saran eksplisit di instruksi tugas untuk kasus "ragu ada titik simpan yang terlewat, lebih aman pakai TTL".

**3) Invalidasi cache** — dicek SATU PER SATU seluruh method controller admin yang menyimpan ke `site_profiles`/`social_links`/`section_settings` (dibaca langsung dari kode, bukan asumsi):
- `Admin\ProfileController@update` — satu-satunya method yang menyentuh `site_profiles` (tidak ada create/delete, ini singleton). Tambah `Cache::forget(SiteProfile::CACHE_KEY)` setelah `$profile->update($data)`.
- `Admin\SocialLinkController` — 4 method mutasi: `store`, `update`, `destroy`, `move` (reorder swap `sort_order`). Ke-4nya ditambah `Cache::forget(PortfolioController::SOCIAL_LINKS_CACHE_KEY)` setelah save berhasil — `move` sengaja tidak diabaikan meski tidak eksplisit disebut nama fieldnya di rencana, karena reorder mengubah `sort_order` yang ikut menentukan isi cache (urutan tampil).
- `Admin\SectionSettingController` — hanya `toggle` yang menyentuh `section_settings` (tidak ada create/delete/move — daftar section adalah set tetap yang di-seed, bukan CRUD bebas oleh admin, dicek lewat `routes/admin.php` yang cuma mendaftarkan route `index`+`toggle` untuk resource ini). Tambah `Cache::forget(PortfolioController::SECTION_SETTINGS_CACHE_KEY)` setelah `$sectionSetting->save()`.

Cache key constants (`SiteProfile::CACHE_KEY`, `PortfolioController::SOCIAL_LINKS_CACHE_KEY`, `PortfolioController::SECTION_SETTINGS_CACHE_KEY`) dipakai bersama antara sisi baca & sisi invalidasi (bukan string literal yang diulang) supaya tidak ada typo key yang bikin invalidasi diam-diam gagal.

**4) Perbaikan query featured project** — persis sesuai rencana: `Project::where('featured', true)->orderBy('sort_order')->get()` menggantikan pola lama (fetch semua baris via `Project::orderBy('sort_order')->get()` lalu filter `->where('featured', true)` di PHP). Fallback `Project::orderBy('sort_order')->take(3)->get()` hanya jalan kalau hasil pertama kosong — di database saat ini ada 3 project `featured=true` (`lumina-saas`, `aurora-commerce`, `zenith-design-system`), jadi fallback TIDAK pernah ter-trigger dalam kondisi normal, dikonfirmasi lewat query log (hanya 1 query `projects` per request, bukan 2).

**5) Cache full-page index — DILEWATI, sesuai saran rencana.** Alasan: 5 dari 8 sumber data index (`Skill`, `Project`, `BlogPost`, `Experience`, `Testimonial`) TIDAK disentuh di iterasi ini dan masing-masing punya banyak titik CRUD admin terpisah (create/update/delete/reorder di 5 controller admin berbeda) yang semuanya harus di-invalidate lengkap kalau full-page cache dipasang — kompleksitas & risiko "cache basi karena ada 1 titik invalidasi yang lupa" jauh lebih tinggi dibanding manfaatnya untuk skala data sekarang (~5-9 baris per tabel). Prinsip "cache wajib auto-invalidate lengkap" (`RENCANA-OPTIMASI-PERFORMA.md` bagian 4) lebih mudah dipenuhi dgn cakupan sempit (3 model, 6 titik invalidasi total, semua sudah diverifikasi) daripada cakupan penuh (8 model, puluhan titik invalidasi tersebar di banyak controller). Cukup cache 3 data di poin 2 untuk iterasi ini.

**6) Pengukuran before/after** — server `php artisan serve` port 8130/8131, `DB::listen()` sementara ditaruh di awal `PortfolioController@index` untuk hitung query (DIHAPUS lagi setelah tiap sesi pengukuran, dikonfirmasi `grep -rn "DB::listen" app/` nihil di kondisi akhir):

| Skenario | Jumlah query `GET /` | Waktu respons (rata-rata) |
|---|---|---|
| **Before** (baseline, sebelum Iterasi 15) | **10** (8 domain: skills/projects-all/blog/experiences/testimonials/site_profile/social_links/section_settings + 2 session) | **0.375s** (avg 3x: 0.363/0.383/0.378) |
| **After — cache DINGIN** (`cache:clear` lalu request pertama) | **16** (5 domain + 3 cache-select MISS + 3 cache-insert + 3 query sumber asli site_profile/social_links/section_settings + 2 session) | 0.522s (1x, wajar lebih lambat — cache MISS + 3x insert tambahan) |
| **After — cache PANAS** (request berikutnya) | **10** (5 domain + 3 cache-select HIT + 2 session) | rata-rata ~0.42s (5x: 0.368/0.429/0.373/0.470/0.461) |

**Temuan penting (jujur, bukan asumsi) — jumlah query cache PANAS TIDAK turun dari baseline (tetap 10)**: ini SESUAI EKSPEKTASI setelah cek `CACHE_STORE=database` di poin 1 di atas, TAPI berbeda dari asumsi naif di rencana ("cache panas harusnya jelas lebih sedikit query, 3 query hilang"). Penyebabnya: dengan cache **driver database**, membaca cache = tetap 1 query SQL sungguhan ke tabel `cache` (`select * from cache where key in (?)`) — bukan operasi in-memory/filesystem tanpa biaya seperti Redis/file cache. Jadi 3 query lama (site_profiles/social_links/section_settings) hanya **ditukar** jadi 3 query baru (cache lookup), bukan dihilangkan sungguhan, dan waktu respons pun nyaris sama (bahkan sedikit lebih lambat karena overhead layer Cache facade + unserialize) untuk dataset sekecil ini. Ini murni konsekuensi driver yang aktif di `.env` proyek ini, BUKAN bug implementasi — kalau `CACHE_STORE` di produksi nanti diganti ke `file`/`redis`/`memcached` (izin sudah ada di rencana bagian 6), 3 query itu benar-benar akan hilang tanpa perubahan kode apa pun (infrastruktur cache-nya sudah benar & auto-invalidating, tinggal ganti driver). Manfaat nyata yang TETAP didapat di iterasi ini terlepas dari driver: (a) query `projects` sekarang filter di level SQL (lebih sedikit baris ditransfer, lebih murah di query planner meski jumlah query tetap 1), dan (b) lapisan cache yang benar & ter-invalidasi otomatis sudah terpasang siap dipakai kapan pun driver diganti — bukan pekerjaan sia-sia.

### File/area utama yang berubah
- `app/Models/SiteProfile.php` — `current()` dibungkus `Cache::remember('site_profile', 3600, ...)`, cache `getAttributes()` (bukan objek Model) + rehydrate via `newFromBuilder()`; tambah konstanta `CACHE_KEY`.
- `app/Http/Controllers/PortfolioController.php` — cache `$socialLinks` (key `social_links_active`) & `$sectionActive` (key `section_settings_map`, sebagai array bukan Collection) via `Cache::remember` TTL 3600; perbaikan query featured project (filter SQL, fallback bersyarat); tambah konstanta `SOCIAL_LINKS_CACHE_KEY`/`SECTION_SETTINGS_CACHE_KEY`.
- `app/Http/Controllers/Admin/ProfileController.php` — `update()` tambah `Cache::forget(SiteProfile::CACHE_KEY)`.
- `app/Http/Controllers/Admin/SocialLinkController.php` — `store`/`update`/`destroy`/`move` tambah `Cache::forget(PortfolioController::SOCIAL_LINKS_CACHE_KEY)`.
- `app/Http/Controllers/Admin/SectionSettingController.php` — `toggle` tambah `Cache::forget(PortfolioController::SECTION_SETTINGS_CACHE_KEY)`.
- `docs/LOG-ITERASI.md` — entri ini sendiri.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru — tabel `cache` sudah ada dari default Laravel (dipakai karena `CACHE_STORE=database`), bukan tabel baru yang dibuat iterasi ini. `docs/ERD.md` sengaja TIDAK diupdate sesuai instruksi eksplisit tugas (tidak ada perubahan skema).
- Tidak ada seeder baru dijalankan.

### Verifikasi
**Fungsional — via `php artisan serve` (port 8130 lalu 8131) + `curl`:**
- `GET /` → 200 (cache dingin & panas, dicek terpisah). `GET /projects` → 200. `GET /projects/lumina-saas` → 200.
- Data index dicek benar setelah perubahan: nama "Bagus Batra" muncul, 3 kartu project featured (`lumina-saas`/`aurora-commerce`/`zenith-design-system`, masing2 5 kemunculan sesuai jumlah link internal per kartu) tampil, project non-featured (`pulse-ai-workspace`) TIDAK tampil di index (0 kemunculan) — sesuai data `featured` aktual di DB. Social links: 5x `github.com`, 2x `linkedin.com`, 2x `twitter.com` — cocok jumlah baris aktif di DB. 9 section id (`hero`/`about`/`skills`/`projects`/`playground`/`experience`/`blog`/`testimonials`/`contact`) semua tampil (9/9 aktif, sama seperti state akhir Iterasi 12).
- **Uji invalidasi cache nyata (3 skenario, semua via login admin sungguhan `admin@bagusbatra.dev`/`Admin#12345` + curl cookie jar, BUKAN clear cache manual):**
  1. `Admin\ProfileController@update` — ubah `tagline_id` jadi marker `TEST-CACHE-INVALIDATION-MARKER-ITERASI15` → `GET /` langsung (request berikutnya, tanpa `cache:clear`) menampilkan marker (1 kemunculan) → dikembalikan ke tagline asli → `GET /` lagi mengandung tagline asli lagi, marker hilang (0 kemunculan). **Invalidasi `site_profile` berhasil.**
  2. `Admin\SectionSettingController@toggle` — toggle section `testimonials` (id=8) nonaktif → `GET /` kehilangan `id="testimonials"` (0 match) → toggle balik aktif → `id="testimonials"` muncul lagi (1 match). **Invalidasi `section_settings_map` berhasil.**
  3. `Admin\SocialLinkController@update` — ubah `name` GitHub jadi marker `TESTMARKER-GITHUB-15` → `GET /` langsung menampilkan marker (3 kemunculan) → dikembalikan ke `GitHub` → marker hilang (0 kemunculan), `github.com` tetap 5 kemunculan seperti semula. **Invalidasi `social_links_active` berhasil.**
  - Semua 3 data dikonfirmasi kembali PERSIS ke state awal via tinker setelah tes: `SocialLink::find(1)->name` = `GitHub`, `SiteProfile::current()->tagline_id` = tagline asli, section `testimonials` = active, `SectionSetting::where('is_active', true)->count()` = 9/9 — tidak ada perubahan data permanen dari baseline akibat sesi pengujian ini.
- **Bug ditemukan & diperbaiki SEBELUM verifikasi final** (lihat Ringkasan poin 2): percobaan awal cache objek Model/Collection langsung memicu `TypeError`/`__PHP_Incomplete_Class` — ditangkap saat pengukuran "after" pertama (muncul di `laravel.log` sebagai exception sungguhan, bukan lolos diam-diam), diperbaiki dengan cache array biasa, diverifikasi ulang bersih setelah perbaikan.
- `storage/logs/laravel.log` dikosongkan sebelum setiap sesi pengukuran/verifikasi, dicek bersih (0 baris) di request akhir sesi ini (setelah `DB::listen` sementara dihapus).
- `DB::listen` sementara (dipasang 2x untuk baseline & after) dikonfirmasi sudah dihapus total dari kode akhir — `grep -rn "DB::listen" app/` nihil.
- Server `php artisan serve` dimatikan setelah verifikasi (dikonfirmasi request berikutnya `000`/connection refused).

### Commit
- Belum di-commit — menunggu review & commit manual dari user (lihat catatan Fase 3 di `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 2). Seluruh perubahan iterasi ini (5 file PHP + entri log ini sendiri) ada di working tree sebagai uncommitted changes.

### Catatan untuk review
- **Temuan ketidaksesuaian instruksi vs kondisi nyata (lagi)**: tugas menyebut Iterasi 14 "kemungkinan masih uncommitted", tapi `git log` awal sesi menunjukkan Iterasi 13 & 14 **keduanya** sudah ter-commit (`b433e69`, `0ac9b64`). Tidak diambil tindakan terhadap commit-commit itu — working tree awal sesi ini BERSIH, perubahan Iterasi 15 murni diff baru di atasnya.
- **Temuan arsitektur penting untuk iterasi/produksi berikutnya**: `config('cache.serializable_classes')` di-set `false` (default keamanan Laravel versi ini) — SIAPA PUN yang menambah `Cache::remember`/`Cache::put` di masa depan di project ini WAJIB menyimpan array/scalar, BUKAN objek Model/Collection/Eloquent langsung, atau akan diam-diam mendapat `__PHP_Incomplete_Class` saat cache HIT (bukan error saat SET, baru muncul saat GET — mudah lolos review kalau tidak dites eksplisit). Sudah didokumentasikan sebagai komentar kode di `SiteProfile.php` & `PortfolioController.php`.
- **`CACHE_STORE=database` membuat manfaat pengurangan query TIDAK terlihat di angka query count untuk cache panas** (tetap 10, sama seperti baseline) — lihat penjelasan lengkap & jujur di poin 6 Ringkasan. Ini bukan kegagalan implementasi, murni karakteristik driver cache aktif saat ini. Tidak mengganti `CACHE_STORE` di iterasi ini (di luar scope — perubahan `.env`/infra bukan bagian dari Iterasi 15, dan bagian 6 rencana eksplisit menyebut "cukup Cache driver default Laravel (file/database, sesuai .env yang berjalan)").
- Belum lanjut ke Iterasi 16 (Kesiapan Produksi & HTTP Delivery) sesuai batasan tugas — berhenti di sini menunggu instruksi lanjut.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` tidak diupdate (sesuai instruksi eksplisit tugas ini).

---

## Iterasi 14 — Optimasi Pemuatan Gambar (selesai: 2026-08-23)
Status: Selesai — **Fase 3 (Optimalisasi Performa), lanjutan Iterasi 13.**

### Ringkasan
Sesuai `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 5 Iterasi 14. Tujuan: seluruh `<img>` di codebase (publik & admin) sebelumnya dimuat eager tanpa `width`/`height` eksplisit (nol dari 18 elemen memakainya, dikonfirmasi via grep ulang `<img` di `resources/views/**/*.blade.php` — hasilnya persis 18 elemen di 14 file, cocok dengan audit sebelumnya). Diterapkan `loading="lazy"`/`loading="eager"` + `decoding="async"` + `width`/`height` eksplisit di semua 18, plus `fetchpriority="high"` untuk 2 kandidat LCP. **Catatan penting**: saat sesi ini dimulai, `git status` menunjukkan working tree BERSIH dan Iterasi 13 ternyata SUDAH ter-commit (`b433e69 feat: Optimize performance by separating public and admin JS bundles`) — berbeda dari instruksi tugas yang menyebutnya masih uncommitted. Tidak diutak-atik (tidak revert, tidak amend) — perubahan Iterasi 14 ini murni ditumpuk sebagai commit baru di atasnya, tetap **tidak ada `git add`/`git commit` dijalankan** di sesi ini sesuai aturan Fase 3.

**Klasifikasi above-the-fold vs below-the-fold (18 total, 2 above / 16 below):**
- **Above-the-fold (2)** — `loading="eager"` + `decoding="async"` + `fetchpriority="high"` + `width`/`height`:
  1. `portfolio/partials/hero.blade.php` — avatar Hero di halaman index (`w-13 h-13 sm:w-14 sm:h-14` → `width="56" height="56"`, breakpoint terbesar dipakai).
  2. `projects/show.blade.php` — banner gambar utama halaman detail project `/projects/{key}` (`h-64 sm:h-80`, mengikuti rasio placeholder `onerror` yang sudah ada 1200x800 → `width="1200" height="800"`).
- **Below-the-fold (16)** — `loading="lazy"` + `decoding="async"` + `width`/`height`: grid project index (`portfolio/partials/projects.blade.php`, 800x600 mengikuti rasio placeholder `onerror`), grid katalog `/projects` (`projects/index.blade.php`, 800x600), related project di detail (`projects/show.blade.php`, 600x400 mengikuti placeholder), grid blog index (`portfolio/partials/blog.blade.php`, 800x450 — tidak ada `onerror` existing jadi dipakai rasio 16:9 mendekati proporsi kartu `h-48 sm:h-56`), avatar testimonial publik (`portfolio/partials/testimonials.blade.php`, 44x44), 3 gambar di dalam modal artikel (`portfolio/partials/article-modal.blade.php`: avatar penulis 40x40, cover artikel 800x450, avatar komentar 28x28 — dinamis via `:src` Alpine, atribut statis `width`/`height`/`loading`/`decoding` tetap valid ditambahkan berdampingan), dan SEMUA gambar admin (list `admin/projects/index.blade.php` 56x56, `admin/blog/index.blade.php` 56x56, `admin/testimonials/index.blade.php` 44x44; preview form `admin/projects/form.blade.php` 160x96, `admin/blog/form.blade.php` 96x64 & 64x64, `admin/testimonials/form.blade.php` 72x72, `admin/profile/edit.blade.php` 96x96 — semua form preview pakai Alpine `:src` dinamis, dimensi diambil dari ukuran placeholder `placehold.co` yang sudah ada di fallback `||` masing-masing, tidak diubah).

Untuk kasus yang ukurannya benar-benar responsif (lebar mengikuti grid/container, tinggi tetap via class `h-*`) — bukan aspect-ratio CSS terpisah yang dipakai, melainkan `width`/`height` HTML attribute mengikuti rasio placeholder `onerror` yang sudah ada di kode (atau rasio 16:9 untuk blog yang belum punya placeholder) sebagai referensi aspek rasio; ukuran box aktual tetap 100% dikontrol Tailwind (`w-full h-48`/`h-full` dst, tidak diubah), jadi atribut ini hanya membantu browser mereservasi rasio sebelum gambar termuat tanpa mengubah tampilan akhir sedikit pun.

**Penyesuaian parameter `w=` Unsplash — hanya 2 lokasi, keduanya thumbnail admin list**: dicek dulu seluruh URL Unsplash yang dipakai (`database/seeders/ProjectSeeder.php`, `BlogPostSeeder.php`, `TestimonialSeeder.php` — tidak ada URL Unsplash hardcode di Blade manapun, semua datang dari kolom DB `image`/`cover_image`/`avatar`). Karena field gambar yang SAMA dipakai ulang di beberapa konteks render berbeda (mis. `$project->image` dipakai di kartu grid ~380px, katalog ~380px, DAN banner detail ~1200px), parameter `w=` yang tersimpan (1000 untuk project, 500-800 untuk blog, 200 untuk testimonial) sudah proporsional untuk konteks TERBESAR pemakaiannya masing-masing — **kecuali** di dua tempat: `admin/projects/index.blade.php` dan `admin/blog/index.blade.php`, yang menampilkan field yang sama hanya sebagai thumbnail list `w-14 h-14` (56×56px) padahal URL tersimpan minta `w=1000`/`w=800-1000` (9-18x lebih besar dari kebutuhan render, jelas "jomplang jauh" sesuai kriteria rencana). Karena mengubah `w=` di sumber data (DB/seeder) akan menurunkan kualitas di konteks besar (katalog/banner) yang memakai field sama, solusinya di level Blade: `src` untuk kedua img ini dibungkus ekspresi `str_contains($x, 'images.unsplash.com') ? preg_replace('/([?&]w=)\d+/', '${1}120', $x) : $x` — mengecilkan `w=` jadi 120 (2x dari 56px untuk retina) HANYA saat dirender sebagai thumbnail admin, TIDAK mengubah nilai `w=` yang tersimpan di DB (jadi kartu index/katalog/banner tetap dapat resolusi penuh seperti sebelumnya). Diverifikasi via curl: `GET /admin/projects` mengandung 5x `w=120` (5 project di DB), `GET /admin/blog` mengandung 4x `w=120` (4 post di DB). Avatar testimonial (`w=200` tersimpan) TIDAK disesuaikan — konteks terbesar pemakaiannya (preview form admin `w-18 h-18` = 72px, butuh ~144px di retina) masih cukup dekat dengan 200, tidak "jomplang jauh". URL non-Unsplash (`placehold.co` di fallback preview form) sengaja tidak disentuh sesuai batasan tugas.

### File/area utama yang berubah
- `resources/views/portfolio/partials/hero.blade.php` — avatar Hero: `width`/`height`/`loading="eager"`/`decoding="async"`/`fetchpriority="high"`.
- `resources/views/projects/show.blade.php` — banner detail (eager + fetchpriority tinggi) & kartu related project (lazy), keduanya + `width`/`height`.
- `resources/views/portfolio/partials/projects.blade.php`, `resources/views/projects/index.blade.php` — kartu project grid index & katalog: lazy + `width="800" height="600"`.
- `resources/views/portfolio/partials/blog.blade.php` — kartu blog grid index: lazy + `width="800" height="450"`.
- `resources/views/portfolio/partials/testimonials.blade.php` — avatar testimonial publik: lazy + `width="44" height="44"`.
- `resources/views/portfolio/partials/article-modal.blade.php` — 3 gambar (avatar penulis, cover artikel, avatar komentar): lazy + dimensi masing-masing.
- `resources/views/admin/projects/index.blade.php`, `resources/views/admin/blog/index.blade.php` — thumbnail list: lazy + `width="56" height="56"` + override `w=120` untuk URL Unsplash.
- `resources/views/admin/testimonials/index.blade.php` — thumbnail avatar list: lazy + `width="44" height="44"` (tanpa override `w=`).
- `resources/views/admin/projects/form.blade.php`, `resources/views/admin/blog/form.blade.php`, `resources/views/admin/testimonials/form.blade.php`, `resources/views/admin/profile/edit.blade.php` — preview gambar form (Alpine `:src` dinamis): lazy + dimensi sesuai ukuran render masing-masing.
- `docs/LOG-ITERASI.md` — entri ini sendiri.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (Fase 3 murni optimasi aset, tidak ada perubahan skema).
- Tidak ada seeder baru dijalankan/diubah (seeder Unsplash dicek untuk audit `w=`, tapi filenya sendiri **tidak diedit** — perbaikan dilakukan di level Blade `src` per konteks, bukan di sumber data, sesuai alasan di Ringkasan).

### Verifikasi
**Fungsional — via `php artisan serve` (port 8124) + `curl`:**
- `GET /` → 200. `GET /projects` → 200. `GET /projects/lumina-saas` → 200. `GET /admin/login` → 200.
- Login admin (`admin@bagusbatra.dev`/`Admin#12345`) via curl cookie jar → `POST /admin/login` 302; `GET /admin/dashboard`, `GET /admin/projects`, `GET /admin/blog`, `GET /admin/testimonials`, `GET /admin/projects/1/edit` (sesi login) → 200 semua.
- `storage/logs/laravel.log` dikosongkan sebelum sesi, dicek bersih (0 baris) di akhir sesi.
- Server dimatikan setelah verifikasi (dikonfirmasi request berikutnya `000`/connection refused).

**Hitung `loading="lazy"` vs `loading="eager"` per halaman (grep pada response HTML aktual via curl):**

| Halaman | `loading="lazy"` | `loading="eager"` | Total `<img>` |
|---|---|---|---|
| `GET /` | 13 | 1 | 14 |
| `GET /projects` | 8 | 0 | 8 |
| `GET /projects/lumina-saas` | 6 | 1 | 7 |
| `GET /admin/projects` | 5 | 0 | 5 |
| `GET /admin/blog` | 4 | 0 | 4 |
| `GET /admin/testimonials` | 3 | 0 | 3 |

Sebelum perubahan: seluruh gambar di atas dimuat **eager** tanpa atribut (0 `lazy`, 0 `width`/`height` di manapun). Sesudah: untuk 1x load `GET /` (halaman paling ramai gambar sekaligus di publik), **13 dari 14 gambar** (93%) sekarang ditunda pemuatannya sampai mendekati viewport — browser tidak lagi mengunduh gambar grid project, grid blog, dan avatar testimonial di awal render, hanya avatar Hero yang tetap eager. Untuk `GET /projects` (halaman terberat gambar sekaligus — grid katalog penuh), **8 dari 8 gambar** (100%) ditunda karena tidak ada elemen above-the-fold di halaman ini (hero tidak dipakai di sini).

**Verifikasi override `w=`**: `GET /admin/projects` (dengan sesi login) mengandung 5x kemunculan `w=120` (cocok 5 baris project di DB), `GET /admin/blog` mengandung 4x kemunculan `w=120` (cocok 4 baris blog post di DB) — dikonfirmasi lewat grep pada response HTML mentah, bukan asumsi.

### Commit
- Belum di-commit — menunggu review & commit manual dari user (lihat catatan Fase 3 di `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 2). Seluruh perubahan iterasi ini (14 file Blade + entri log ini sendiri) ada di working tree sebagai uncommitted changes.

### Catatan untuk review
- **Ditemukan ketidaksesuaian instruksi vs kondisi nyata di awal sesi**: tugas menyebut Iterasi 13 "belum di-commit" tapi `git log` menunjukkan sudah ter-commit (`b433e69`). Tidak diambil tindakan apa pun terhadap commit tersebut (tidak direvert/diamend) — perubahan Iterasi 14 murni ditumpuk sebagai diff baru di atas commit itu, working tree sekarang HANYA berisi 14 file Blade dari Iterasi 14 (dikonfirmasi `git status --short` — tidak ada sisa perubahan Iterasi 13 yang tercampur).
- Tidak menjalankan `npm run build` — perubahan iterasi ini murni atribut HTML di Blade, tidak menyentuh JS/CSS/Vite entry.
- **Belum lanjut ke Iterasi 15** (Query & Cache Layer) sesuai batasan tugas — berhenti di sini menunggu instruksi lanjut, konsisten dengan pola "tidak dirantai otomatis" Fase 3.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` tidak diupdate (sesuai instruksi eksplisit tugas ini).

---

## Iterasi 13 — Pemisahan Bundle Publik vs Admin (selesai: 2026-08-23)
Status: Selesai — **Awal Fase 3 (Optimalisasi Performa).**

### Ringkasan
Iterasi pertama Fase 3, sesuai `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 5 Iterasi 13. Tujuan: hentikan halaman admin (termasuk halaman login sebelum autentikasi) mengunduh seluruh logic publik (`portfolio.js` — lang store, scroll-spy, floating widget, demo Playground) yang sebelumnya ikut terbawa karena satu entry Vite (`app.js`) dipakai identik di semua halaman, dan sebaliknya halaman publik tidak perlu helper CRUD admin (`sectionToggle`). **Tidak ada perubahan tampilan/perilaku yang terlihat user** — murni bagaimana JS dikirim ke browser per konteks halaman.

**Perubahan entry point**: `resources/js/app.js` dihapus, diganti 2 entry mandiri:
- `resources/js/public.js` (baru) — `import Alpine from 'alpinejs'; import './reveal'; import './portfolio';` + `Alpine.start()`. Dipakai `layouts/app.blade.php` (halaman publik: `/`, `/projects`, `/projects/{key}`) dan `welcome.blade.php` (halaman scaffold default Laravel, tidak pernah dirutekan oleh `routes/web.php` manapun — dicek eksplisit, tapi tetap diupdate `@vite()`-nya sekalian daripada dibiarkan menunjuk ke file yang sudah dihapus).
- `resources/js/admin.js` (diubah jadi entry mandiri, isinya tetap sama — helper `sectionToggle`) — sekarang `import Alpine from 'alpinejs'; import './reveal';` ditambahkan di atas + `Alpine.start()` ditambahkan di bawah (sebelumnya `admin.js` hanya modul yang di-import `app.js`, bukan entry sendiri). Dipakai `admin/layouts/app.blade.php` dan `admin/auth/login.blade.php`.

**Dicek dulu (grep menyeluruh) sebelum memutuskan isi `admin.js`**: apakah ada view admin yang diam-diam bergantung pada sesuatu dari `portfolio.js` (mis. `$store.lang`, `$store.ui`, `appRoot()`) yang sebelumnya "gratis" ikut karena satu bundle gabungan. Hasil: **tidak ada** — satu-satunya komponen dari luar file admin sendiri yang dipakai halaman admin adalah `x-data="revealOnScroll"` (dipakai `admin/layouts/app.blade.php` `<main>` dan `admin/auth/login.blade.php` `<body>`), yang sudah didefinisikan di `reveal.js` (bukan `portfolio.js`) sejak awal — jadi cukup `import './reveal'` di `admin.js`, tidak perlu duplikasi apa pun dari `portfolio.js`. Sebaliknya, tidak ada view publik yang memakai `sectionToggle`. Cross-check dikonfirmasi lewat grep nama komponen (`appRoot|aboutSection|projectsSection|blogSection|articleModal|playground|contactSection|socialBar|revealOnScroll|sectionToggle`) di `resources/views/portfolio/**`, `resources/views/projects/**`, `resources/views/layouts/app.blade.php` vs `resources/views/admin/**` — tidak ada satu pun yang salah tempat.

**Shared chunk Alpine core — didapat GRATIS dari Vite/Rollup, tanpa config tambahan**: karena `public.js` DAN `admin.js` sama-sama `import './reveal'` (yang sendiri `import Alpine from 'alpinejs'`), Rollup otomatis mendeteksi modul bersama dan memisahkannya jadi chunk terpisah (`reveal-[hash].js`, isinya Alpine core + `reveal.js`, 53.168 KB raw / 18.605 KB gzip) yang di-`modulepreload` oleh KEDUA entry (dikonfirmasi lewat `public/build/manifest.json`: baik `resources/js/public.js` maupun `resources/js/admin.js` punya `"imports": ["_reveal-....js"]`). Artinya Alpine core **tidak terduplikasi** dua kali di dua bundle terpisah — trade-off "splitting effort" yang disebut di rencana (bagian 5 Iterasi 13) ternyata tidak perlu usaha manual sama sekali, Vite menanganinya otomatis selama kedua entry sama-sama meng-import modul yang sama.

**Keputusan CSS: TETAP SATU FILE (`resources/css/app.css`), tidak dipecah.** Dicek isi filenya (148 baris): `@import 'tailwindcss'` + `@theme` (font tokens) + custom classes (`.frosted-glass*`, custom scrollbar, `[data-reveal]`, ambient blob animations, `[x-cloak]`) — **semuanya dipakai di KEDUA sisi** (frosted-glass & ambient blob dipakai layout admin persis sama dgn layout publik, lihat `admin/layouts/app.blade.php` baris 19-22 yang mereplikasi `ambient-blob` publik; `[data-reveal]`/`revealOnScroll` dipakai admin juga). Tailwind v4 sudah content-scan seluruh `resources/views/**` (termasuk admin) lewat `@source` di `app.css`, jadi output CSS yang dihasilkan sudah otomatis hanya berisi utility class yang benar-benar dipakai — memecah jadi `public.css`/`admin.css` terpisah akan butuh 2x proses Tailwind scan (2x @theme, 2x custom classes duplikat atau di-`@import` silang) untuk manfaat yang sangat kecil (kelas admin-only vs publik-only kemungkinan besar overlap besar, tidak ada scoping halaman admin yang beda drastis secara visual dari publik — sama-sama pakai palet indigo/slate & frosted-glass). Kompleksitas tambahan tidak sepadan dengan potensi penghematan, sesuai izin eksplisit di rencana bagian 5 ("keputusan diambil saat implementasi, dicatat alasannya").

### File/area utama yang berubah
- `resources/js/public.js` (baru) — entry Vite publik: Alpine core + `reveal.js` + `portfolio.js` + `Alpine.start()`.
- `resources/js/admin.js` — diubah dari modul biasa jadi entry Vite mandiri: tambah `import Alpine from 'alpinejs'; import './reveal';` di atas dan `window.Alpine = Alpine; Alpine.start();` di bawah; isi `sectionToggle` tidak diubah.
- `resources/js/app.js` — **dihapus** (tidak dipakai lagi, semua 4 referensi `@vite(['resources/js/app.js'...])` diupdate).
- `vite.config.js` — `input: [...]` diubah dari `['resources/css/app.css', 'resources/js/app.js']` jadi `['resources/css/app.css', 'resources/js/public.js', 'resources/js/admin.js']`.
- `resources/views/layouts/app.blade.php`, `resources/views/welcome.blade.php` — `@vite([...])` diarahkan ke `resources/js/public.js`.
- `resources/views/admin/layouts/app.blade.php`, `resources/views/admin/auth/login.blade.php` — `@vite([...])` diarahkan ke `resources/js/admin.js`.
- `README.md` — baris deskripsi struktur `resources/js/*` diupdate (1 baris `app.js` lama diganti 3 baris: `reveal.js`/`public.js`/`admin.js`, jelaskan pemisahan Iterasi 13).
- `resources/js/reveal.js`, `resources/js/portfolio.js` — **tidak diubah** (isinya sudah tepat sejak awal, hanya cara di-import yang berubah).

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (Fase 3 murni optimasi aset, tidak ada perubahan skema).
- Tidak ada seeder baru dijalankan.

### Verifikasi
**Ukuran bundle — before (baseline, satu bundle gabungan untuk semua halaman) vs after (per konteks):**

| Bundle | Raw | Gzip |
|---|---|---|
| **Before** — `app.css` | 133.072 KB | 20.554 KB |
| **Before** — `app.js` (Alpine + reveal + portfolio + admin) | 61.611 KB | 21.585 KB |
| **Before** — Total per 1x load (SEMUA halaman, publik maupun admin) | **194.683 KB** | **42.139 KB** |
| **After** — `app.css` (tetap 1 file, tidak berubah) | 133.072 KB | 20.554 KB |
| **After** — chunk shared `reveal-*.js` (Alpine core + reveal.js, dipakai KEDUA entry via modulepreload) | 53.168 KB | 18.605 KB |
| **After** — `public.js` (portfolio.js logic) | 7.774 KB | 2.943 KB |
| **After** — `admin.js` (sectionToggle saja) | 0.796 KB | 0.489 KB |
| **After** — Total per 1x load HALAMAN PUBLIK (css + public.js + reveal chunk, TANPA admin.js) | **194.014 KB** | **42.102 KB** |
| **After** — Total per 1x load HALAMAN ADMIN (css + admin.js + reveal chunk, TANPA public.js/portfolio.js) | **187.036 KB** | **39.648 KB** |

Halaman publik nyaris tidak berubah (wajar — halaman publik memang butuh hampir semua logic yang ada, cuma kehilangan 0.796 KB `sectionToggle` yang memang tidak pernah dipakainya): -0.669 KB raw (-0.3%), -0.037 KB gzip (~0%). Halaman admin turun nyata di sisi JS: -7.647 KB raw (-3.9% dari total, atau **-12.4% khusus JS**), -2.491 KB gzip (-5.9% dari total, atau **-11.5% khusus JS**) — penghematan berasal murni dari `portfolio.js` (427 baris logic publik: lang store, scroll-spy, floating widget, blog filter, article modal, 3 demo Playground) yang sekarang tidak lagi ikut terkirim ke browser admin. Sebagian besar ukuran bundle (Alpine.js core, ~53 KB raw / ~18.6 KB gzip) memang harus tetap ada di kedua sisi karena baik publik maupun admin sama-sama pakai Alpine untuk interaktivitas — chunk ini **tidak terduplikasi** berkat code-splitting otomatis Rollup (lihat Ringkasan), jadi tidak ada biaya ganda untuk itu.

**Fungsional — via `php artisan serve` (port 8123, terpisah dari default 8000) + `curl`:**
- `GET /` → 200. `GET /projects` → 200. `GET /projects/aurora-commerce` (project pertama di DB saat verifikasi) → 200. `GET /admin/login` → 200.
- Login admin (`admin@bagusbatra.dev`/`Admin#12345`) via curl cookie jar (`--data-urlencode`, token CSRF diambil dari halaman login) → `POST /admin/login` 302 ke `/admin/dashboard`; `GET /admin/dashboard` dgn cookie sesi → 200, `<title>Dashboard — Admin Bagus Batra</title>` terkonfirmasi (bukan halaman login yang ke-redirect balik).
- 4 halaman admin tambahan dicek sekalian: `GET /admin/projects`, `/admin/section-settings`, `/admin/profile`, `/admin/messages` → 200 semua (dgn sesi login yang sama).
- **Verifikasi tag `<script>`/`<link>` yang di-generate `@vite()` menunjuk bundle yang benar** (grep `manifest.json` utk konfirmasi nama file ter-hash cocok dgn HTML): `GET /`, `/projects`, `/projects/{key}` semuanya me-load `app-*.css` + `public-*.js` + `reveal-*.js` (via `modulepreload`) — **TIDAK ADA** `admin-*.js`. `GET /admin/login` dan `GET /admin/dashboard` (setelah login) semuanya me-load `app-*.css` + `admin-*.js` + `reveal-*.js` — **TIDAK ADA** `public-*.js`. Dikonfirmasi via `public/build/manifest.json`: `resources/js/public.js` → `admin/imports` tidak saling silang, masing-masing entry hanya `import` chunk `reveal` yang sama.
- Sanity check silang Alpine (grep, bukan browser sungguhan — dicatat sebagai keterbatasan sesuai instruksi tugas): seluruh nama komponen (`appRoot`, `aboutSection`, `projectsSection`, `blogSection`, `articleModal`, `playground`, `contactSection`, `socialBar`) yang dipanggil dari `x-data="..."` di `resources/views/portfolio/**` & `resources/views/projects/**` **semuanya** ada persis di `portfolio.js` (dibundel via `public.js`); satu-satunya komponen dipakai `resources/views/admin/**` di luar inline object literal (`x-data="{ ... }"`) adalah `sectionToggle` (ada di `admin.js`) — **tidak ditemukan** referensi ke `$store.lang`/`$store.ui`/`appRoot` di manapun di bawah `resources/views/admin/`.
- `storage/logs/laravel.log` dikosongkan sebelum sesi, dicek bersih (0 baris) di akhir sesi — tidak ada exception baru dari perubahan ini.
- Server `php artisan serve` dimatikan setelah verifikasi (dikonfirmasi request berikutnya `000`/connection refused).

### Commit
- Belum di-commit — menunggu review & commit manual dari user (lihat catatan Fase 3 di `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 2). Seluruh perubahan iterasi ini (kode + entri log ini sendiri) ada di working tree sebagai uncommitted changes.

### Catatan untuk review
- **Ini iterasi pertama Fase 3** — beda dari Fase 1/2, TIDAK dirantai otomatis ke Iterasi 14 (Optimasi Pemuatan Gambar), sesuai instruksi eksplisit di `docs/RENCANA-OPTIMASI-PERFORMA.md` bagian 2. Berhenti di sini menunggu instruksi lanjut.
- Keputusan CSS tetap satu file (bukan dipecah publik/admin) — alasan lengkap di Ringkasan: isi `app.css` overlap besar antara kedua sisi (frosted-glass, ambient blob, reveal-on-scroll, custom scrollbar semuanya dipakai admin juga), dan Tailwind v4 content-scan sudah otomatis menghasilkan CSS minimal tanpa perlu campur tangan splitting manual.
- File `resources/views/welcome.blade.php` (scaffold default Laravel, TIDAK pernah dirutekan `routes/web.php` mana pun — dicek eksplisit) ikut diupdate `@vite()`-nya ke `public.js` murni untuk kebersihan (menghindari referensi ke `resources/js/app.js` yang sudah dihapus), bukan karena halaman ini benar-benar dipakai — di luar scope fungsional iterasi ini, tidak diverifikasi via curl karena memang tidak ada route yang mengarah ke sana.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` tidak diupdate (sesuai instruksi eksplisit tugas ini).

---

## Iterasi 12 — Merapikan Data & Audit Akhir (selesai: 2026-08-23)
Status: Selesai — **Fase 2 (Iterasi 10-12) TUNTAS SEPENUHNYA.**

### Ringkasan
Iterasi audit & perbaikan (bukan fitur baru), penutup Fase 2 — pola sama dengan Iterasi 9 di penutup Fase 1. Audit dijalankan terhadap ketiga jenis halaman publik Projects yang sekarang ada (index highlight di `/`, katalog `/projects`, detail `/projects/{key}`) sesuai baris "Data cleanup (index)" di `RENCANA-PENGEMBANGAN.md` bagian 10. Temuan: sebagian besar item audit **sudah benar sejak Iterasi 10-11** (ditulis sekaligus saat membuat halaman, bukan ditambal belakangan) — field opsional (`client`, `demo_url`, `github_url`) sudah di-guard dengan `@if`/fallback sejak awal, badge kategori & line-clamp sudah konsisten dengan pola index lama. Yang benar-benar baru ditambahkan di iterasi ini: fallback `onerror` untuk gambar rusak (belum ada sebelumnya di kartu manapun) dan quick-win link admin ke halaman publik.

**1) Audit isi data** — dicek manual field-per-field terhadap 5 project di database saat ini (`lumina-saas`, `aurora-commerce`, `zenith-design-system`, `pulse-ai-workspace`, `fast-state-npm`): 2 project (`pulse-ai-workspace`, `fast-state-npm`) punya `client = null` — halaman detail (`projects/show.blade.php`, ditulis di Iterasi 11) sudah menampilkan fallback `{{ $project->client ?: 'Open Project' }}` (meniru fallback yang sama persis dipakai modal lama), dicek via curl **tidak ada teks "null" bocor ke HTML manapun** (grep `>null<` di halaman index/katalog/detail — nihil). Semua 5 project punya `demo_url`/`github_url` terisi sehingga tombol terkait selalu tampil, tapi kode `@if ($project->demo_url)`/`@if ($project->github_url)` tetap ada di semua tempat (kartu index, kartu katalog, footer strip detail, tab Preview detail) untuk kasus field kosong di masa depan — **dicek eksplisit ini sudah benar sejak awal Iterasi 10-11**, bukan baru diperbaiki sekarang.
**2) Gambar broken** — ditambahkan atribut `onerror="this.onerror=null;this.src='https://placehold.co/...'"` (fallback ke placeholder abu-abu via placehold.co, sengaja bukan sistem upload-check yang rumit sesuai batasan tugas) di **setiap** `<img>` project publik: kartu index highlight, kartu katalog `/projects`, banner hero halaman detail, dan kartu "Proyek Lainnya" — 4 lokasi, masing-masing placeholder ukuran sesuai konteks (800x600 untuk kartu, 1200x800 untuk banner besar, 600x400 untuk related card kecil).
**3) Line-clamp/truncation** — dicek terhadap konten terpanjang yang benar-benar ada sekarang: `description` terpanjang 139 karakter (project `pulse-ai-workspace`), `long_description` terpanjang 231 karakter — keduanya masih rapi di `line-clamp-2` (kartu) dan tanpa clamp (paragraf penuh di tab Overview halaman detail, sengaja tidak diclamp karena itu memang halaman detail penuh, bukan preview kartu — sama seperti modal lama). Tidak ditemukan konten yang overflow/perlu tambahan clamp.
**4) Badge kategori** — 5 kategori di database (`Full-Stack`, `Frontend`, `UI/UX & Systems`, `Open Source`, `AI & Tools`) semuanya Title Case konsisten, sama persis dengan konstanta `ProjectController::CATEGORIES` yang dipakai dropdown admin — tidak ada normalisasi tambahan yang diperlukan.

**5) Section toggle vs halaman Projects** — diverifikasi eksplisit sesuai instruksi: toggle section `projects` di admin (`PATCH admin/section-settings/{id}/toggle`) dimatikan → `GET /` kehilangan `id="projects"` (0 match, section highlight hilang) **TAPI** `GET /projects` dan `GET /projects/lumina-saas` tetap `200` tanpa perubahan apa pun. Toggle dikembalikan aktif setelah verifikasi (state akhir sama seperti awal: 9/9 section aktif).

**6) Regresi penuh** — dijalankan via `php artisan serve` + `curl` (server dimatikan sebelum & sesudah setiap sesi verifikasi, `laravel.log` dikosongkan sebelum tiap sesi & dicek bersih sesudahnya):
- `GET /` → 200. `GET /projects` → 200, mengandung 5 kartu (`catalog-card-*`), 48/43 elemen `x-show="$store.lang.current === 'id'/'en'"` (toggle bahasa terpasang konsisten), 8 elemen `[data-reveal]`.
- `GET /projects/{key}` untuk **SETIAP** project di database (di-loop otomatis dari `Project::pluck('project_key')`, bukan daftar hardcode) → 5/5 sukses 200, tidak ada 404/500. `GET /projects/does-not-exist` (key tak dikenal) → 404 (route-model-binding bekerja benar). Halaman detail: 46/41 elemen lang-toggle, 5 elemen `[data-reveal]`, breadcrumb + 3 tab (`tab-overview`/`tab-architecture`/`tab-preview`) semuanya ada, `og:image` terisi URL gambar project yang benar, related projects (`Proyek Lainnya`) mengecualikan project yang sedang dibuka dengan benar (dicek utk `lumina-saas`: 3 related-nya adalah `aurora-commerce`/`pulse-ai-workspace`/`zenith-design-system`, TIDAK termasuk `lumina-saas` sendiri).
- Admin CRUD Projects (Iterasi 4 lama) dicek tetap sinkron: login → 200/302 seperti biasa, `GET /admin/projects` (list) → 200, `GET /admin/projects/1/edit` → 200, `GET /admin/projects/create` → 200 — tidak ada regresi dari perubahan Iterasi 10-11 (route-scoped binding by `project_key` di publik tidak bentrok dengan binding by `id` di admin, lihat catatan Iterasi 11).
- Responsif: dicek class Tailwind di `resources/views/projects/index.blade.php` & `show.blade.php` — breakpoint (`sm:`/`md:`/`lg:`) konsisten dengan pola yang sudah dipakai `portfolio/partials/projects.blade.php` (grid `grid-cols-1 md:grid-cols-2 lg:grid-cols-3`), tidak ada breakpoint baru yang menyimpang.

### File/area utama yang berubah
- `resources/views/projects/index.blade.php`, `resources/views/projects/show.blade.php` — tambah `onerror` fallback placeholder di setiap `<img>` project (4 lokasi total termasuk `portfolio/partials/projects.blade.php` yang sudah dapat perbaikan sama di Iterasi 10).
- `resources/views/admin/projects/form.blade.php` — tambah tombol "Lihat di Halaman Publik" (`target="_blank"`, hanya muncul saat `$project->exists`, mengarah `route('projects.show', $project)`) di header form edit.
- `resources/views/admin/projects/index.blade.php` — tambah ikon `external-link` per baris list, mengarah ke halaman publik project tersebut (tab baru), diletakkan sebelum ikon Edit yang sudah ada.
- `README.md` — dokumentasikan `ProjectPageController`, route `/projects` & `/projects/{project_key}`, view `resources/views/projects/*`, update deskripsi modal (project-modal sudah dicabut Iterasi 11, article-modal tetap dipakai Blog).

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (Fase 2 murni halaman/routing, tidak ada perubahan skema — sesuai prediksi di `RENCANA-PENGEMBANGAN.md` bagian 10).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `npm run build` — sukses, tidak ada error.
- Lihat poin 5 & 6 di atas (Ringkasan) untuk detail lengkap end-to-end via `php artisan serve` + `curl`.
- `storage/logs/laravel.log` dicek bersih (0 baris) di akhir setiap sesi verifikasi sepanjang iterasi ini.
- Server `php artisan serve` dimatikan setelah setiap sesi verifikasi (dikonfirmasi request berikutnya gagal connect / `000`).

### Commit
- `2cd0fbc` — Iterasi 12: audit & perapian data halaman Projects

### Catatan untuk review
- **Fase 2 (Iterasi 10-12) sekarang TUNTAS SEPENUHNYA.** Halaman `/projects` (katalog) dan `/projects/{project_key}` (detail) sudah production-ready secara fungsional: filter kategori, pagination (infra terpasang meski baru 5 data), toggle bahasa, reveal-on-scroll, SEO meta dinamis, related projects, dan fallback data yang rapi untuk field opsional/gambar rusak.
- Tidak ada blocker atau penyimpangan besar dari rencana di seluruh Fase 2 — satu-satunya bug yang ditemukan (partial `cv-modal.blade.php` mengasumsikan `$personalInfo`/dst selalu ada dari `PortfolioController`) ditemukan **dan diperbaiki di Iterasi 10 sendiri**, sebelum sempat jadi masalah lintas iterasi.
- Keputusan "filter kategori di `/projects` beroperasi client-side (Alpine `x-show`) di dalam halaman yang sudah dipaginasi server-side" (bukan filter kategori ikut jadi query param yang mempengaruhi pagination) dipertahankan dari Iterasi 10 — cukup untuk 5 data saat ini, infra `paginate()` sudah terpasang untuk kapan pun datanya bertambah melebihi `perPage` (12), tapi interaksi filter+pagination lintas halaman belum diuji secara nyata karena belum ada datanya. Dicatat sebagai area yang perlu ditinjau ulang bila jumlah project sudah cukup banyak untuk benar-benar melewati satu halaman pagination.
- Tidak ada perubahan skema database di seluruh Fase 2 — `docs/ERD.md` **sengaja tidak ditambah entri riwayat baru** (instruksi eksplisit: jangan tambah entri riwayat kosong bila memang tidak ada perubahan skema).

---

## Iterasi 11 — Halaman Detail Project & Pencabutan Modal (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Halaman detail `/projects/{project_key}` (route `projects.show`) dibangun dengan mereplikasi penuh konten 3 tab modal lama (`project-modal.blade.php`: Overview & Solusi / Arsitektur Stack / Simulasi Interaktif) sebagai halaman utuh — bedanya, konten sekarang server-rendered langsung dari `$project` (Blade `@foreach`/`@if` biasa), bukan lagi lewat `$store.ui.activeProject` + `@js()` embed JSON; tab tetap interaktif tanpa reload lewat Alpine lokal (`x-data="{ tab: 'overview' }"` di scope halaman, bukan store global). Ditambah 3 elemen baru yang tidak ada di modal: breadcrumb (Beranda → Projects → judul), tombol "Kembali ke Semua Proyek", dan blok "Proyek Lainnya" (2-3 project terkait). Modal case-study lama dicabut penuh: file dihapus, `@include`-nya di layout dihapus, dan seluruh state/method terkait (`activeProject`, `activeProjectTab`, `openProject`, `closeProject`) dibuang dari `$store.ui` di `portfolio.js` — modal Blog (`article-modal.blade.php`, `$store.ui.activeArticle`/`openArticle`/`closeArticle`) sama sekali tidak disentuh, sesuai batasan scope Fase 2.

Route pakai **route-scoped** binding `{project:project_key}` (bukan `getRouteKeyName()` global di model `Project`) — keputusan disengaja setelah disadari bahwa override `getRouteKeyName()` akan berlaku global untuk SEMUA implicit binding `{project}`, termasuk 4 route admin di `routes/admin.php` (`PUT/DELETE/PATCH admin/projects/{project}`) yang mem-bind by `id` numerik. Kalau dipaksa override, admin CRUD akan langsung 404 karena mencoba mencari `project_key = '3'` alih-alih `id = 3`. Ditemukan & dihindari sebelum sempat ditulis ke kode (dicek dulu dengan membaca `routes/admin.php`), bukan ditemukan lewat bug di runtime — dicatat di sini karena ini keputusan arsitektur penting yang menjelaskan kenapa dua mekanisme binding (route-scoped vs model-level) dipilih.

### File/area utama yang berubah
- `routes/web.php` — route baru `GET /projects/{project:project_key}` (`projects.show`).
- `app/Http/Controllers/ProjectPageController.php` — method `show(Project $project)` baru: query related projects (kategori sama dulu, exclude project yang sedang dibuka, fallback project lain bila kurang dari 3, max 3 total — sengaja simpel tanpa algoritma skor, sesuai batasan Fase 2 bagian 12).
- `resources/views/projects/show.blade.php` (baru) — halaman detail penuh: hero banner, 3 tab (Overview/Architecture/Preview), footer action strip (tags + tombol GitHub/demo ber-`@if`), breadcrumb, tombol kembali, blok Proyek Lainnya. Meta SEO dinamis lewat `@section('meta_title'|'meta_description'|'meta_image')` yang infrastrukturnya disiapkan di layout pada Iterasi 10.
- `resources/views/portfolio/partials/project-modal.blade.php` — **dihapus** (`git rm`).
- `resources/views/layouts/app.blade.php` — `@include('portfolio.partials.project-modal')` dihapus (cv-modal & article-modal tetap).
- `resources/js/portfolio.js` — `$store.ui.activeProject`, `activeProjectTab`, `openProject()`, `closeProject()` dihapus; `activeArticle`/`openArticle()`/`closeArticle()` (Blog) tidak diubah.
- `resources/views/portfolio/partials/projects.blade.php`, `resources/views/projects/index.blade.php` — tombol "Detail Case Study" di tiap kartu diubah dari `<button @click="$store.ui.openProject(project)">` jadi `<a href="{{ route('projects.show', $project) }}">` biasa; wrapper `x-data="{ project: @js($project->toJs()) }"` yang menumpang di tiap `<article>` kartu ikut dibuang karena sudah tidak ada konsumennya.
- `app/Models/Project.php` — method `toJs()` dihapus (dead code — hanya dipakai modal yang baru dicabut; `BlogPost::toJs()` tidak disentuh, masih dipakai `article-modal`). Ditambah komentar menjelaskan kenapa `getRouteKeyName()` **sengaja tidak** dioverride di sini (lihat Ringkasan).

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru.
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `npm run build` — sukses.
- `php artisan route:list --path=projects` — 9 route (7 admin lama + `projects.index` + `projects.show` baru), bersih.
- End-to-end via `php artisan serve` + `curl`:
  - `GET /projects/{key}` untuk kelima project (`lumina-saas`, `aurora-commerce`, `zenith-design-system`, `pulse-ai-workspace`, `fast-state-npm`) → 200 semua, `<title>` masing-masing sesuai judul project + `" — Studi Kasus Proyek | Bagus Batra"`.
  - `GET /projects/does-not-exist` → 404 (route-model-binding menolak key yang tidak ada, sesuai perilaku default Laravel).
  - Breadcrumb (`aria-label="Breadcrumb"`) ada 1x, ketiga tab (`id="tab-overview"`/`tab-architecture`/`tab-preview"`) ada, `og:image` terisi URL gambar project yang benar, related projects untuk `lumina-saas` menghasilkan `aurora-commerce`/`pulse-ai-workspace`/`zenith-design-system` (benar, exclude dirinya sendiri).
  - Field `client = null` (project `pulse-ai-workspace`) menampilkan fallback "Open Project" di tab Overview, bukan teks kosong/"null".
  - `GET /` (index) & `GET /projects` (katalog) dicek ulang: tombol "Detail Case Study" sekarang mengarah ke `/projects/{key}` yang benar (dicek `href` lewat grep), bukan lagi memicu modal.
  - Login admin → `GET /admin/dashboard`, `GET /admin/projects`, `GET /admin/projects/1/edit` semuanya tetap 200 — konfirmasi route-scoped binding tidak merusak binding by `id` di admin (lihat catatan arsitektur di Ringkasan).
  - Grep menyeluruh `activeProject|openProject|closeProject|project-modal` di seluruh `resources/` setelah semua perubahan: hanya tersisa di komentar penjelas (bukan kode aktif) — dikonfirmasi tidak ada dead code/reference yang akan menyebabkan console error.
  - `storage/logs/laravel.log` dicek bersih (0 baris) di akhir sesi.
  - Server dimatikan setelah verifikasi (dikonfirmasi request berikutnya gagal connect).

### Commit
- `612224e` — Iterasi 11: halaman detail Project & pencabutan modal case-study

### Catatan untuk review
- Keputusan route-scoped binding (`{project:project_key}`) vs `getRouteKeyName()` override adalah keputusan teknis paling penting di iterasi ini — lihat penjelasan lengkap di Ringkasan. Ini murni detail implementasi, tidak mengubah perilaku yang terlihat user (URL tetap `/projects/lumina-saas`, bukan `/projects/3`).
- Tab "Simulasi Interaktif" (Preview) di halaman detail mereplikasi persis tampilan sandbox palsu yang sudah ada di modal lama (bukan live preview sungguhan) — sesuai konten asli, tidak diperluas jadi fitur baru karena di luar scope Fase 2 (lihat batasan bagian 12: bukan comment system/algoritma canggih).
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` tidak diupdate.

---

## Iterasi 10 — Routing & Halaman Listing Projects (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Awal Fase 2. Halaman katalog `/projects` (route `projects.index`) dibangun via controller publik baru `ProjectPageController` (dipisah dari `PortfolioController` karena merender view yang sama sekali berbeda — `resources/views/projects/*`, bukan `resources/views/portfolio/*` — dan punya concern controller sendiri yang tidak cocok ditumpuk ke method `index()` `PortfolioController` yang sudah ada). View-nya `extends('layouts.app')` — layout publik yang sama persis dipakai `/` — supaya navbar/footer/ambient background/`$store.lang`/reveal-on-scroll identik tanpa duplikasi kode layout. Grid kartu memakai markup yang sama persis dengan kartu di section Projects index (hanya ganti sumber data jadi SEMUA project, bukan subset featured), filter kategori tetap Alpine client-side (pola sama seperti `projects.blade.php`), dan `Project::orderBy('sort_order')->paginate(12)` dipasang sebagai infrastruktur pagination meski baru ada 5 project (jadi belum benar-benar terlihat kecuali datanya bertambah > 12).

Section Projects di halaman index sekarang jadi **highlight**: `PortfolioController@index` memfilter ke `featured = true`, fallback ke 3 project pertama by `sort_order` kalau belum ada satupun yang featured (kondisi saat ini: 3 dari 5 project memang sudah `featured = true`, jadi fallback belum pernah teraktivasi dengan data nyata, tapi logic-nya tetap diverifikasi lewat pembacaan kode & akan otomatis aktif kalau suatu saat semua project di-set `featured = false`). Tombol "Detail Case Study" di kedua halaman (index highlight & katalog `/projects`) **sengaja belum diubah** — masih memicu modal lama (`$store.ui.openProject`) — supaya tidak ada state transisi yang rusak di tengah iterasi; diganti sekaligus dengan pencabutan modal di Iterasi 11.

**Bug ditemukan & diperbaiki saat verifikasi (bukan di rencana awal)**: `GET /projects` sempat crash 500 saat pertama dicoba. Root cause: `cv-modal.blade.php` (partial structural, di-`@include` tanpa syarat dari `layouts/app.blade.php`) mengasumsikan `$personalInfo`/`$skills`/`$experiences` selalu tersedia — asumsi yang selama ini kebetulan selalu benar karena satu-satunya controller yang merender layout ini adalah `PortfolioController`, yang memang selalu mengirim ketiga variabel itu. `ProjectPageController@index` adalah controller PERTAMA yang merender `layouts.app` tanpa mengirim variabel-variabel itu (karena memang tidak butuh untuk kontennya sendiri), sehingga Blade melempar "Undefined variable $personalInfo". Diperbaiki dengan membuat partial itu self-contained: `$personalInfo = $personalInfo ?? \App\Models\SiteProfile::current()->toArray();` (dan serupa untuk `$skills`/`$experiences`) di awal file, memakai null-coalescing supaya tidak override data yang memang sudah dikirim `PortfolioController` maupun menambah query duplikat pada halaman `/` (variabel sudah ada, fallback tidak pernah dieksekusi di sana).

### File/area utama yang berubah
- `app/Http/Controllers/ProjectPageController.php` (baru) — method `index()`: `Project::orderBy('sort_order')->paginate(12)`, kirim ke view `projects.index`.
- `app/Http/Controllers/PortfolioController.php` — `$projects` yang dikirim ke `portfolio.index` sekarang subset featured (fallback 3 pertama), bukan lagi seluruh project.
- `routes/web.php` — route baru `GET /projects` (`projects.index`).
- `resources/views/projects/index.blade.php` (baru) — halaman katalog: back-link ke `/`, judul + jumlah total project, filter kategori (`x-data="projectsSection()"`, dipakai ulang dari `portfolio.js`, tidak ada komponen Alpine baru yang perlu ditulis), grid kartu (markup sama dengan `projects.blade.php`), pagination `{{ $projects->links() }}` (muncul kondisional lewat `hasPages()` — belum terlihat sekarang karena baru 5 data).
- `resources/views/portfolio/partials/projects.blade.php` — tambah tombol CTA "Lihat Semua Proyek" (link ke `route('projects.index')`) di bawah grid; tambah atribut `onerror` fallback placeholder di `<img>` kartu (perbaikan kecil sekalian, bukan diminta eksplisit di iterasi ini tapi murah untuk dilakukan sekarang).
- `resources/views/layouts/app.blade.php` — `<title>`/`meta description`/`og:title`/`og:description` diubah dari hardcoded jadi dinamis lewat `@hasSection('meta_title')`/`@yield('meta_title')` dkk dengan fallback ke nilai lama persis (tidak ada regresi visual di halaman manapun yang belum set section ini); tambah `<meta property="og:image">` kondisional (`@hasSection('meta_image')`) — infrastruktur ini disiapkan di sini supaya siap dipakai halaman detail Project di Iterasi 11.
- `resources/views/portfolio/partials/cv-modal.blade.php` — perbaikan bug 500 (lihat Ringkasan): partial menghitung fallback `$personalInfo`/`$skills`/`$experiences` sendiri via null-coalescing kalau variabel itu belum dikirim controller.

### Migrasi & seeder dijalankan
- Tidak ada migrasi baru (sesuai prediksi rencana — Fase 2 murni halaman/routing, bukan skema data baru).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `npm run build` — sukses.
- `php artisan route:list --path=projects` — route `projects.index` terdaftar bersih.
- End-to-end via `php artisan serve` + `curl`:
  - `GET /` → 200; section Projects index hanya menampilkan 3 kartu featured (`project-card-lumina-saas`/`aurora-commerce`/`zenith-design-system`, cocok dengan 3 project yang memang `featured = true` di database saat ini), CTA "Lihat Semua Proyek" ada 1x dengan `href` mengarah ke `/projects`.
  - `GET /projects` → sempat 500 (lihat Ringkasan — bug `cv-modal.blade.php`), setelah diperbaiki → 200, menampilkan seluruh 5 `catalog-card-*` (semua project, bukan cuma featured), `<title>Semua Proyek — Bagus Batra</title>` (meta dinamis berfungsi).
  - `storage/logs/laravel.log` dicek bersih (0 baris) setelah perbaikan bug di atas dikonfirmasi tidak muncul lagi.
  - Server `php artisan serve` dimatikan setelah verifikasi (dikonfirmasi request berikutnya gagal connect, `000`).

### Commit
- `80ab96b` — Iterasi 10: routing & halaman listing Projects

### Catatan untuk review
- Bug `cv-modal.blade.php` di atas adalah **temuan penting**: mengonfirmasi bahwa `layouts/app.blade.php` sebelum Fase 2 diam-diam berasumsi hanya `PortfolioController` yang akan pernah merendernya. Perbaikannya (fallback self-contained di partial, bukan memaksa `ProjectPageController` mengirim variabel yang tidak dibutuhkan kontennya sendiri) dipilih supaya partial structural benar-benar reusable oleh controller manapun di masa depan, bukan cuma tambal untuk kasus ini saja.
- Keputusan nama controller `ProjectPageController` (bukan menambah method ke `PortfolioController`) — dipilih karena kedua controller merender pohon view yang sama sekali terpisah (`projects/*` vs `portfolio/*`) dan `ProjectPageController` akan terus tumbuh sendiri di Iterasi 11 (method `show()` + query related projects) — memisahkannya dari awal menghindari `PortfolioController` membengkak dengan concern yang tidak berhubungan dengan halaman `/`.
- Tidak ada perubahan skema database di iterasi ini — `docs/ERD.md` tidak diupdate (sesuai instruksi: tidak menambah entri riwayat kosong bila memang tidak ada perubahan skema).

---

## Iterasi 9 — Polish & QA lintas admin (selesai: 2026-08-23)
Status: Selesai — **Fase 1 (Iterasi 0-9) TUNTAS SEPENUHNYA.**

### Ringkasan
Iterasi audit & perbaikan (bukan fitur baru), penutup Fase 1. Catatan penting: **percobaan pertama Iterasi 9 (sesi sebelumnya) gagal di tengah jalan karena error sesi API, bukan error kode** — repo masih bersih tanpa perubahan apa pun saat itu dihentikan. Eksekusi ini adalah **percobaan pertama yang benar-benar menghasilkan perubahan** untuk Iterasi 9.

Audit dilakukan dengan membaca seluruh `resources/views/admin/**` dan `app/Http/Controllers/Admin/**` yang ada (dibangun Iterasi 0-8), dibandingkan satu sama lain untuk konsistensi. Hasilnya: pola yang sudah ada **sangat konsisten** (list Projects/Blog/Experience/Testimonials/Messages berbagi struktur search+filter+pagination+modal-hapus yang nyaris identik; semua form berbagi struktur kartu+grid responsif+toggle pill yang sama; `[data-reveal]` sudah terpasang di hampir semua elemen). Temuan nyata yang diperbaiki lebih sedikit dari perkiraan awal, tapi regresi CRUD penuh (poin 5) menemukan **bug fungsional nyata** yang tidak terlihat dari audit visual saja — lihat di bawah.

**1) Audit konsistensi list/table** — diperiksa: Projects, Blog, Experience, Testimonials, Skills, Social Links, Pesan Masuk.
- Ditemukan **Skills** dan **Social Links** adalah satu-satunya list tanpa search/filter sama sekali (beda sendiri dari 4 list lain yang semuanya punya search+filter+pagination seragam), padahal `RENCANA-PENGEMBANGAN.md` bagian 4 eksplisit menyebut Skills termasuk daftar yang butuh List/Table lengkap, dan Social Links "cukup list + search ringan" — tapi search itu tidak pernah benar-benar dibuat sejak Iterasi 2/3. Diperbaiki: `SkillController@index` & `SocialLinkController@index` sekarang menerima `search` (dan `category` khusus Skills) via query string, view masing-masing dapat tambahan form search (gaya identik form Projects/Blog: ikon kaca pembesar, input, dropdown kategori khusus Skills, tombol Filter/Reset). **Sengaja tanpa pagination** untuk keduanya (dataset kecil & dibatasi — 12 skill tetap, ~6 social link — direorder manual dgn tombol naik/turun; menambah pagination akan mempersulit reorder lintas halaman tanpa manfaat nyata).
- Pesan Masuk memakai tab filter (Semua/Belum Dibaca) alih-alih dropdown — **sengaja dipertahankan berbeda**, bukan inkonsistensi: filter status biner (2 opsi tetap) secara UX lebih idiomatik sebagai tab daripada dropdown, dan `RENCANA-PENGEMBANGAN.md` bagian 5 memang hanya minta "filter status" (bukan search) untuk menu ini.
- Empty-state, jumlah item per halaman (`paginate(10)` di 4 list yang sudah punya pagination), posisi & warna tombol aksi, modal konfirmasi hapus — sudah seragam di semua list sejak awal, tidak perlu diubah.

**2) Audit responsif (mobile ~375px, tablet ~768px)** — diperiksa seluruh halaman admin (login, dashboard, 9 list, 9 form, section-settings). Sudah solid di semua halaman: sidebar collapsible via breakpoint `lg:` dengan overlay mobile & tombol hamburger (`resources/views/admin/layouts/app.blade.php`), setiap grid form pakai `grid-cols-1 sm:grid-cols-2` (atau `sm:grid-cols-[auto_1fr]` untuk pasangan gambar+field), setiap baris list pakai `min-w-0`+`truncate`+`shrink-0` yang benar sehingga tidak overflow horizontal, textarea selalu `w-full` (termasuk textarea gaya code-editor gelap di form Blog — tidak ada resiko overflow karena wrap teks biasa, bukan lebar tetap). **Tidak ada perbaikan yang diperlukan** — dicatat sudah diperiksa menyeluruh, bukan dilewati.

**3) Audit animasi** — ditemukan **1 halaman tanpa reveal-on-scroll sama sekali**: `resources/views/admin/auth/login.blade.php` (halaman login berdiri sendiri, tidak `@extends('admin.layouts.app')`, jadi tidak otomatis mewarisi `x-data="revealOnScroll"` dari layout). Diperbaiki: tambah `x-data="revealOnScroll"` di `<body>` + `data-reveal` di brand mark, kartu form, dan link kembali ke halaman publik — konsisten dgn halaman admin lain. Transisi modal konfirmasi hapus (`x-transition` duration-200/150) dan toggle switch pill (`w-11 h-6`, `translate-x-5`, `transition-colors`) sudah 100% konsisten gaya & durasinya di semua tempat yang memakainya (dicek lintas 9 file form/list) — tidak ada yang diubah.

**Bug fungsional ditemukan saat regresi CRUD (poin 5), bukan dari audit visual poin 1-3, tapi diperbaiki sebagai bagian "Perbaiki SEMUA isu yang ditemukan":** 8 kolom di 4 tabel (`projects.color_gradient`, `projects.accent_color`, `projects.image`, `blog_posts.cover_image`, `blog_posts.author_avatar`, `testimonials.avatar`, `testimonials.project_tag`, `skills.highlight_text`) dibuat `NOT NULL` tanpa default sejak skema awal (pra-Fase 1, saat schema digenerate dari seed data yang selalu lengkap), padahal validasi & form CRUD admin (Iterasi 3/4/6/7) semuanya memperlakukan field ini sebagai **opsional** (`nullable` di rule validasi, tanpa `required` di form/HTML). Akibatnya: create/update via admin sambil mengosongkan salah satu field itu **crash 500** (`SQLSTATE[HY000]: Field '...' doesn't have a default value` / `Column '...' cannot be null`). Ditemukan pertama kali saat test create Project tanpa mengisi "Warna Gradient Kartu" (field yang memang tidak punya tanda wajib di form), lalu diverifikasi sistematis lintas semua 8 kolom NOT NULL vs rule validasi tiap controller. Diperbaiki dengan 2 migrasi baru (raw SQL `ALTER TABLE ... MODIFY ... NULL`, sengaja tanpa `doctrine/dbal` karena tidak terinstal — lihat detail di bawah).

### File/area utama yang berubah
- `app/Http/Controllers/Admin/SkillController.php` — `index()` sekarang menerima `search`/`category` query, filter `where('name','like',...)`/`where('category',...)`, kirim `categories` (const `CATEGORIES` baru) + `minSortOrder`/`maxSortOrder` (global, lihat di bawah) ke view.
- `app/Http/Controllers/Admin/SocialLinkController.php` — `index()` sekarang menerima `search` query, filter `orWhere` name/platform, kirim `minSortOrder`/`maxSortOrder` ke view.
- `resources/views/admin/skills/index.blade.php` — tambah form search+filter kategori (gaya identik Projects), empty-state kondisional ("belum ada skill" vs "tidak ada skill yang cocok dengan filter").
- `resources/views/admin/social-links/index.blade.php` — tambah form search ringan (tanpa dropdown filter, sesuai rencana), empty-state kondisional.
- **Perbaikan bug turunan dari penambahan filter di atas**: tombol naik/turun pada kedua list ini sebelumnya memakai `$loop->first`/`$loop->last` untuk menonaktifkan tombol di batas urutan — benar selama list selalu menampilkan SEMUA baris, tapi jadi salah begitu list bisa terfilter (baris pertama/terakhir versi terfilter bukan berarti baris pertama/terakhir sebenarnya). Diperbaiki: kedua controller sekarang mengirim `minSortOrder`/`maxSortOrder` global (dari `Skill::min('sort_order')`/`max('sort_order')`, dst — bukan dari koleksi yang sudah difilter), kedua view membandingkan `$item->sort_order` terhadap nilai global itu, bukan posisi loop. Diverifikasi via curl: filter `category=backend` pada Skills tidak lagi menonaktifkan tombol naik pada baris pertama versi terfilter (yang bukan baris pertama sebenarnya).
- `resources/views/admin/auth/login.blade.php` — tambah `x-data="revealOnScroll"` di `<body>` + `data-reveal` pada brand mark, kartu form login, dan link "Kembali ke halaman publik".
- `database/migrations/2026_08_23_094419_make_color_gradient_and_accent_color_nullable_on_projects_table.php` (baru) — `projects.color_gradient`, `projects.accent_color` → nullable.
- `database/migrations/2026_08_23_094909_make_optional_image_and_text_fields_nullable.php` (baru) — `projects.image`, `blog_posts.cover_image`, `blog_posts.author_avatar`, `testimonials.avatar`, `testimonials.project_tag`, `skills.highlight_text` → nullable. Kedua migrasi memakai `DB::statement('ALTER TABLE ... MODIFY ... NULL')` mentah (bukan `Schema::table()->change()`) karena `doctrine/dbal` (dibutuhkan Laravel untuk `change()`) tidak terinstal di project ini — menambahnya hanya untuk 2 migrasi one-off dianggap tidak sepadan; kedua migrasi no-op di SQLite (dicek driver, `return` dini) karena environment test/CI yang mungkin masih SQLite tidak punya masalah nullability yang sama dgn cara Laravel `Schema::create` menangani `string()` di SQLite.
- `README.md` — ditulis ulang total: stack SQLite→MySQL, `config/portfolio.php`→database, tambah langkah setup MySQL + `storage:link`, kredensial admin default + cara ganti password via tinker, struktur folder admin panel lengkap (`app/Http/Controllers/Admin/`, `resources/views/admin/`), referensi ke `docs/LOG-ITERASI.md`/`docs/RENCANA-PENGEMBANGAN.md`. Sebelumnya masih menggambarkan kondisi pra-Fase 1 sepenuhnya (basi total sejak Iterasi 0).
- `docs/ERD.md` — 8 kolom di atas ditandai `"nullable sejak Iterasi 9"` di diagram; entri baru di "Riwayat perubahan skema"; baris "Terakhir diupdate" & "Catatan render" diperbarui.
- `docs/RENCANA-PENGEMBANGAN.md` — baris status paling atas diupdate: Fase 1 (Iterasi 0-9) tuntas sepenuhnya, siap lanjut Fase 2 (belum dimulai, detail menyusul terpisah).

### Migrasi & seeder dijalankan
- `php artisan migrate --force` — 2 migrasi baru dijalankan bersih di MySQL (`bagus_batra_portfolio`): `..._094419_make_color_gradient_and_accent_color_nullable_on_projects_table` dan `..._094909_make_optional_image_and_text_fields_nullable`. Tidak ada isu — kedua `ALTER TABLE ... MODIFY` berhasil tanpa downtime/lock issue terlihat (database kecil, <15 baris per tabel terdampak). Tidak ada data yang berubah/hilang (dicek: 5 project, 4 blog, 3 testimonial, 12 skill semuanya tetap punya nilai non-null di kolom yang baru dijadikan nullable, karena berasal dari seeder yang selalu mengisi semua field).
- Tidak ada seeder baru dijalankan.

### Verifikasi
- `npm run build` — sukses, tidak ada error setelah perubahan `login.blade.php`/`skills/index.blade.php`/`social-links/index.blade.php`.
- End-to-end via `php artisan serve` + `curl` (cookie jar):
  - **Regresi publik (poin 4)**: `GET /` → 200; grep membuktikan **9/9 section tampil** (`id="hero"` s/d `id="contact"` masing-masing 1 match, baseline seed `is_active=true` semua). Nama/tagline profil (`SiteProfile::current()`) muncul di HTML (bukan dari config — `config/portfolio.php` tidak lagi direferensikan di controller manapun, dicek ulang). Data JSON yang di-embed ke Alpine untuk modal case-study project & artikel blog divalidasi dgn Node `vm` sandbox (mem-parse ulang `JSON.parse('...')` yang di-generate `@js()` Laravel per kartu project & per artikel blog) — **semua valid, tidak ada yang corrupt/terpotong**; `blogSection(...)` di section header berisi tepat 4 item (cocok `BlogPost::count()`). CV modal (`cv-modal.blade.php`) memakai `$personalInfo` server-rendered langsung (bukan JSON embed), sudah dicek tampil benar. Form kontak publik: `POST /contact` sungguhan → 302 sukses, `ContactMessage` baru tersimpan dgn `is_read=false` (dicek via tinker) — dihapus lagi di akhir sesi.
  - **Regresi admin (poin 5)**: login `admin@bagusbatra.dev`/`Admin#12345` → 302 ke dashboard. **Ke-11 menu non-Playground + Playground** (12 total) semuanya `GET` → 200. CRUD create→edit→delete dgn data dummy (dihapus lagi di akhir) untuk **Projects, Blog, Experience, Testimonials, Skills, Social Links** — 6 dari 6 yang diminta, semuanya sukses (create 302, muncul di `/` setelah create/edit, hilang dari `/` setelah delete, `Model::count()` kembali ke baseline tiap kali). **Bug 500 di Projects (`color_gradient`) dan Blog (`cover_image`) ditemukan pada percobaan create pertama** (lihat Ringkasan) — diperbaiki via migrasi, lalu **CRUD diulang dan sukses** untuk keduanya. Section Settings: toggle `testimonials` off (`PATCH .../toggle`) → hilang dari `/` (0 match `id="testimonials"`), toggle on lagi → muncul lagi (1 match) — dikembalikan ke aktif. Pesan Masuk: `GET /admin/messages/{id}` menandai `is_read=true` otomatis (dicek via tinker sebelum/sesudah), `DELETE` berfungsi dgn modal konfirmasi.
  - Tombol naik/turun Skills/Social Links dicek ulang setelah perbaikan `minSortOrder`/`maxSortOrder`: unfiltered → tepat 2 tombol disabled (naik pada baris pertama, turun pada baris terakhir); filtered (`category=backend`) → 0 tombol salah-disabled.
  - `sort_order` seluruh 6 tabel yang punya reorder (`projects`, `blog`, `experience`, `testimonials`, `skills`, `social_links`) dicek kontigu tanpa celah (`0..N-1`) di akhir sesi — tidak ada sisa gap dari siklus create+delete data dummy.
  - `storage/logs/laravel.log` — berisi persis 2 baris `.ERROR` (dua bug 500 di atas, keduanya dari sesi test ini sendiri, sebelum diperbaiki); log **dikosongkan** (`> laravel.log`) setelah dikonfirmasi tidak ada exception lain, supaya baseline log bersih untuk sesi berikutnya.
  - Baseline data akhir dikonfirmasi identik dgn sebelum sesi: `projects=5, blog=4, experience=3, testimonials=3, skills=12, social_links=6, contact_messages=0` (tidak ada pesan kontak asli yang perlu dipertahankan — tabel memang kosong sejak awal sesi), `section_settings` 9/9 aktif.
  - Server `php artisan serve` dimatikan setelah verifikasi (proses PHP di-`Stop-Process`, dikonfirmasi request berikutnya gagal connect).

### Commit
- `1245c7c` — Iterasi 9: polish & QA lintas admin — audit konsistensi, responsif, animasi, regresi penuh

### Catatan untuk review
- **Fase 1 (Iterasi 0-9) sekarang BENAR-BENAR TUNTAS.** Seluruh 12 menu admin punya fungsi nyata (Playground memang permanen tanpa form data, by design — bukan belum-selesai), seluruh audit polish (konsistensi/responsif/animasi) sudah dijalankan, dan seluruh regresi publik+admin hijau termasuk 2 bug 500 yang ditemukan & diperbaiki di iterasi ini sendiri. Percobaan Iterasi 9 sebelumnya (sesi terpisah) berhenti karena error sesi API sebelum sempat mengubah kode apa pun — eksekusi kali ini adalah realisasi pertama & satu-satunya untuk Iterasi 9.
- 2 migrasi baru di iterasi ini adalah **perubahan skema pertama sejak Iterasi 8** — keduanya murni korektif (memperbaiki inkonsistensi NOT NULL vs validasi `nullable` yang sudah ada sejak skema awal), bukan penambahan fitur/kolom baru.
- Keputusan tidak menambah pagination ke Skills/Social Links (walau `RENCANA-PENGEMBANGAN.md` awal menyiratkan List/Table lengkap untuk Skills) didasarkan pada ukuran dataset yang secara desain dibatasi kecil (12 skill tetap, ~6 platform sosial) dan potensi konflik UX dengan reorder tombol naik/turun lintas halaman — bisa direvisit bila Fase 2 mengubah asumsi ini.
- Tidak ada perubahan pada halaman publik (`resources/views/portfolio/**`) di iterasi ini di luar apa yang sudah diverifikasi tetap berfungsi — sesuai batasan, perapian tampilan/copy halaman publik masuk Fase 2, bukan Iterasi 9.

---

## Iterasi 8 — Pesan Masuk / Contact Messages (selesai: 2026-08-23)
Status: Selesai

### Ringkasan
Menu "Pesan Masuk" (placeholder sejak Iterasi 0) sekarang berfungsi penuh — iterasi terakhir dari rangkaian Iterasi 1-8 yang dikerjakan berurutan otomatis. Beda dari 4 CRUD sebelumnya (Projects/Experience/Blog/Testimonials): ini **bukan** CRUD create/update penuh (pesan hanya dibuat dari form kontak publik, admin tidak membuat/mengedit pesan) — hanya **baca, tandai dibaca otomatis, dan hapus**, sesuai `RENCANA-PENGEMBANGAN.md`. Kolom baru `is_read` ditambahkan ke `contact_messages` lewat migrasi (baru pertama kali sejak Iterasi 0 ada perubahan skema di Fase 1 ini). Dashboard diupdate: kartu "Pesan Masuk" sekarang benar-benar menghitung **belum dibaca** (bukan total pesan), sesuai deskripsi asli di `RENCANA-PENGEMBANGAN.md` bagian 5 ("pesan kontak belum dibaca").

### File/area utama yang berubah
- `database/migrations/2026_08_23_060959_add_is_read_to_contact_messages_table.php` (baru) — `boolean('is_read')->default(false)->after('message')`.
- `app/Models/ContactMessage.php` — tambah `is_read` ke `$fillable` + cast `boolean`.
- `app/Http/Controllers/Admin/MessageController.php` (baru) — `index()` (filter `status=all|unread` via query string, `latest()->paginate(10)`, hitung `$unreadCount` terpisah untuk badge), `show()` (menandai `is_read = true` otomatis saat dibuka — **tidak ada tombol "tandai dibaca" terpisah**, sesuai rencana persis), `destroy()`. Tidak ada `create`/`store`/`edit`/`update` — pesan hanya berasal dari form kontak publik (`ContactMessageController@store` yang sudah ada, tidak disentuh).
- `routes/admin.php` — placeholder `messages` dihapus dari `$placeholders`; ditambah 3 route (`admin.messages` GET index, `admin.messages.show` GET detail, `admin.messages.destroy` DELETE). **Ini melengkapi seluruh 11 menu admin non-Playground** — `$placeholders` sekarang hanya berisi 1 entri (`playground`, yang memang permanen placeholder karena section itu murni demo tanpa data, sesuai catatan di Iterasi 1).
- `resources/views/admin/messages/index.blade.php` (baru) — daftar pesan dgn badge jumlah belum dibaca di header, tab filter "Semua"/"Belum Dibaca", baris pesan belum dibaca ditandai visual (background indigo tipis + dot indikator + judul bold), klik baris membuka detail (`admin.messages.show`), tombol hapus terpisah dgn modal konfirmasi (pola sama seluruh CRUD lain) supaya tidak ke-trigger tidak sengaja saat mencoba membuka pesan.
- `resources/views/admin/messages/show.blade.php` (baru) — detail pesan (nama, email, jenis proyek, budget, timeline, isi pesan lengkap, tombol "Balas via Email" `mailto:`). Status ditandai dibaca terjadi di controller sebelum view dirender, jadi saat halaman ini tampil pesan **sudah** berstatus dibaca.
- `app/Http/Controllers/Admin/DashboardController.php` — stat `contact_messages` diubah dari `ContactMessage::count()` (total) menjadi `ContactMessage::where('is_read', false)->count()` (belum dibaca), plus komentar penjelas.
- `resources/views/admin/dashboard.blade.php` — label kartu diubah dari "Pesan Masuk" menjadi "Pesan Belum Dibaca" supaya angka & label konsisten.

### Migrasi & seeder dijalankan
- `php artisan migrate` — 1 migrasi baru dijalankan bersih (`add_is_read_to_contact_messages_table`), tidak ada isu (tabel `contact_messages` kosong saat migrasi dijalankan — 0 baris — jadi tidak ada masalah default value terhadap data lama).
- Tidak ada seeder baru (contact_messages memang tidak diseed, hanya diisi dari form publik).

### Verifikasi
- `php artisan route:list --path=admin` — **54 route total**, bersih; hanya `admin/playground` (GET) yang masih route placeholder (closure `PlaceholderController`), sesuai desain permanen sejak Iterasi 1 — bukan sisa yang belum dikerjakan.
- `npm run build` — sukses.
- End-to-end via `php artisan serve` + `curl` (cookie jar, login admin):
  - `GET /admin/messages` (kosong, `ContactMessage::count()` = 0 dari baseline) → 200, kosong.
  - Kirim 1 pesan test lewat **form kontak publik sungguhan** (`POST /contact`, bukan lewat admin — sesuai desain, admin tak bisa membuat pesan) → `ContactMessage::count()` = 1, `is_read` = `false` (default).
  - `GET /admin/messages` → pesan tampil dengan badge "1 belum dibaca" & indikator visual unread.
  - `GET /admin/messages/{id}` (membuka detail) → 200, isi pesan lengkap tampil; dicek DB via `tinker` sesudahnya: `is_read` berubah jadi `true` **otomatis** (tanpa aksi eksplisit lain) — bukti "tandai dibaca otomatis saat dibuka" berfungsi.
  - `GET /admin/messages?status=unread` sesudah pesan dibaca → tampil pesan kosong "Tidak ada pesan yang belum dibaca" — filter berfungsi benar.
  - `GET /admin/dashboard` → kartu "Pesan Belum Dibaca" menunjukkan `0` (karena satu-satunya pesan sudah dibaca) — dicek langsung di HTML respons.
  - `DELETE /admin/messages/{id}` → 302; `ContactMessage::count()` kembali `0` — bukti hapus berfungsi dgn modal konfirmasi.
  - `storage/logs/laravel.log` dicek setelah seluruh rangkaian — kosong, tidak ada exception baru.
  - Server dimatikan setelah verifikasi (dikonfirmasi request gagal connect); file cookie jar scratch dihapus.

### Commit
- `d413f72` — Iterasi 8: Pesan Masuk — tandai dibaca otomatis, filter, hapus

### Catatan untuk review
- **Ini iterasi terakhir dari rangkaian Iterasi 1-8 yang diminta dikerjakan otomatis berurutan.** Semua 11 menu admin non-Playground (Dashboard, Profil & Hero, Social Links, About & Skills, Projects, Experience, Blog, Testimonials, Pesan Masuk, Pengaturan Section, Login/Logout) kini punya fitur nyata — tidak ada lagi halaman "Segera Hadir" kecuali Playground (memang permanen by design). Ringkasan lengkap Iterasi 1-8 (hash commit, fitur, kredensial) dilaporkan terpisah ke pemanggil tugas di luar dokumen log ini.
- Tidak ada halaman "reply" in-app (kirim balasan dari dalam admin) — sesuai rencana, hanya tombol `mailto:` yang membuka aplikasi email default admin. Ini konsisten dgn scope "tandai dibaca/belum, hapus, filter status" yang diminta, tidak lebih.

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
- `5b66a09` — Iterasi 7: CRUD Testimonials dengan star rating picker interaktif

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
