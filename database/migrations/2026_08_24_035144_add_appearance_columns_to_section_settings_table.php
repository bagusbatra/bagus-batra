<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Iterasi 18 (Fase 4): kolom baru untuk kustomisasi tampilan per section
     * — semua nullable/opsional (lihat docs/RENCANA-KUSTOMISASI-TAMPILAN.md
     * bagian 3 & 5 Iterasi 18):
     * - `display_count`: jumlah item ditampilkan, hanya relevan utk section
     *   bertipe list (projects/blog/testimonials) — dipakai Iterasi 20.
     * - `heading_id`/`heading_en`/`subheading_id`/`subheading_en`: override
     *   custom heading, fallback ke teks hardcoded Blade kalau NULL —
     *   dipakai Iterasi 21.
     * - `draft_overrides` (JSON, nullable): object partial berisi field yg
     *   punya perubahan draft pending utk baris section ini, mis.
     *   {"is_active": false, "sort_order": 3} — HANYA key yg berubah, tidak
     *   perlu duplikasi kolom `*_draft` terpisah utk tiap kolom di atas.
     *   Publish = merge key dari draft_overrides ke kolom aslinya lalu
     *   NULL-kan draft_overrides. Discard = NULL-kan draft_overrides saja.
     *   Dipilih ketimbang kolom `*_draft` berdampingan per field supaya
     *   tidak perlu 5+ migration/kolom tambahan tiap kali field baru
     *   ditambah — cukup tambah key baru di JSON, skema tabel tidak
     *   berubah lagi utk field draft-able berikutnya.
     */
    public function up(): void
    {
        Schema::table('section_settings', function (Blueprint $table) {
            $table->unsignedInteger('display_count')->nullable()->after('sort_order');
            $table->string('heading_id')->nullable()->after('display_count');
            $table->string('heading_en')->nullable()->after('heading_id');
            $table->text('subheading_id')->nullable()->after('heading_en');
            $table->text('subheading_en')->nullable()->after('subheading_id');
            $table->json('draft_overrides')->nullable()->after('subheading_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_settings', function (Blueprint $table) {
            $table->dropColumn([
                'display_count',
                'heading_id',
                'heading_en',
                'subheading_id',
                'subheading_en',
                'draft_overrides',
            ]);
        });
    }
};
