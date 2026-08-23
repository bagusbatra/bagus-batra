<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillController extends Controller
{
    public const CATEGORIES = [
        'frontend' => 'Frontend',
        'backend' => 'Backend',
        'devops' => 'DevOps & Cloud',
        'tools' => 'Tools & UI',
    ];

    /**
     * Search + category filter — added in Iterasi 9 for consistency with
     * the other list/table admin pages (Projects/Blog/Experience/
     * Testimonials all have this same search+filter form; Skills was the
     * one CRUD without it, see docs/LOG-ITERASI.md Iterasi 9). No
     * pagination here on purpose: the dataset is small and bounded
     * (fixed tech stack, reordered manually with up/down buttons), same
     * reasoning already applied to Social Links.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $categoryFilter = trim((string) $request->query('category', ''));

        $query = Skill::query();

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryFilter !== '') {
            $query->where('category', $categoryFilter);
        }

        $skills = $query->orderBy('sort_order')->get();

        return view('admin.skills.index', [
            'skills' => $skills,
            'search' => $search,
            'categoryFilter' => $categoryFilter,
            'categories' => self::CATEGORIES,
            // Global (unfiltered) boundaries — used to disable the up/down
            // buttons only at the *true* first/last row, not just the
            // first/last row of the current filtered view (Iterasi 9 fix;
            // filtering didn't exist yet when the disabled-at-loop-first/
            // last logic was first written).
            'minSortOrder' => (int) (Skill::min('sort_order') ?? 0),
            'maxSortOrder' => (int) (Skill::max('sort_order') ?? 0),
        ]);
    }

    public function create(): View
    {
        return view('admin.skills.form', ['skill' => new Skill()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) (Skill::max('sort_order') ?? -1) + 1;

        Skill::create($data);

        return redirect()->route('admin.about-skills')->with('success', 'Skill baru berhasil ditambahkan.');
    }

    public function edit(Skill $skill): View
    {
        return view('admin.skills.form', compact('skill'));
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $skill->update($this->validated($request));

        return redirect()->route('admin.about-skills')->with('success', 'Skill berhasil diperbarui.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return redirect()->route('admin.about-skills')->with('success', 'Skill berhasil dihapus.');
    }

    /**
     * Swap sort_order with the neighbouring row — same simple reorder
     * pattern as SocialLinkController@move (see its docblock for why).
     */
    public function move(Request $request, Skill $skill): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $neighbour = $direction === 'up'
            ? Skill::where('sort_order', '<', $skill->sort_order)->orderBy('sort_order', 'desc')->first()
            : Skill::where('sort_order', '>', $skill->sort_order)->orderBy('sort_order', 'asc')->first();

        if ($neighbour) {
            [$a, $b] = [$skill->sort_order, $neighbour->sort_order];
            $skill->update(['sort_order' => $b]);
            $neighbour->update(['sort_order' => $a]);
        }

        return redirect()->route('admin.about-skills');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:frontend,backend,devops,tools'],
            'level' => ['required', 'integer', 'min:0', 'max:100'],
            'experience' => ['required', 'string', 'max:50'],
            'icon_name' => ['required', 'string', 'max:50'],
            'highlight_text' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
