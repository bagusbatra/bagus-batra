# Rencana Pengembangan — Kustomisasi Tampilan Halaman Index (Fase 4)

Status: **TUNTAS — Fase 4 (Iterasi 18-23) selesai sepenuhnya per 2026-08-25.** Fondasi draft/publish, preset warna & logo, animasi & efek, reorder section & jumlah item, toggle sub-elemen & custom heading, dan mode maintenance seluruhnya dibangun & terverifikasi — lihat entri "Iterasi 23 — Audit & QA Penutup Fase 4" di `docs/LOG-ITERASI.md` untuk ringkasan hasil audit gabungan akhir (semua fitur diuji bersamaan dalam 1 sesi draft, tidak ditemukan regresi, tidak ada perubahan kode di Iterasi 23). TIDAK ADA COMMIT OTOMATIS dijalankan di seluruh Fase 4 (lihat bagian 2) — tiap iterasi di-commit manual oleh user setelah selesai.
Dibuat: 2026-08-24

## 1. Tujuan

Memperluas "Pengaturan Section" yang sudah ada (Fase 1, Iterasi 1 — baru sebatas on/off) jadi panel **kustomisasi tampilan** yang jauh lebih kaya untuk halaman index, dikelola penuh dari admin: preset warna aksen, logo/branding, toggle animasi & efek, reorder section (drag & drop), jumlah item per section, toggle sub-elemen halaman, custom heading/subheading, mode maintenance, dan mode preview/draft sebelum publish.

Cakupan ini dipilih langsung oleh user dari daftar saran (lihat riwayat percakapan) — **dark mode SENGAJA TIDAK termasuk** (tidak dipilih).

## 2. Cara kerja dokumen ini — sama seperti Fase 3

- `LOG-ITERASI.md` (file yang sama, dipakai bersama seluruh fase) dicatat setiap iterasi selesai. `ERD.md` diupdate setiap ada perubahan skema.
- **TIDAK ADA `git commit` otomatis** di seluruh Fase 4 — perubahan dibiarkan uncommitted di working tree, user review & commit manual sendiri.
- **Iterasi TIDAK dirantai otomatis** — tiap iterasi berhenti menunggu instruksi lanjut, supaya working tree tidak menumpuk perubahan dari beberapa iterasi tanpa checkpoint commit yang jelas.

## 3. Keputusan arsitektur penting (dibaca dulu sebelum mulai — ini yang bikin Fase 4 beda dari Fase 1-3)

| Area | Keputusan & alasan |
|---|---|
| **Urutan iterasi vs mode preview/draft** | Mode preview/draft **SENGAJA dibangun di Iterasi 18 (fondasi), bukan di akhir**. Alasan: kalau dibangun terakhir, semua form admin yang sudah dibuat di Iterasi 19-22 (preset warna, logo, reorder, dst) harus DIROMBAK ULANG supaya menulis ke layer draft alih-alih langsung ke live — kerja dua kali. Dengan draft/publish sebagai fondasi sejak awal, setiap fitur baru otomatis "draft-aware" tanpa retrofit. |
| **Mekanisme draft/publish** | **Bukan** sistem revisi/histori penuh (seperti Git). Modelnya sederhana: satu snapshot "draft" (kolom `is_draft`/tabel draft) vs satu state "live/published" yang dibaca situs publik. Admin edit → tersimpan sebagai draft. Tombol **"Publish"** menyalin draft → live. Tombol **"Discard Draft"** membuang draft, kembali ke live. Tidak ada riwayat multi-versi/undo-ke-versi-lama — kalau butuh itu nanti, itu pengembangan terpisah jauh lebih besar (di luar scope). |
| **Cakupan mode preview/draft** | **HANYA** untuk pengaturan tampilan yang dibangun di Fase 4 ini (preset warna, logo, section order/toggle/count, sub-elemen, heading override, maintenance mode). **TIDAK** berlaku untuk CRUD konten Fase 1 (Projects/Blog/Experience/Testimonials/Skills tetap langsung live seperti sekarang, tanpa draft) — memperluas draft/publish ke seluruh konten adalah pekerjaan jauh lebih besar, di luar scope permintaan ini. |
| **Preview sebelum publish** | Admin yang sedang login bisa melihat draft di halaman publik lewat mode preview (mis. akses `/?preview=1` saat login sebagai admin, atau indikator "Melihat Draft" di admin dengan link buka preview di tab baru) — pengunjung biasa (tidak login) SELALU melihat versi live/published, tidak pernah draft. |
| **Preset warna aksen — cakupan realistis** | TIDAK mengganti semua warna di codebase. Hanya elemen yang merepresentasikan "brand accent": tombol CTA utama, pill nav aktif, badge section header, gradient judul Hero, accent card/border hover. Warna netral (slate untuk teks/background) TIDAK ikut berubah. Direalisasikan lewat CSS custom properties (`--accent-500/600/700` dst) yang di-swap sesuai preset terpilih, elemen terkait diubah dari `bg-indigo-600` (hardcoded) jadi `bg-[var(--accent-600)]` (Tailwind arbitrary value). |
| **Reorder section — dampak struktural** | `resources/views/portfolio/index.blade.php` saat ini me-`@include` tiap partial section dalam urutan TETAP di kode. Supaya admin bisa mengubah urutan, index harus di-refactor jadi loop dinamis atas daftar section aktif terurut (`sort_order`), dengan pemetaan `section_key → nama partial view`. Ini perubahan struktural nyata pada file inti, bukan sekadar tambah kolom. |
| **Reorder — mekanisme UI** | "Drag & drop" sungguhan (bukan tombol naik/turun seperti reorder di CRUD Fase 1) — pakai HTML5 Drag and Drop API native + Alpine.js untuk kirim urutan baru via fetch/AJAX. **Tidak menambah library JS baru** (tidak pakai SortableJS dkk) supaya tidak menambah bobot bundle — kalau ternyata drag-drop native kurang mulus/reliable saat implementasi, boleh fallback ke tombol naik/turun dan didokumentasikan alasannya di LOG. |
| **Jumlah item per section** | Kolom `display_count` (nullable int) di `section_settings`, HANYA relevan untuk section bertipe "list" (projects, blog, testimonials — mungkin skills). Section non-list (hero, about, playground, contact) kolom ini diabaikan/null. |
| **Custom heading/subheading** | Kolom nullable (`heading_id`, `heading_en`, `subheading_id`, `subheading_en`) di `section_settings`. **Nullable = opsional**: kalau admin tidak mengisi, Blade tetap fallback ke teks hardcoded bilingual yang sudah ada sekarang (tidak ada risiko "teks kosong" kalau admin belum sempat isi). |
| **Mode maintenance** | Middleware baru yang mengecek 1 setting (`maintenance_mode`), menampilkan halaman "Segera Hadir" untuk SEMUA request publik KECUALI: rute `/admin/*` (supaya admin tetap bisa login & matikan maintenance), dan pengunjung yang sedang login sebagai admin sekalipun mengakses halaman publik (supaya admin bisa cek tampilan publik tanpa logout dulu). |
| **Penyimpanan pengaturan generik** | Preset warna, toggle animasi/efek, logo, sub-elemen, maintenance — semuanya disimpan di SATU tabel key-value baru `display_settings` (`setting_key` unik, `value`, opsional `value_draft` untuk mekanisme draft) — bukan menambah banyak kolom nullable ke `site_profiles` yang sudah ada. Lebih scalable untuk menambah setting baru di masa depan tanpa migration berulang. |

## 4. Struktur menu admin baru

Menu **"Tampilan Halaman Index"** (nama definitif diputuskan saat implementasi, boleh gabung ke menu "Pengaturan Section" yang sudah ada di Fase 1 atau jadi menu terpisah — evaluasi mana yang lebih masuk akal secara UX saat Iterasi 18, dokumentasikan pilihannya) berisi sub-bagian:
1. **Tema & Branding** — preset warna aksen, logo/branding.
2. **Animasi & Efek** — toggle reveal-on-scroll, ambient blobs, scroll progress bar.
3. **Urutan & Isi Section** — reorder drag-drop, on/off (sudah ada dari Fase 1, dipindah ke sini), jumlah item, custom heading/subheading.
4. **Elemen Halaman** — toggle sub-elemen (CTA navbar, floating widget, social bar Hero).
5. **Mode Situs** — maintenance mode.
6. Indikator status **Draft / Live** yang terlihat di seluruh menu ini + tombol global **"Publish Perubahan"** dan **"Buang Draft"**.

## 5. Rincian iterasi (lanjutan penomoran — mulai Iterasi 18)

### Iterasi 18 — Fondasi: Draft/Publish + Skema Baru
- Tabel `display_settings` (setting_key unik, value/value_draft, timestamps).
- Tambah kolom di `section_settings`: `display_count`, `heading_id`, `heading_en`, `subheading_id`, `subheading_en`, dan (kalau mekanisme draft dipilih per-kolom bukan snapshot terpisah) kolom `*_draft` yang sepadan — putuskan pendekatan paling rapi saat implementasi (opsi A: kolom draft berdampingan per field; opsi B: satu tabel snapshot draft terpisah yang menyalin seluruh state relevan — pilih salah satu, dokumentasikan alasan).
- Middleware/helper untuk menentukan "apakah request ini melihat draft" (admin login + mode preview aktif → draft; selain itu → live).
- Mekanisme publish/discard (route + controller admin) yang generik, bisa dipakai semua setting Fase 4 ke depan.
- **Bukti konsep end-to-end**: implementasikan mekanisme ini untuk SATU setting sederhana dulu (`animations_enabled` — toggle reveal-on-scroll) supaya alur draft→preview→publish/discard benar-benar teruji sebelum dipakai fitur-fitur lain di iterasi berikutnya.
- Menu admin "Tampilan Halaman Index" shell dibuat (menu lain di dalamnya boleh placeholder "segera hadir per iterasi" mengikuti pola Fase 1 Iterasi 0).
- **Verifikasi**: ubah `animations_enabled` di admin → status jadi draft (belum berubah di publik) → buka preview sebagai admin → berubah → publish → berubah di publik untuk visitor biasa (uji tanpa cookie login) → coba lagi dengan discard draft → kembali ke nilai live semula.

### Iterasi 19 — Preset Warna Aksen & Logo/Branding
- CSS custom properties untuk accent color, 4 preset (Indigo/Emerald/Rose/Amber), terapkan ke elemen brand-accent yang sudah didaftar di bagian 3 (bukan exhaustive recolor).
- Upload logo gambar ATAU tetap logo teks (toggle `logo_type`), tersimpan di `display_settings`.
- Semua lewat alur draft (dari fondasi Iterasi 18) — form ini adalah pengguna pertama alur generik itu selain bukti-konsep di Iterasi 18.
- **Verifikasi**: ganti preset di draft, preview menampilkan warna baru di tombol CTA/badge/gradient Hero, publish membuatnya live, regresi visual singkat (baca kode, pastikan tidak ada elemen brand yang "lupa" ikut variable).

### Iterasi 20 — Reorder Section (Drag & Drop) & Jumlah Item per Section
- Refactor `portfolio/index.blade.php` jadi loop dinamis section aktif terurut `sort_order` + pemetaan `section_key → partial`.
- UI drag-drop di admin untuk urutan section, AJAX simpan ke draft.
- Input jumlah item per section list-type (projects/blog/testimonials), `PortfolioController@index` pakai nilai ini untuk `->take($count)` (fallback ke jumlah default sekarang kalau belum diisi).
- **Verifikasi**: ubah urutan di draft → preview menampilkan section dalam urutan baru → publish → live berubah. Ubah jumlah featured project ditampilkan → cocok di publik.

### Iterasi 21 — Toggle Sub-Elemen & Custom Heading/Subheading
- Toggle: CTA navbar (Hire Me/Download CV), floating widget kanan-bawah, social bar di Hero — tiap toggle langsung berefek sembunyikan/tampilkan elemen terkait di Blade.
- Form custom heading/subheading per section (yang punya heading), fallback ke teks hardcoded default kalau kosong (ID & EN terpisah).
- **Verifikasi**: matikan 1 sub-elemen di draft → hilang di preview, tetap ada di live sampai publish. Isi custom heading 1 section → tampil menggantikan default di preview.

### Iterasi 22 — Mode Maintenance
- Middleware maintenance untuk semua rute publik (kecuali `/admin/*` dan admin yang sedang login).
- Halaman "Segera Hadir" (bilingual ID/EN, style konsisten frosted-glass situs).
- Toggle di admin + pesan custom (opsional, dari `display_settings`).
- **Verifikasi**: aktifkan maintenance → visitor tanpa login lihat halaman maintenance di SEMUA rute publik (`/`, `/projects`, `/projects/{key}`) → admin yang login tetap lihat situs normal → `/admin/*` tetap bisa diakses untuk mematikan lagi → matikan → visitor normal kembali.

### Iterasi 23 — Audit & QA Penutup Fase 4
- Regresi penuh publik + admin (pola sama seperti Iterasi 9/17): semua fitur 18-22 dites ulang bersamaan (bukan satu-satu terpisah) untuk pastikan tidak saling konflik.
- Uji draft/publish/discard end-to-end untuk SEMUA setting Fase 4 sekaligus dalam satu sesi draft (ubah banyak hal, preview, publish sekali, cek semua berubah bersamaan).
- Pastikan `section_settings`/`display_settings` yang draft-nya belum di-publish TIDAK bocor ke visitor biasa di skenario apa pun.
- Ringkasan akhir Fase 4 di `LOG-ITERASI.md`, update status dokumen ini jadi TUNTAS.
- Tidak ada perubahan kode baru kecuali menemukan regresi nyata dari iterasi sebelumnya.

## 6. Yang TIDAK termasuk Fase 4

- Dark mode (tidak dipilih user).
- Draft/publish untuk konten CRUD Fase 1 (Projects/Blog/Experience/Testimonials/Skills) — tetap langsung live seperti sekarang.
- Riwayat/versioning multi-level (hanya draft tunggal vs live, bukan histori banyak versi dengan rollback ke versi manapun).
- Preset warna custom bebas (color picker RGB/hex bebas) — hanya 4 preset kurasi, demi konsistensi visual & membatasi risiko kombinasi warna yang jelek.
- Custom font upload/pilihan font bebas — di luar permintaan awal (hanya warna, animasi, logo yang diminta untuk tema visual).
