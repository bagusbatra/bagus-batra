<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Data cleanup — Iterasi 19 (Fase 4), Bagian A. TIDAK mengubah struktur
 * tabel `section_settings` (kolom tetap sama) — hanya menghapus 1 baris
 * yatim `section_key = 'playground'`.
 *
 * Konteks (lihat docs/LOG-ITERASI.md Iterasi 18 & 19): section Playground
 * dihapus total dari kode publik oleh user di commit `d1d2774` (di luar
 * Fase 4), tapi baris `section_settings`-nya sendiri tidak ikut dihapus
 * saat itu — jadi baris ini jadi "yatim" (ada di DB, tidak digate oleh
 * `@if`/`@include` manapun di resources/views/portfolio/index.blade.php).
 * Dibersihkan di sini supaya Iterasi 20 (reorder section) tidak salah
 * asumsi jumlah section yang benar-benar aktif dari `section_settings`.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('section_settings')->where('section_key', 'playground')->delete();

        // Rapikan sort_order supaya tidak ada "lubang" bekas playground
        // (sebelumnya 0,1,2,3,4=playground,5,6,7,8 -> sekarang 0-7 rapat),
        // konsisten dengan urutan baru di SectionSettingSeeder. Ini murni
        // kosmetik (nomor urut yang ditampilkan di admin), tidak mengubah
        // urutan render publik (masih hardcode di index.blade.php sampai
        // Iterasi 20).
        $order = ['hero' => 0, 'about' => 1, 'skills' => 2, 'projects' => 3, 'experience' => 4, 'blog' => 5, 'testimonials' => 6, 'contact' => 7];
        foreach ($order as $key => $sortOrder) {
            DB::table('section_settings')->where('section_key', $key)->update(['sort_order' => $sortOrder]);
        }
    }

    /**
     * down() sengaja NO-OP (bukan insert ulang baris) — baris `playground`
     * murni sampah peninggalan fitur yang sudah dihapus permanen dari kode,
     * bukan data yang perlu dipulihkan kalau migration ini di-rollback.
     * Insert ulang baris dummy untuk section yang sudah tidak ada
     * partial/route-nya lagi justru akan menciptakan ulang masalah "baris
     * yatim" yang sedang diperbaiki migration ini.
     */
    public function down(): void
    {
        // No-op — lihat catatan di atas.
    }
};
