<?php

namespace App\Support;

/**
 * Iterasi 26 (Fase 5) — beberapa gaya reveal-on-scroll, admin pilih 1 gaya
 * aktif. Struktur SENGAJA meniru App\Support\AccentPreset (DEFAULT/STYLES
 * const + keys()) — pola yang sudah terbukti jalan di Fase 4/5, bukan pola
 * baru. Lihat docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian 3 baris "Gaya
 * reveal-on-scroll" & bagian 5 Iterasi 26.
 *
 * Realisasi visual 100% CSS (resources/css/app.css, keyed lewat
 * `body[data-reveal-style="..."]`) — kelas ini HANYA menyimpan metadata
 * (key valid + label utk UI admin), TIDAK ada logic transform/animasi di
 * PHP sama sekali (beda dari AccentPreset yang harus menghitung hex).
 */
class AnimationStyle
{
    public const DEFAULT = 'fade-up';

    public const STYLES = [
        'fade-up' => [
            'label' => 'Fade Up',
            'description' => 'Muncul dari bawah sambil memudar (default sejak Iterasi 18).',
        ],
        'fade-in' => [
            'label' => 'Fade In',
            'description' => 'Memudar di tempat, tanpa pergeseran posisi.',
        ],
        'zoom-in' => [
            'label' => 'Zoom In',
            'description' => 'Membesar dari 92% ke ukuran penuh sambil memudar.',
        ],
        'slide-left' => [
            'label' => 'Geser dari Kanan',
            'description' => 'Masuk dari kanan bergeser ke kiri sambil memudar.',
        ],
        'slide-right' => [
            'label' => 'Geser dari Kiri',
            'description' => 'Masuk dari kiri bergeser ke kanan sambil memudar.',
        ],
        'flip-up' => [
            'label' => 'Flip Up',
            'description' => 'Miring 3D dari bawah lalu tegak sambil memudar.',
        ],
    ];

    /**
     * Semua key gaya yang valid, dipakai untuk validasi form admin.
     */
    public static function keys(): array
    {
        return array_keys(self::STYLES);
    }
}
