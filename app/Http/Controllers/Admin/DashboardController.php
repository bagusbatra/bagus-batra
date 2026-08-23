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
            'contact_messages' => ContactMessage::count(),
        ];

        $sections = SectionSetting::orderBy('sort_order')->get();

        return view('admin.dashboard', compact('stats', 'sections'));
    }
}
