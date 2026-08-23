<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function index(): View
    {
        $skills = Skill::orderBy('sort_order')->get();

        return view('admin.skills.index', compact('skills'));
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
