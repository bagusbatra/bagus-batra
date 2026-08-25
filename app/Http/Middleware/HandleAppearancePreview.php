<?php

namespace App\Http\Middleware;

use App\Models\DisplaySetting;
use App\Models\SectionSetting;
use App\Support\AccentPreset;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Iterasi 18 (Fase 4) — menentukan "apakah request ini melihat draft" dan
 * membagikannya ke SEMUA view lewat view()->share(), supaya berlaku
 * site-wide (logo/warna aksen/dst dipakai di layouts/app.blade.php yang
 * shared oleh semua halaman publik), bukan cuma halaman index. Lihat
 * docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 3 baris "Preview sebelum
 * publish" & Iterasi 18.
 *
 * Didaftarkan di grup middleware "web" (bootstrap/app.php) — berlaku utk
 * SEMUA route (publik & admin) karena keduanya hanya pakai routing "web".
 *
 * Mekanisme penanda: query string `?preview=1` (butuh admin login, guard
 * "web") menyalakan flag di SESSION (`appearance_preview`) supaya tidak
 * perlu ditempel di setiap link — klik link internal apa pun tetap dalam
 * mode preview selama session aktif. `?preview=0` mematikannya lagi
 * (dipakai tombol "Keluar dari Mode Preview").
 */
class HandleAppearancePreview
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->query('preview') === '0') {
            $request->session()->forget('appearance_preview');
        } elseif ($request->query('preview') === '1' && auth()->guard('web')->check()) {
            $request->session()->put('appearance_preview', true);
        }

        // Preview HANYA aktif kalau (a) user login (guard web, sama seperti
        // admin) DAN (b) flag session menyala. Pengunjung yang tidak login
        // TIDAK PERNAH bisa mengaktifkan preview meski menambahkan
        // ?preview=1 di URL — dicek eksplisit di sini, bukan cuma di form
        // pengecekan di atas, supaya kondisi ini selalu final apa pun
        // urutan pemrosesan query di atas.
        $previewActive = auth()->guard('web')->check() && (bool) $request->session()->get('appearance_preview', false);

        $request->attributes->set('appearance_preview', $previewActive);

        $animationsEnabled = DisplaySetting::getBool('animations_enabled', true, $previewActive);

        // Iterasi 19 (Fase 4) — preset warna aksen & logo/branding, kedua
        // pengguna KEDUA dari alur draft/publish generik (setelah
        // animations_enabled di Iterasi 18). Dihitung di sini (bukan cuma
        // di AppearanceController) supaya tersedia di SEMUA halaman publik
        // lewat view()->share(), sama seperti $animationsEnabled — dipakai
        // resources/views/layouts/app.blade.php (<style> accent vars di
        // <head>) & portfolio/partials/navbar.blade.php + footer.blade.php
        // (logo teks vs gambar).
        $accentPreset = DisplaySetting::get('accent_preset', AccentPreset::DEFAULT, $previewActive);
        $logoType = DisplaySetting::get('logo_type', 'text', $previewActive);
        $logoImage = DisplaySetting::get('logo_image', null, $previewActive);

        // Iterasi 21 (Fase 4) — toggle sub-elemen halaman (Bagian A). Ketiga
        // key ini defaultnya TRUE (elemen tampil) kalau baris belum pernah
        // dibuat sama sekali, pola identik $animationsEnabled Iterasi 18.
        // Dibagikan di sini (bukan cuma AppearanceController) supaya
        // tersedia di SEMUA halaman publik lewat view()->share() — CTA
        // navbar & floating widget dipakai layouts/app.blade.php (shared
        // semua halaman publik), bukan cuma index.
        $navbarCtaVisible = DisplaySetting::getBool('navbar_cta_visible', true, $previewActive);
        $floatingWidgetVisible = DisplaySetting::getBool('floating_widget_visible', true, $previewActive);
        $heroSocialBarVisible = DisplaySetting::getBool('hero_social_bar_visible', true, $previewActive);

        $hasPendingDraft = $previewActive
            ? (DisplaySetting::hasPendingDraft() || SectionSetting::query()->whereNotNull('draft_overrides')->exists())
            : false;

        view()->share('appearancePreview', $previewActive);
        view()->share('animationsEnabled', $animationsEnabled);
        view()->share('appearanceHasDraft', $hasPendingDraft);
        view()->share('accentPreset', $accentPreset);
        view()->share('logoType', $logoType);
        view()->share('logoImage', $logoImage);
        view()->share('navbarCtaVisible', $navbarCtaVisible);
        view()->share('floatingWidgetVisible', $floatingWidgetVisible);
        view()->share('heroSocialBarVisible', $heroSocialBarVisible);

        return $next($request);
    }
}
