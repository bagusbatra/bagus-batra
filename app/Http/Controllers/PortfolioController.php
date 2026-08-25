<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SectionSetting;
use App\Models\SiteProfile;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{
    /**
     * Cache keys untuk SocialLink aktif & peta SectionSetting — dipakai di
     * sini dan di Admin\SocialLinkController / Admin\SectionSettingController
     * untuk invalidasi setelah setiap perubahan (Iterasi 15, lihat
     * docs/RENCANA-OPTIMASI-PERFORMA.md Iterasi 15).
     */
    public const SOCIAL_LINKS_CACHE_KEY = 'social_links_active';

    public const SECTION_SETTINGS_CACHE_KEY = 'section_settings_map';

    /**
     * Iterasi 20 (Fase 4) — pemetaan section_key => partial view untuk 7
     * section top-level yang BENAR-BENAR bisa direorder & di-@include
     * terpisah (lihat docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 3 baris
     * "Reorder section — dampak struktural"). "skills" SENGAJA tidak ada di
     * sini — ia nested di dalam partial "about" (lihat komentar di
     * portfolio/partials/about.blade.php), jadi tidak punya posisi DOM
     * independen untuk direorder; hanya togglenya yang fungsional (lewat
     * $sectionActive di bawah, tidak berubah dari Iterasi 1-19).
     */
    public const SECTION_PARTIALS = [
        'hero' => 'portfolio.partials.hero',
        'about' => 'portfolio.partials.about',
        'projects' => 'portfolio.partials.projects',
        'experience' => 'portfolio.partials.experience',
        'blog' => 'portfolio.partials.blog',
        'testimonials' => 'portfolio.partials.testimonials',
        'contact' => 'portfolio.partials.contact',
    ];

    public function index(Request $request)
    {
        $skills = Skill::orderBy('sort_order')->get();

        // Iterasi 20 (Fase 4): urutan & aktif/tidaknya 7 section top-level
        // sekarang ditentukan oleh section_settings.sort_order/is_active
        // EFEKTIF (draft-aware lewat SectionSetting::effective(), lihat
        // App\Models\SectionSetting), BUKAN lagi urutan tetap yang di-hardcode
        // di portfolio/index.blade.php. Query di sini SENGAJA TIDAK memakai
        // cache Iterasi 15 (SECTION_SETTINGS_CACHE_KEY di bawah) — cache itu
        // cuma pluck is_active LIVE (dipakai $sectionActive utk toggle
        // "skills" yang memang selalu live, lihat di bawah), sedangkan
        // urutan/is_active efektif di sini HARUS preview-aware per-request
        // (beda utk admin yang login+preview vs visitor biasa dalam request
        // yang sama-sama nge-hit endpoint ini). 7-8 baris section_settings
        // sangat ringan untuk di-query langsung tanpa cache setiap request.
        $previewActive = (bool) $request->attributes->get('appearance_preview', false);

        $topLevelSections = SectionSetting::whereIn('section_key', array_keys(self::SECTION_PARTIALS))
            ->get()
            ->keyBy('section_key');

        $orderedSections = collect(self::SECTION_PARTIALS)
            ->keys()
            ->map(fn (string $key) => $topLevelSections->get($key))
            ->filter()
            ->filter(fn (SectionSetting $section) => (bool) $section->effective('is_active', $previewActive))
            ->sortBy(fn (SectionSetting $section) => (int) $section->effective('sort_order', $previewActive))
            ->map(fn (SectionSetting $section) => self::SECTION_PARTIALS[$section->section_key])
            ->values();

        // Iterasi 20 (Fase 4): display_count efektif (draft-aware) untuk 3
        // section list-type. NULL = pakai fallback/default lama (lihat
        // docs/RENCANA-KUSTOMISASI-TAMPILAN.md bagian 5 Iterasi 20).
        $projectCount = (int) ($topLevelSections->get('projects')?->effective('display_count', $previewActive) ?? 3);
        $blogCount = $topLevelSections->get('blog')?->effective('display_count', $previewActive);
        $testimonialsCount = $topLevelSections->get('testimonials')?->effective('display_count', $previewActive);

        // Iterasi 10 (Fase 2): section Projects di index sekarang jadi
        // highlight, bukan katalog lengkap (itu pindah ke halaman /projects,
        // lihat ProjectPageController). Hanya project featured=true yang
        // tampil; kalau belum ada satupun yang ditandai featured, fallback
        // ke $projectCount project pertama berdasar sort_order supaya
        // section ini tidak pernah kosong (lihat
        // docs/RENCANA-PENGEMBANGAN.md #10).
        //
        // Iterasi 15 (Fase 3): sebelumnya query ini fetch SEMUA baris lalu
        // filter `featured` di PHP (Collection::where) — boros karena selalu
        // mengambil seluruh tabel meski cuma butuh subset featured. Sekarang
        // filter dilakukan di level SQL; query fallback HANYA dijalankan
        // kalau hasil pertama benar-benar kosong, jadi skenario normal (ada
        // project featured) cukup 1 query, bukan 1 query fetch-all + filter
        // PHP.
        //
        // Iterasi 20 (Fase 4): kedua query di-`take($projectCount)`, bukan
        // lagi hardcode 3 — default TETAP 3 kalau admin belum pernah
        // mengatur display_count (lihat $projectCount di atas). Karena
        // baseline seed hanya berisi 3 project featured, `take(3)` di query
        // pertama tidak mengubah hasil untuk baseline (regresi aman).
        $projects = Project::where('featured', true)->orderBy('sort_order')->take($projectCount)->get();
        if ($projects->isEmpty()) {
            $projects = Project::orderBy('sort_order')->take($projectCount)->get();
        }

        // Iterasi 20 (Fase 4): blog & testimonials SEBELUMNYA tidak pernah
        // di-take() sama sekali di index (selalu tampil SEMUA baris) — itu
        // jadi "default" yang harus dipertahankan kalau display_count belum
        // diatur admin (NULL). take() hanya diterapkan kalau display_count
        // efektif terisi angka.
        $blogQuery = BlogPost::orderBy('sort_order');
        if ($blogCount !== null) {
            $blogQuery->take((int) $blogCount);
        }
        $blogPosts = $blogQuery->get();

        $experiences = Experience::orderBy('sort_order')->get();

        $testimonialsQuery = Testimonial::orderBy('sort_order');
        if ($testimonialsCount !== null) {
            $testimonialsQuery->take((int) $testimonialsCount);
        }
        $testimonials = $testimonialsQuery->get();

        // Iterasi 2: dibaca dari database (site_profiles / social_links),
        // bukan lagi config('portfolio.*'). SiteProfile's fillable columns
        // match the config('portfolio.personal_info') keys 1:1, and
        // SocialLink's fillable columns match config('portfolio.social_links')
        // items 1:1 (plus is_active/sort_order) — so every Blade partial
        // that reads $personalInfo['x'] / $link['x'] keeps working unchanged.
        //
        // Iterasi 15 (Fase 3): SiteProfile::current() sudah self-caching
        // (lihat app/Models/SiteProfile.php). SocialLink aktif & peta
        // SectionSetting di-cache di sini (bukan di model) karena keduanya
        // hasil query yang dibentuk khusus untuk kebutuhan halaman index
        // (filter is_active + urutan tertentu / pluck ke map), bukan
        // singleton "current row" seperti SiteProfile — pola yang sudah
        // konsisten dgn cara controller ini menyusun data sejak awal.
        $personalInfo = SiteProfile::current()->toArray();
        $socialLinks = Cache::remember(self::SOCIAL_LINKS_CACHE_KEY, 3600, function () {
            return SocialLink::where('is_active', true)->orderBy('sort_order')->get()->toArray();
        });

        // section_key => is_active, queried once and shared with the view
        // (and every @include'd partial, since Blade partials inherit the
        // parent view's variables). Missing keys default to visible via
        // the `?? true` fallback used at each call site, so a partially
        // seeded table never hides a section by accident.
        //
        // Cache di sini disimpan sebagai array PLAIN (bukan Collection hasil
        // ->pluck()) — project ini pakai `serializable_classes = false` di
        // config/cache.php (default keamanan Laravel terbaru), jadi objek
        // apa pun (termasuk Illuminate\Support\Collection) akan diam-diam
        // rusak jadi __PHP_Incomplete_Class saat dibaca ulang dari cache.
        // Array biasa aman. Semua pemakaian $sectionActive di Blade cuma
        // akses `[]` (lihat portfolio/index.blade.php dst), jadi array
        // biasa berperilaku identik dengan Collection untuk kebutuhan ini.
        $sectionActive = Cache::remember(self::SECTION_SETTINGS_CACHE_KEY, 3600, function () {
            return SectionSetting::pluck('is_active', 'section_key')->all();
        });

        // Iterasi 21 (Fase 4, Bagian B): $topLevelSections (sudah dibangun di
        // atas utk reorder/display_count) dikirim juga ke view supaya tiap
        // partial section bisa memanggil ->effective('heading_id'/'heading_en'/
        // 'subheading_id'/'subheading_en', $appearancePreview) untuk custom
        // heading/subheading-nya sendiri (fallback ke teks hardcoded kalau
        // NULL). $appearancePreview sendiri sudah dibagikan ke SEMUA view
        // lewat HandleAppearancePreview (Iterasi 18), tidak perlu dikirim
        // ulang di sini.
        return view('portfolio.index', compact(
            'skills',
            'projects',
            'blogPosts',
            'experiences',
            'testimonials',
            'personalInfo',
            'socialLinks',
            'sectionActive',
            'orderedSections',
            'topLevelSections'
        ));
    }
}
