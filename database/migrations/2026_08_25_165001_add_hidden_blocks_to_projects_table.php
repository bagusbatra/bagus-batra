<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Iterasi 28 (Fase 5): `hidden_blocks` (JSON, nullable, array of block-key
     * string) — admin bisa memaksa sembunyikan blok konten OPSIONAL tertentu
     * di halaman detail project publik (`/projects/{key}`) meski datanya
     * terisi (lihat docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian 3 baris
     * "Projects — hide/unhide per blok"). NULL/kosong = semua blok tampil
     * (default, 100% backward compatible dengan project existing — sama
     * filosofi `draft_overrides`/`display_count` nullable di Fase 4).
     *
     * Direct-live (BUKAN lewat mekanisme draft/publish Fase 4) — konsisten
     * dgn seluruh CRUD Projects sejak Fase 1, Fase 4 bagian 6 eksplisit
     * membatasi draft/publish HANYA utk pengaturan tampilan situs, bukan
     * konten CRUD per-project.
     *
     * 7 block-key valid (divalidasi di Admin\ProjectController, BUKAN di
     * skema — kolom ini generik array string, sama pola `tags`): metrics,
     * highlights, tech_frontend, tech_backend, tech_database, tech_cloud,
     * tab_preview.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('hidden_blocks')->nullable()->after('accent_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('hidden_blocks');
        });
    }
};
