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

class PortfolioController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('sort_order')->get();
        $projects = Project::orderBy('sort_order')->get();
        $blogPosts = BlogPost::orderBy('sort_order')->get();
        $experiences = Experience::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();

        // Iterasi 2: dibaca dari database (site_profiles / social_links),
        // bukan lagi config('portfolio.*'). SiteProfile's fillable columns
        // match the config('portfolio.personal_info') keys 1:1, and
        // SocialLink's fillable columns match config('portfolio.social_links')
        // items 1:1 (plus is_active/sort_order) — so every Blade partial
        // that reads $personalInfo['x'] / $link['x'] keeps working unchanged.
        $personalInfo = SiteProfile::current()->toArray();
        $socialLinks = SocialLink::where('is_active', true)->orderBy('sort_order')->get()->toArray();

        // section_key => is_active, queried once and shared with the view
        // (and every @include'd partial, since Blade partials inherit the
        // parent view's variables). Missing keys default to visible via
        // the `?? true` fallback used at each call site, so a partially
        // seeded table never hides a section by accident.
        $sectionActive = SectionSetting::pluck('is_active', 'section_key');

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
