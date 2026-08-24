<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PortfolioController;
use App\Models\DisplaySetting;
use App\Models\SectionSetting;
use App\Support\AccentPreset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Menu admin "Tampilan Halaman Index" — Iterasi 18 (Fase 4). Lihat
 * docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 4 & 5 Iterasi 18.
 *
 * Digabung dengan "Pengaturan Section" lama (Fase 1) jadi satu menu dengan
 * beberapa tab — lihat catatan keputusan di resources/views/admin/appearance
 * /index.blade.php & docs/LOG-ITERASI.md entri Iterasi 18.
 *
 * publish()/discardDraft() generik: dipakai oleh SEMUA setting Fase 4 ke
 * depan (display_settings.value_draft & section_settings.draft_overrides),
 * bukan cuma animations_enabled — iterasi berikutnya (19-22) tinggal
 * menulis ke layer draft yang sama, tanpa perlu controller publish/discard
 * baru.
 */
class AppearanceController extends Controller
{
    public function index(Request $request): View
    {
        $sections = SectionSetting::orderBy('sort_order')->get();

        // Admin yang sedang mengedit SELALU melihat nilai efektif draft
        // (preview = true) di form ini, supaya form mencerminkan apa yang
        // baru saja mereka simpan sebagai draft — beda dengan visitor
        // publik yang lewat DisplaySetting::get() dgn preview=false.
        $animationsEnabled = DisplaySetting::getBool('animations_enabled', true, true);

        // Iterasi 19 (Fase 4) — sama pola dgn $animationsEnabled di atas:
        // admin yang sedang mengedit form SELALU melihat nilai efektif
        // draft (preview = true) supaya form mencerminkan draft yang baru
        // saja disimpan, bukan nilai live.
        $accentPreset = DisplaySetting::get('accent_preset', AccentPreset::DEFAULT, true);
        $logoType = DisplaySetting::get('logo_type', 'text', true);
        $logoImage = DisplaySetting::get('logo_image', null, true);
        $accentPresets = AccentPreset::PRESETS;

        $hasPendingDraft = DisplaySetting::hasPendingDraft()
            || SectionSetting::whereNotNull('draft_overrides')->exists();

        $tab = $request->query('tab', 'ringkasan');

        return view('admin.appearance.index', compact(
            'sections',
            'animationsEnabled',
            'accentPreset',
            'logoType',
            'logoImage',
            'accentPresets',
            'hasPendingDraft',
            'tab'
        ));
    }

    /**
     * Bukti konsep Iterasi 18 — toggle animasi reveal-on-scroll. Menyimpan
     * ke `display_settings.value_draft`, BUKAN langsung ke `value` live.
     */
    public function updateAnimations(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'animations_enabled' => ['nullable', 'boolean'],
        ]);

        DisplaySetting::setDraft('animations_enabled', $request->boolean('animations_enabled'));

        return redirect()
            ->route('admin.appearance', ['tab' => 'animasi'])
            ->with('success', 'Perubahan disimpan sebagai draft. Buka Preview untuk melihatnya, lalu Publish supaya berlaku di situs live.');
    }

    /**
     * Iterasi 19 (Fase 4) — preset warna aksen & logo/branding. Pengguna
     * KEDUA dari alur draft generik Iterasi 18 (setelah animations_enabled)
     * — menyimpan ke display_settings.value_draft, BUKAN langsung live,
     * lewat DisplaySetting::setDraft() yang sama. publish()/discardDraft()
     * di bawah TIDAK perlu diubah sama sekali untuk mendukung ini —
     * keduanya sudah generik (loop SEMUA display_settings.value_draft
     * non-null), persis seperti yang didesain di Iterasi 18.
     *
     * Upload logo mengikuti pola yang sama dengan
     * Admin\ProjectController/TestimonialController: file disimpan ke
     * storage/app/public/branding lewat disk "public", URL publiknya
     * (Storage::url()) yang disimpan sebagai draft — bukan file itu
     * sendiri. File hanya diproses kalau admin benar-benar upload file baru
     * di request ini; kalau tidak, logo_image draft yang sudah ada
     * (atau nilai live) tetap dipertahankan apa adanya.
     */
    public function updateBranding(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'accent_preset' => ['required', Rule::in(AccentPreset::keys())],
            'logo_type' => ['required', 'in:text,image'],
            'logo_image_file' => ['nullable', 'image', 'max:2048'],
        ]);

        DisplaySetting::setDraft('accent_preset', $validated['accent_preset']);
        DisplaySetting::setDraft('logo_type', $validated['logo_type']);

        if ($request->hasFile('logo_image_file')) {
            $url = Storage::url($request->file('logo_image_file')->store('branding', 'public'));
            DisplaySetting::setDraft('logo_image', $url);
        }

        return redirect()
            ->route('admin.appearance', ['tab' => 'branding'])
            ->with('success', 'Perubahan disimpan sebagai draft. Buka Preview untuk melihatnya, lalu Publish supaya berlaku di situs live.');
    }

    /**
     * Publish SEMUA draft pending (display_settings.value_draft +
     * section_settings.draft_overrides) sekaligus — bukan per-setting,
     * sesuai model "satu snapshot draft vs satu live" di bagian 3 rencana.
     */
    public function publish(Request $request): RedirectResponse
    {
        DisplaySetting::publishAll();

        SectionSetting::whereNotNull('draft_overrides')->get()->each(function (SectionSetting $section) {
            $section->publishDraftOverrides();
        });

        // section_settings bisa berubah (is_active dkk) lewat draft_overrides
        // di iterasi2 berikutnya — invalidasi cache yang sama dipakai
        // SectionSettingController@toggle supaya publik langsung lihat hasil
        // publish tanpa perlu request tambahan.
        Cache::forget(PortfolioController::SECTION_SETTINGS_CACHE_KEY);

        return redirect()
            ->route('admin.appearance')
            ->with('success', 'Perubahan berhasil dipublish dan sekarang live untuk semua pengunjung.');
    }

    /**
     * Buang SEMUA draft pending tanpa menerapkannya — live TIDAK berubah.
     */
    public function discardDraft(Request $request): RedirectResponse
    {
        DisplaySetting::discardAllDrafts();

        SectionSetting::whereNotNull('draft_overrides')->get()->each(function (SectionSetting $section) {
            $section->discardDraftOverrides();
        });

        return redirect()
            ->route('admin.appearance')
            ->with('success', 'Draft dibuang. Situs live tidak berubah.');
    }
}
