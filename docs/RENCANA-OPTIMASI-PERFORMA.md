# Rencana Pengembangan — Optimalisasi Performa (Fase 3)

Status: **Draft — menunggu review sebelum Iterasi 13 dieksekusi. TIDAK ADA COMMIT OTOMATIS di seluruh Fase 3 (lihat bagian 2).**
Dibuat: 2026-08-23

## 1. Tujuan

Membuat setiap halaman, section, dan menu (publik maupun admin) di `D:\PROJEK\bagus-batra` lebih ringan dan lebih cepat dimuat — **tanpa mengubah tampilan/UX yang sudah dibangun di Fase 1 & 2**. Ini murni optimasi cara aset & data dikirim ke browser, bukan redesign.

## 2. Cara kerja dokumen ini — PENTING, beda dari Fase 1/2

- Sama seperti `RENCANA-PENGEMBANGAN.md`: dokumen ini rencana hidup, `LOG-ITERASI.md` (file yang sama, dipakai bersama seluruh fase) dicatat setiap iterasi selesai, `ERD.md` diupdate hanya bila ada perubahan skema (index baru dianggap perubahan skema kecil, akan dicatat).
- **PERBEDAAN UTAMA yang diminta eksplisit**: **tidak ada `git commit` otomatis** di Fase 3. Setiap iterasi berhenti setelah perubahan selesai & terverifikasi lokal (`php artisan serve` + curl / build check), TAPI perubahan dibiarkan **uncommitted** di working tree. User yang me-review diff dan commit sendiri kapan pun siap.
- Konsekuensi: **iterasi TIDAK dirantai otomatis** seperti Fase 1/2 (yang saling chaining sampai selesai semua). Untuk Fase 3, tiap iterasi berhenti menunggu instruksi lanjut setelah selesai — supaya working tree tidak menumpuk banyak perubahan dari beberapa iterasi berbeda tanpa checkpoint commit yang jelas. Kalau Anda ingin tetap dirantai otomatis meski tanpa commit (working tree akan menumpuk sampai akhir, baru direview & commit sekaligus), sampaikan saat instruksi "lanjut" — defaultnya berhenti per-iterasi.
- Sebelum iterasi berikutnya mulai, disarankan (tidak wajib) Anda sudah commit perubahan iterasi sebelumnya, supaya kalau perlu rollback, granularitasnya per-iterasi.

## 3. Baseline — temuan audit saat ini (diukur langsung dari kode & build, bukan asumsi)

| Area | Temuan |
|---|---|
| Bundle aset | Satu entry point Vite untuk SEMUA halaman: `resources/css/app.css` (133KB dibangun / **~20KB gzip**) + `resources/js/app.js` (61KB dibangun / **~21KB gzip**) — dimuat identik di halaman publik (index, /projects, /projects/{key}) MAUPUN seluruh halaman admin (termasuk halaman login sebelum autentikasi). |
| Bundle tidak dipecah per konteks | `app.js` = Alpine core + `reveal.js` + `portfolio.js` (427 baris: lang store, scroll-spy navbar, floating widget, 3 demo Interactive Playground termasuk simulasi spring/theme/optimistic-UI) + `admin.js` (helper CRUD: toggle section, delete-confirm, reorder). Halaman admin memuat seluruh logic publik (termasuk demo Playground yang tidak pernah dipakai di admin) dan sebaliknya. |
| Query database per load index | `PortfolioController@index` menjalankan **8 query terpisah tanpa cache** (`Skill`, `Project` all-rows, `BlogPost`, `Experience`, `Testimonial`, `SiteProfile::current()`, `SocialLink`, `SectionSetting`) di SETIAP request — padahal konten ini nyaris statis (hanya berubah saat admin edit). |
| Query featured project tidak efisien | `Project::orderBy('sort_order')->get()` mengambil **SEMUA baris** lalu difilter `->where('featured', true)` di PHP (bukan di level SQL). Tidak masalah di 5 baris sekarang, tapi pola ini salah secara desain — akan makin boros seiring data project bertambah lewat admin. |
| Gambar tanpa optimasi loading | **Nol** dari seluruh `<img>` di codebase (publik maupun admin) memakai `loading="lazy"`, `decoding="async"`, atau `width`/`height` eksplisit — termasuk grid `/projects` (banyak gambar sekaligus), related-project di halaman detail, avatar testimonial, cover blog. Semua gambar dimuat eager tanpa ruang layout dicadangkan (risiko LCP lambat & layout shift). |
| Gambar tanpa responsive sizing | Gambar Unsplash sudah pakai `?w=...&auto=format&q=80` (bagus, otomatis format modern) tapi **satu ukuran fixed** untuk semua breakpoint — HP mengunduh gambar seukuran desktop. |
| Google Fonts | Satu `<link>` render-blocking menarik 3 keluarga font dengan total ±18 kombinasi weight/style (`Fira Code` 3, `Outfit` 5, `Plus Jakarta Sans` 10 termasuk italic) — belum diverifikasi berapa yang benar-benar dipakai di CSS. |
| Optimasi produksi Laravel | Tidak ada langkah `config:cache` / `route:cache` / `view:cache` yang didokumentasikan di README sebagai bagian deploy — saat ini hanya dijalankan `php artisan serve` mode development. |
| HTTP caching | Tidak ada response caching untuk halaman publik yang isinya jarang berubah (index, /projects, /projects/{key}) — tiap request selalu render ulang dari 0 + 8 query di atas. |

## 4. Prinsip & batasan optimasi

- **Tidak boleh mengubah tampilan visual, teks, atau interaksi** yang sudah ada — murni cara aset/data dikirim & dirender, tetap 1:1 secara kasat mata dengan hasil Fase 1 & 2.
- Setiap iterasi WAJIB mengukur **sebelum vs sesudah** (ukuran bundle gzip, jumlah query per halaman, waktu respons kasar via curl) supaya klaim "lebih cepat" bukan asumsi.
- Tidak menambah dependency besar baru kalau bisa dihindari (mis. tidak perlu image-processing library berat untuk sekadar menambah `loading="lazy"`).
- Cache apa pun yang ditambahkan (query cache, response cache) **wajib auto-invalidate** saat data terkait diubah lewat admin — tidak boleh membuat admin mengedit lalu publik menampilkan data basi.

## 5. Rincian iterasi (lanjutan penomoran dari Fase 1/2 — mulai Iterasi 13)

### Iterasi 13 — Pemisahan Bundle Publik vs Admin
- Pecah `resources/js/app.js` jadi entry terpisah: `public.js` (Alpine core + reveal + portfolio.js) dan `admin.js` (Alpine core + reveal + admin.js) — hindari duplikasi Alpine core kalau bisa (evaluasi shared chunk lewat Vite build config, atau cukup terima duplikasi kecil kalau usaha splitting-nya tidak sepadan, dokumentasikan trade-off-nya).
- Sama untuk CSS bila memungkinkan dipisah tanpa merusak style yang dipakai bersama (banyak utility Tailwind dipakai di kedua sisi, jadi CSS mungkin tetap satu bundle — keputusan diambil saat implementasi, dicatat alasannya).
- Update `vite.config.js` (multiple entry points) dan tiap `@vite([...])` call di `resources/views/layouts/app.blade.php` vs `resources/views/admin/layouts/app.blade.php` + `admin/auth/login.blade.php`.
- **Ukur**: `npm run build`, bandingkan ukuran gzip sebelum/sesudah untuk masing-masing bundle publik & admin secara terpisah, catat di LOG.
- **Verifikasi**: publik & admin tetap berfungsi identik (regresi manual singkat: buka index, /projects, /projects/{key}, login admin, dashboard, 1 halaman CRUD) — tidak ada JS/CSS hilang yang bikin fitur rusak.

### Iterasi 14 — Optimasi Pemuatan Gambar
- Tambahkan `loading="lazy"` + `decoding="async"` pada SEMUA `<img>` yang berada di bawah viewport awal (grid projects/blog, testimonial, related projects, avatar di dalam modal artikel) — kecuali gambar above-the-fold (avatar hero, gambar banner pertama di halaman detail project) yang sebaiknya tetap eager (`loading="eager"` eksplisit) atau malah diberi `fetchpriority="high"` supaya LCP tidak melambat.
- Tambahkan `width`/`height` eksplisit (atau `aspect-ratio` via class Tailwind) di setiap `<img>` supaya browser bisa mencadangkan ruang layout sebelum gambar selesai dimuat (kurangi CLS).
- Evaluasi menambah parameter `w=` yang lebih sesuai breakpoint kartu (mis. tidak perlu `w=1000` untuk thumbnail kecil 56×56px di list admin) — sesuaikan tanpa menurunkan kualitas visual yang terlihat.
- **Ukur**: hitung total ukuran gambar yang di-request untuk 1x load index & 1x load /projects (lewat header `Content-Length` per gambar via curl atau devtools kalau tersedia), bandingkan sebelum/sesudah untuk skenario "viewport belum discroll" (berapa banyak gambar yang tadinya ikut ke-download di awal, sekarang ditunda).

### Iterasi 15 — Query & Cache Layer
- Cache `SiteProfile::current()`, `SocialLink` aktif, dan `SectionSetting` (data yang paling jarang berubah & paling sering dibaca di setiap request) pakai `Cache::remember` dengan key jelas — **invalidasi otomatis**: setiap controller admin yang menyimpan perubahan ke tabel-tabel ini memanggil `Cache::forget`/`Cache::tags(...)->flush()` yang sesuai (cek `Admin\ProfileController`, `Admin\SocialLinkController`, `Admin\SectionSettingController`).
- Perbaiki query featured project: filter `featured=true` di level SQL (`Project::where('featured', true)->orderBy('sort_order')->get()`), baru fallback query terpisah `Project::orderBy('sort_order')->take(3)->get()` HANYA kalau hasil pertama kosong (bukan lagi fetch-all-lalu-filter-PHP).
- Evaluasi (opsional, putuskan saat implementasi apakah worth it): cache penuh output halaman index (`Cache::remember` pada level response/view) dengan invalidasi saat *manapun* dari 8 sumber data admin berubah — kalau kompleksitas invalidasinya terlalu tinggi dibanding manfaatnya di skala data sekarang, boleh dilewati dan cukup cache per-model seperti di atas, catat alasannya di LOG.
- **Ukur**: jumlah query SQL per request `GET /` sebelum vs sesudah (pakai `DB::listen()` sementara di route debug, atau `\Illuminate\Support\Facades\DB::enableQueryLog()` + dump count — cara paling ringan tanpa nambah package baru), waktu respons kasar (curl `-w "%{time_total}"`) sebelum/sesudah dengan cache dingin vs cache panas.

### Iterasi 16 — Kesiapan Produksi & HTTP Delivery
- Tambahkan bagian baru di `README.md`: langkah deploy produksi (`php artisan config:cache`, `route:cache`, `view:cache`, `event:cache`, pastikan `APP_DEBUG=false` & `APP_ENV=production` di `.env` produksi, `npm run build` bukan `npm run dev`).
- Cek header cache untuk aset Vite (`public/build/assets/*` sudah punya hash di nama file → aman di-cache `immutable` jangka panjang; pastikan tidak ada konfigurasi server yang menimpa ini kalau memakai Laragon/Apache/Nginx — dokumentasikan rekomendasi header, tidak perlu mengubah konfigurasi Laragon langsung kecuali diminta).
- Reminder OPcache untuk PHP di lingkungan produksi (dokumentasi saja, bukan perubahan kode — OPcache adalah konfigurasi `php.ini`, di luar kendali kode aplikasi).
- **Ukur**: bandingkan waktu respons `GET /` sebelum vs sesudah `config:cache`+`route:cache`+`view:cache` aktif (curl timing, beberapa kali request ambil rata-rata).

### Iterasi 17 — Audit Ulang & Ringkasan Hasil
- Jalankan ulang seluruh pengukuran dari Iterasi 13-16 dalam satu sesi (setelah semua perubahan sebelumnya sudah di-commit oleh Anda), bandingkan terhadap baseline di bagian 3 dokumen ini.
- Tulis ringkasan hasil akhir (before/after) di `LOG-ITERASI.md` sebagai entri penutup Fase 3.
- Regresi penuh sekali lagi (publik + admin) untuk memastikan tidak ada yang rusak sepanjang 4 iterasi optimasi.
- **Tidak ada perubahan kode baru di iterasi ini** kecuali ditemukan regresi yang perlu diperbaiki dari iterasi sebelumnya.

## 6. Yang TIDAK termasuk Fase 3

- Migrasi ke CDN/image-hosting sendiri (masih pakai URL Unsplash langsung, hanya cara memuatnya yang dioptimasi).
- Server-side rendering cache tingkat lanjut (Redis, Varnish, dll.) — di luar scope kecuali diminta terpisah, saat ini cukup `Cache` driver default Laravel (file/database, sesuai `.env` yang berjalan).
- Perubahan visual/redesign apa pun (murni performa, bukan tampilan).
- Automated performance testing/CI (Lighthouse CI dll.) — pengukuran di rencana ini manual per iterasi, bukan otomatis di pipeline.
