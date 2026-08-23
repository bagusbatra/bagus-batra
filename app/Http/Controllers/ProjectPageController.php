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

    /**
     * GET /projects/{project:project_key} — full case-study page for one
     * project (replaces the old on-page modal, see Iterasi 11).
     */
    public function show(Project $project): View
    {
        // "Project Lainnya": prefer same-category projects first (excluding
        // the one being viewed), then fill up to 3 with any other project
        // ordered by sort_order. Intentionally simple — no scoring/algorithm,
        // per the Fase 2 scope decision (docs/RENCANA-PENGEMBANGAN.md #12).
        $related = Project::where('id', '!=', $project->id)
            ->where('category', $project->category)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        if ($related->count() < 3) {
            $more = Project::where('id', '!=', $project->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->orderBy('sort_order')
                ->limit(3 - $related->count())
                ->get();

            $related = $related->concat($more);
        }

        return view('projects.show', [
            'project' => $project,
            'related' => $related,
        ]);
    }
}
