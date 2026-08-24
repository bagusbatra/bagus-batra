<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Iterasi 18 (Fase 4): tabel key-value generik untuk pengaturan tampilan
     * halaman index (preset warna, logo, toggle animasi/efek, sub-elemen,
     * maintenance mode, dst — lihat docs/RENCANA-KUSTOMISASI-TAMPILAN.md
     * bagian 3 "Penyimpanan pengaturan generik"). Satu tabel key-value
     * dipilih ketimbang menambah kolom nullable ke site_profiles supaya
     * setting baru di iterasi-iterasi berikutnya tidak perlu migration baru.
     *
     * `value` = nilai LIVE yang dibaca visitor publik biasa. `value_draft`
     * = nilai draft pending (NULL berarti "tidak ada perubahan pending",
     * fallback ke `value`). Admin login + mode preview aktif melihat
     * value_draft ?? value; visitor biasa SELALU melihat value saja
     * (lihat App\Models\DisplaySetting::get()).
     */
    public function up(): void
    {
        Schema::create('display_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique();
            $table->text('value')->nullable();
            $table->text('value_draft')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_settings');
    }
};
