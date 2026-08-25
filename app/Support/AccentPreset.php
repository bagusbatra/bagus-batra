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

    /**
     * Iterasi 25 (Fase 5) — key preset ke-5, BUKAN entry di PRESETS di
     * bawah (PRESETS murni 4 preset kurasi statis dgn hex tetap). "custom"
     * artinya admin memilih 1 hex bebas lewat color picker
     * (`display_settings.accent_custom_hex`) — 5 step lain dihitung
     * on-the-fly dari hex itu lewat fromHex(), BUKAN dari tabel statis.
     * Lihat resolve() & fromHex() di bawah, dan
     * docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian 3 baris "Warna aksen
     * custom bebas".
     */
    public const CUSTOM_KEY = 'custom';

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
     * Iterasi 25: termasuk CUSTOM_KEY — "custom" adalah pilihan preset yang
     * SAH (bukan salah satu dari 4 entry PRESETS), divalidasi di sini juga
     * supaya `Rule::in(AccentPreset::keys())` di
     * Admin\AppearanceController@updateBranding meloloskannya.
     */
    public static function keys(): array
    {
        return [...array_keys(self::PRESETS), self::CUSTOM_KEY];
    }

    /**
     * Iterasi 25 (Fase 5) — resolusi TUNGGAL dipakai semua tempat yang
     * butuh render warna aksen aktif (middleware, layout, halaman
     * maintenance): kalau preset yang dipilih adalah "custom", hitung skala
     * dari hex tersimpan lewat fromHex(); selain itu (4 preset kurasi atau
     * key tak dikenal) tetap lewat get() seperti sebelum Iterasi 25.
     * $customHex boleh null (mis. admin pilih "custom" tapi belum sempat
     * simpan hex apa pun) — fallback ke hex indigo-600 supaya tidak pernah
     * gagal render.
     */
    public static function resolve(string $presetKey, ?string $customHex): array
    {
        if ($presetKey === self::CUSTOM_KEY) {
            return self::fromHex($customHex ?: self::PRESETS[self::DEFAULT]['600']);
        }

        return self::get($presetKey);
    }

    /**
     * Hitung skala 6-step (50/100/300/500/600/700) dari 1 hex bebas.
     * $hex diperlakukan sbg step "600" (step paling sering dipakai sbg
     * warna solid utama di seluruh elemen brand-accent — CTA, pill nav,
     * dst — lihat AccentPreset dipakai di mana). 5 step lain dihitung lewat
     * pergeseran LIGHTNESS di ruang HSL (hue & saturation dipertahankan
     * persis), BUKAN interpolasi RGB linear (interpolasi RGB akan
     * menghasilkan warna yang terlihat "kotor"/desaturasi di step terang
     * krn RGB bukan ruang warna yang perceptually uniform).
     *
     * Step lain dihitung sbg PROPORSI jarak tersisa menuju putih (utk step
     * lebih terang dari 600) atau menuju hitam (utk step 700, lebih gelap),
     * BUKAN offset lightness tetap (poin persentase tetap terbukti gagal
     * generalisasi — dicoba dulu saat implementasi & disimpan sbg pelajaran
     * di sini: base lightness indigo-600 kebetulan sudah tinggi ~59%, jadi
     * offset tetap "masuk akal" utk indigo, tapi hex gelap-jenuh mis.
     * emerald-600 (L~30%) dgn offset tetap yg sama hanya nyampe L~68% —
     * jauh dari putih pucat yg diharapkan, hasilnya warna neon terlalu
     * jenuh, bukan tint pucat). Fraksi di bawah dihitung dgn reverse-
     * engineer HSL 4 preset kurasi (rata-rata fraksi jarak tempuh tiap step
     * relatif thd 600 di keempatnya, konsisten ±0.05 lintas hue berbeda —
     * jauh lebih stabil drpd delta L tetap). Saturation & hue DIPERTAHANKAN
     * persis di semua step (tidak ikut fraksi) — cukup utk hasil yg
     * layak pakai, tidak sekuat kurasi manual tp jauh lebih baik drpd
     * delta tetap.
     */
    public static function fromHex(string $hex): array
    {
        // Fraksi jarak menuju putih (L=100) utk step > 600, atau menuju
        // hitam (L=0) utk step 700 (satu-satunya step < 600).
        $fractions = [
            '50' => 0.93,
            '100' => 0.85,
            '300' => 0.52,
            '500' => 0.16,
            '600' => 0.0,
            '700' => -0.17,
        ];

        [$h, $s, $l] = self::hexToHsl($hex);

        $scale = [];
        foreach ($fractions as $step => $fraction) {
            if ($step === '600') {
                $scale[$step] = strtolower($hex);

                continue;
            }

            $stepLightness = $fraction >= 0
                ? $l + (100 - $l) * $fraction   // menuju putih
                : $l * (1 + $fraction);         // menuju hitam ($fraction negatif)

            $scale[$step] = self::hslToHex($h, $s, max(0, min(100, $stepLightness)));
        }

        $scale['label'] = 'Custom';
        $scale['swatch'] = $scale['600'];

        return $scale;
    }

    /**
     * @return array{0: float, 1: float, 2: float} [hue 0-360, saturation 0-100, lightness 0-100]
     */
    private static function hexToHsl(string $hex): array
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            return [0.0, 0.0, round($l * 100, 2)];
        }

        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        $h = match ($max) {
            $r => (($g - $b) / $d) + ($g < $b ? 6 : 0),
            $g => (($b - $r) / $d) + 2,
            default => (($r - $g) / $d) + 4,
        };
        $h *= 60;

        return [round($h, 2), round($s * 100, 2), round($l * 100, 2)];
    }

    private static function hslToHex(float $h, float $s, float $l): string
    {
        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        [$r, $g, $b] = match (true) {
            $h < 60 => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        $toHex = fn (float $v) => str_pad(dechex((int) round(($v + $m) * 255)), 2, '0', STR_PAD_LEFT);

        return '#'.$toHex($r).$toHex($g).$toHex($b);
    }
}
