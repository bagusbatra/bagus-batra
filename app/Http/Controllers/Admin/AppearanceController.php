<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PortfolioController;
use App\Models\DisplaySetting;
use App\Models\SectionSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $hasPendingDraft = DisplaySetting::hasPendingDraft()
            || SectionSetting::whereNotNull('draft_overrides')->exists();

        $tab = $request->query('tab', 'ringkasan');

        return view('admin.appearance.index', compact('sections', 'animationsEnabled', 'hasPendingDraft', 'tab'));
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
