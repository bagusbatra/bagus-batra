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

    public function index()
    {
        $skills = Skill::orderBy('sort_order')->get();

        // Iterasi 10 (Fase 2): section Projects di index sekarang jadi
        // highlight, bukan katalog lengkap (itu pindah ke halaman /projects,
        // lihat ProjectPageController). Hanya project featured=true yang
        // tampil; kalau belum ada satupun yang ditandai featured, fallback
        // ke 3 project pertama berdasar sort_order supaya section ini tidak
        // pernah kosong (lihat docs/RENCANA-PENGEMBANGAN.md #10).
        //
        // Iterasi 15 (Fase 3): sebelumnya query ini fetch SEMUA baris lalu
        // filter `featured` di PHP (Collection::where) — boros karena selalu
        // mengambil seluruh tabel meski cuma butuh subset featured. Sekarang
        // filter dilakukan di level SQL; query fallback (take 3) HANYA
        // dijalankan kalau hasil pertama benar-benar kosong, jadi skenario
        // normal (ada project featured) cukup 1 query, bukan 1 query
        // fetch-all + filter PHP.
        $projects = Project::where('featured', true)->orderBy('sort_order')->get();
        if ($projects->isEmpty()) {
            $projects = Project::orderBy('sort_order')->take(3)->get();
        }

        $blogPosts = BlogPost::orderBy('sort_order')->get();
        $experiences = Experience::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();

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

        return view('portfolio.index', compact(
            'skills',
            'projects',
            'blogPosts',
            'experiences',
            'testimonials',
            'personalInfo',
            'socialLinks',
            'sectionActive'
        ));
    }
}
