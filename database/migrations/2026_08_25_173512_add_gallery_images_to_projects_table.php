<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Iterasi 29 (Fase 5): `gallery_images` (JSON, nullable, array of URL
     * string) — galeri multi-gambar opsional per project, ditampilkan di
     * tab BARU "Galeri" pada halaman detail publik (`/projects/{key}`),
     * hanya muncul kalau array ini tidak kosong (lihat
     * docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian 3 baris "Projects — galeri
     * multi-gambar"). Pola JSON array sederhana — SAMA persis `tags` (bukan
     * tabel relasi `project_images` dgn FK), konsisten dgn preferensi
     * arsitektur project ini utk data array kecil (sudah dipakai
     * `tags`/`metrics`/`highlights`/`tech_stack` sejak Iterasi 4 Fase 1) —
     * jumlah gambar per project realistisnya kecil, tidak butuh query
     * relasional. NULL/kosong = tidak ada galeri (default, 100% backward
     * compatible). Direct-live (BUKAN draft/publish), sama seperti
     * `hidden_blocks` Iterasi 28 — konsisten seluruh CRUD Projects Fase 1.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('hidden_blocks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('gallery_images');
        });
    }
};
