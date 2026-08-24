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

        // Iterasi 20 (Fase 4) — 7 section top-level (lihat
        // PortfolioController::SECTION_PARTIALS) diurutkan menurut
        // sort_order EFEKTIF, preview=true SELALU dipakai di sini (sama pola
        // dgn $animationsEnabled/$accentPreset di atas) supaya form reorder
        // mencerminkan draft yang baru saja disimpan admin, bukan nilai
        // live. Dipakai UI drag-drop baru di tab "Urutan & Isi Section" —
        // TERPISAH dari $sections (list on/off Iterasi 1, 8 baris termasuk
        // "skills", tetap live-only/tidak berubah, lihat
        // admin/section-settings/_list.blade.php).
        $orderedTopLevelSections = SectionSetting::whereIn('section_key', array_keys(PortfolioController::SECTION_PARTIALS))
            ->get()
            ->keyBy('section_key')
            ->sortBy(fn (SectionSetting $s) => (int) $s->effective('sort_order', true))
            ->values();

        $tab = $request->query('tab', 'ringkasan');

        return view('admin.appearance.index', compact(
            'sections',
            'animationsEnabled',
            'accentPreset',
            'logoType',
            'logoImage',
            'accentPresets',
            'hasPendingDraft',
            'orderedTopLevelSections',
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
     * Iterasi 20 (Fase 4) — reorder (drag-drop) 7 section top-level +
     * jumlah item (display_count) untuk 3 section list-type (projects/blog/
     * testimonials), keduanya digabung jadi SATU form/aksi di UI (lihat
     * admin/appearance/index.blade.php, blok "Urutan Tampil & Jumlah Item").
     *
     * Menulis ke section_settings.draft_overrides (BUKAN langsung ke kolom
     * asli) — pengguna KETIGA dari alur draft generik Iterasi 18, sama
     * seperti updateAnimations()/updateBranding() di atas. publish()/
     * discardDraft() TIDAK perlu diubah sama sekali untuk mendukung ini.
     *
     * `order` = array 7 section_key sesuai urutan baru hasil drag-drop
     * (index array = sort_order baru). `display_count` = array asosiatif
     * opsional {projects, blog, testimonials} — dikirim SETIAP submit form
     * ini (bukan cuma saat benar-benar diubah admin), nilai kosong/null
     * berarti "pakai default" (lihat PortfolioController@index).
     */
    public function updateSections(Request $request): RedirectResponse
    {
        $sectionKeys = array_keys(PortfolioController::SECTION_PARTIALS);

        $validated = $request->validate([
            'order' => ['required', 'array', 'size:'.count($sectionKeys)],
            'order.*' => ['required', 'string', Rule::in($sectionKeys)],
            'display_count' => ['nullable', 'array'],
            'display_count.projects' => ['nullable', 'integer', 'min:1', 'max:50'],
            'display_count.blog' => ['nullable', 'integer', 'min:1', 'max:50'],
            'display_count.testimonials' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        // `order` harus persis 7 section_key top-level, masing2 sekali —
        // divalidasi manual di sini karena `distinct` + `in:` saja tidak
        // menjamin SEMUA key tercakup (mis. admin bisa saja kirim duplikat
        // yg lolos `in:` tapi hilang salah satu section).
        if (array_values(array_unique($validated['order'])) !== $validated['order']
            || count(array_diff($sectionKeys, $validated['order'])) > 0) {
            return redirect()
                ->route('admin.appearance', ['tab' => 'sections'])
                ->withErrors(['order' => 'Urutan section tidak valid (ada yang hilang/duplikat) — coba lagi.']);
        }

        $sections = SectionSetting::whereIn('section_key', $sectionKeys)->get()->keyBy('section_key');

        foreach ($validated['order'] as $index => $key) {
            $section = $sections->get($key);
            if (! $section) {
                continue;
            }

            $overrides = is_array($section->draft_overrides) ? $section->draft_overrides : [];
            $overrides['sort_order'] = $index;

            if (in_array($key, ['projects', 'blog', 'testimonials'], true)) {
                $overrides['display_count'] = $validated['display_count'][$key] ?? null;
            }

            $section->draft_overrides = $overrides;
            $section->save();
        }

        return redirect()
            ->route('admin.appearance', ['tab' => 'sections'])
            ->with('success', 'Urutan & jumlah item disimpan sebagai draft. Buka Preview untuk melihatnya, lalu Publish supaya berlaku di situs live.');
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
