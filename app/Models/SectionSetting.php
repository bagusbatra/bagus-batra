<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionSetting extends Model
{
    protected $fillable = [
        'section_key',
        'label',
        'is_active',
        'sort_order',
        'display_count',
        'heading_id',
        'heading_en',
        'subheading_id',
        'subheading_en',
        'draft_overrides',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        // Iterasi 18 (Fase 4): object partial berisi field-field yang punya
        // perubahan draft pending untuk baris ini, mis.
        // {"is_active": false, "sort_order": 3} — lihat migrasi
        // 2026_08_24_035144_add_appearance_columns_to_section_settings_table
        // untuk alasan kenapa 1 kolom JSON dipilih ketimbang kolom *_draft
        // terpisah per field.
        'draft_overrides' => 'array',
    ];

    /**
     * Nilai efektif satu field mempertimbangkan mode preview.
     *
     * $preview = true (admin login + mode preview aktif): kembalikan
     * override draft kalau field itu ADA di draft_overrides, selain itu
     * fallback ke nilai kolom aslinya.
     *
     * $preview = false (default, visitor publik biasa): SELALU kembalikan
     * nilai kolom asli — draft_overrides tidak pernah dibaca lewat jalur
     * ini, jadi draft tidak pernah bocor ke publik.
     */
    public function effective(string $field, bool $preview = false): mixed
    {
        if ($preview && is_array($this->draft_overrides) && array_key_exists($field, $this->draft_overrides)) {
            return $this->draft_overrides[$field];
        }

        return $this->{$field};
    }

    /**
     * Publish draft_overrides baris ini: merge ke kolom asli, lalu NULL-kan
     * draft_overrides. Dipanggil oleh Admin\AppearanceController@publish.
     */
    public function publishDraftOverrides(): void
    {
        if (! is_array($this->draft_overrides)) {
            return;
        }

        $this->fill($this->draft_overrides);
        $this->draft_overrides = null;
        $this->save();
    }

    /**
     * Buang draft_overrides baris ini tanpa menerapkannya — kolom asli
     * TIDAK berubah. Dipanggil oleh Admin\AppearanceController@discardDraft.
     */
    public function discardDraftOverrides(): void
    {
        $this->draft_overrides = null;
        $this->save();
    }
}
