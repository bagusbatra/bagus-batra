<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

/**
 * Public-facing Projects listing & detail pages (Fase 2 / Iterasi 10-11).
 *
 * Kept separate from PortfolioController (which owns the single-page `/`
 * index) because these two routes render entirely different Blade views
 * (`resources/views/projects/*`, not `resources/views/portfolio/*`) and
 * have their own controller-level concerns (pagination, route-model
 * binding by project_key, related-projects lookup) that don't belong
 * mixed into the one-shot `index()` method PortfolioController already has.
 */
class ProjectPageController extends Controller
{
    /**
     * GET /projects — full catalog of every project, paginated.
     */
    public function index(): View
    {
        $projects = Project::orderBy('sort_order')->paginate(12);

        return view('projects.index', compact('projects'));
    }
}
