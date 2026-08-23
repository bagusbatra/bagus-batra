<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SectionSetting;
use App\Models\Skill;
use App\Models\Testimonial;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'projects' => Project::count(),
            'blog_posts' => BlogPost::count(),
            'experiences' => Experience::count(),
            'testimonials' => Testimonial::count(),
            'skills' => Skill::count(),
            // Unread count (bukan total pesan) — sesuai rencana dashboard
            // "pesan kontak belum dibaca" di RENCANA-PENGEMBANGAN.md #5.
            'contact_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        $sections = SectionSetting::orderBy('sort_order')->get();

        $quickLinks = [
            ['route' => 'admin.profile', 'label' => 'Profil & Hero', 'icon' => 'user'],
            ['route' => 'admin.social-links', 'label' => 'Social Links', 'icon' => 'globe'],
            ['route' => 'admin.about-skills', 'label' => 'About & Skills', 'icon' => 'cpu'],
            ['route' => 'admin.projects', 'label' => 'Projects', 'icon' => 'folder-git-2'],
            ['route' => 'admin.experience', 'label' => 'Experience', 'icon' => 'briefcase'],
            ['route' => 'admin.blog', 'label' => 'Blog', 'icon' => 'book-open'],
            ['route' => 'admin.testimonials', 'label' => 'Testimonials', 'icon' => 'quote'],
            ['route' => 'admin.messages', 'label' => 'Pesan Masuk', 'icon' => 'mail'],
            ['route' => 'admin.section-settings', 'label' => 'Pengaturan Section', 'icon' => 'sliders'],
        ];

        return view('admin.dashboard', compact('stats', 'sections', 'quickLinks'));
    }
}
