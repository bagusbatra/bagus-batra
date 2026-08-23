<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
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

        $personalInfo = config('portfolio.personal_info');
        $socialLinks = config('portfolio.social_links');

        return view('portfolio.index', compact(
            'skills',
            'projects',
            'blogPosts',
            'experiences',
            'testimonials',
            'personalInfo',
            'socialLinks'
        ));
    }
}
