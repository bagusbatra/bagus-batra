<?php

namespace App\Support;

/**
 * 4 preset warna aksen kurasi — Iterasi 19 (Fase 4). Lihat
 * docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 3 ("Preset warna aksen —
 * cakupan realistis") & bagian 5 Iterasi 19.
 *
 * Nilai hex tiap step (50/100/300/500/600/700) disamakan PERSIS dengan
 * skala warna resmi Tailwind (indigo/emerald/rose/amber) supaya kontras &
 * aksesibilitas tetap terjaga — bukan warna custom bebas (color picker
 * RGB/hex bebas sengaja TIDAK termasuk Fase 4, lihat bagian 6 rencana).
 *
 * Dipakai oleh:
 * - App\Http\Middleware\HandleAppearancePreview (resolve preset AKTIF per
 *   request, preview-aware) & resources/views/layouts/app.blade.php
 *   (render sebagai inline <style> di <head>, server-rendered — bukan JS
 *   runtime, supaya tidak ada flash-of-wrong-color saat load).
 * - App\Http\Controllers\Admin\AppearanceController (daftar pilihan +
 *   swatch di tab "Tema & Branding").
 */
class AccentPreset
{
    public const DEFAULT = 'indigo';

    public const PRESETS = [
        'indigo' => [
            'label' => 'Indigo',
            'swatch' => '#4f46e5',
            '50' => '#eef2ff',
            '100' => '#e0e7ff',
            '300' => '#a5b4fc',
            '500' => '#6366f1',
            '600' => '#4f46e5',
            '700' => '#4338ca',
        ],
        'emerald' => [
            'label' => 'Emerald',
            'swatch' => '#059669',
            '50' => '#ecfdf5',
            '100' => '#d1fae5',
            '300' => '#6ee7b7',
            '500' => '#10b981',
            '600' => '#059669',
            '700' => '#047857',
        ],
        'rose' => [
            'label' => 'Rose',
            'swatch' => '#e11d48',
            '50' => '#fff1f2',
            '100' => '#ffe4e6',
            '300' => '#fda4af',
            '500' => '#f43f5e',
            '600' => '#e11d48',
            '700' => '#be123c',
        ],
        'amber' => [
            'label' => 'Amber',
            'swatch' => '#d97706',
            '50' => '#fffbeb',
            '100' => '#fef3c7',
            '300' => '#fcd34d',
            '500' => '#f59e0b',
            '600' => '#d97706',
            '700' => '#b45309',
        ],
    ];

    /**
     * Ambil array nilai satu preset. Fallback ke DEFAULT (indigo) kalau
     * key tidak dikenal (mis. data lama/rusak di display_settings) — supaya
     * layout tidak pernah gagal render karena preset tak valid.
     */
    public static function get(string $key): array
    {
        return self::PRESETS[$key] ?? self::PRESETS[self::DEFAULT];
    }

    /**
     * Semua key preset yang valid, dipakai untuk validasi form admin.
     */
    public static function keys(): array
    {
        return array_keys(self::PRESETS);
    }
}
