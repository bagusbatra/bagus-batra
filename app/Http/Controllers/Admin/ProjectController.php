<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Categories a project can actually belong to. "All" is a filter-only
     * pseudo-category on the public page (projects.blade.php), never a
     * real project category, so it is intentionally excluded here.
     */
    public const CATEGORIES = ['Full-Stack', 'Frontend', 'UI/UX & Systems', 'Open Source', 'AI & Tools'];

    /**
     * Iterasi 28 (Fase 5) — 7 blok konten OPSIONAL di halaman detail publik
     * (resources/views/projects/show.blade.php) yang admin bisa paksa
     * sembunyikan per-project lewat `hidden_blocks`, meski datanya terisi.
     * Blok kerangka halaman (banner, breadcrumb, tags footer, tombol demo/
     * github, tab Overview & Architecture ITU SENDIRI, field wajib spt
     * long_description) SENGAJA TIDAK termasuk — lihat
     * docs/RENCANA-PENYEMPURNAAN-ADMIN.md bagian 3.
     */
    public const HIDEABLE_BLOCKS = [
        'metrics' => 'Hasil & Dampak Terukur (Metrics)',
        'highlights' => 'Sorotan Fitur & Inovasi (Highlights)',
        'tech_frontend' => 'Tech Stack — Frontend & Client',
        'tech_backend' => 'Tech Stack — Backend & API',
        'tech_database' => 'Tech Stack — Database & Caching',
        'tech_cloud' => 'Tech Stack — Cloud, CI/CD & Deployment',
        'tab_preview' => 'Tab "Simulasi Interaktif" (Live Sandbox)',
    ];

    public function index(Request $request): View
    {
        $query = Project::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = $request->string('category')->trim()->value()) {
            $query->where('category', $category);
        }

        $projects = $query->orderBy('sort_order')->paginate(10)->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'categories' => self::CATEGORIES,
            'search' => $search ?? '',
            'categoryFilter' => $category ?? '',
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => new Project(),
            'categories' => self::CATEGORIES,
            'hideableBlocks' => self::HIDEABLE_BLOCKS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['project_key'] = $this->uniqueKey($data['title']);
        $data['sort_order'] = (int) (Project::max('sort_order') ?? -1) + 1;
        $data['image'] = $this->resolveImage($request, null);

        Project::create($data);

        return redirect()->route('admin.projects')->with('success', 'Project baru berhasil ditambahkan.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', [
            'project' => $project,
            'categories' => self::CATEGORIES,
            'hideableBlocks' => self::HIDEABLE_BLOCKS,
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validated($request);
        // project_key is intentionally immutable after creation — it's the
        // stable business key referenced in DOM ids / URLs (see docs/ERD.md).
        $data['image'] = $this->resolveImage($request, $project->image);

        $project->update($data);

        return redirect()->route('admin.projects')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects')->with('success', 'Project berhasil dihapus.');
    }

    public function move(Request $request, Project $project): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $neighbour = $direction === 'up'
            ? Project::where('sort_order', '<', $project->sort_order)->orderBy('sort_order', 'desc')->first()
            : Project::where('sort_order', '>', $project->sort_order)->orderBy('sort_order', 'asc')->first();

        if ($neighbour) {
            [$a, $b] = [$project->sort_order, $neighbour->sort_order];
            $project->update(['sort_order' => $b]);
            $neighbour->update(['sort_order' => $a]);
        }

        return redirect()->route('admin.projects');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'long_description' => ['required', 'string'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'role' => ['required', 'string', 'max:255'],
            'timeline' => ['required', 'string', 'max:100'],
            'client' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:100'],
            'metrics' => ['nullable', 'array'],
            'metrics.*.label' => ['nullable', 'string', 'max:100'],
            'metrics.*.value' => ['nullable', 'string', 'max:50'],
            'highlights' => ['nullable', 'array'],
            'highlights.*' => ['nullable', 'string', 'max:500'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.frontend' => ['nullable', 'array'],
            'tech_stack.frontend.*' => ['nullable', 'string', 'max:100'],
            'tech_stack.backend' => ['nullable', 'array'],
            'tech_stack.backend.*' => ['nullable', 'string', 'max:100'],
            'tech_stack.database' => ['nullable', 'array'],
            'tech_stack.database.*' => ['nullable', 'string', 'max:100'],
            'tech_stack.cloudAndDevOps' => ['nullable', 'array'],
            'tech_stack.cloudAndDevOps.*' => ['nullable', 'string', 'max:100'],
            'demo_url' => ['nullable', 'string', 'max:2048'],
            'github_url' => ['nullable', 'string', 'max:2048'],
            'featured' => ['nullable', 'boolean'],
            'color_gradient' => ['nullable', 'string', 'max:255'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'hidden_blocks' => ['nullable', 'array'],
            'hidden_blocks.*' => ['string', Rule::in(array_keys(self::HIDEABLE_BLOCKS))],
        ]);

        $data['featured'] = $request->boolean('featured');
        // Iterasi 28 (Fase 5): checkbox array — field absen dari request sama
        // sekali (semua kotak dikosongkan admin) berarti "tidak ada yang
        // disembunyikan", BUKAN "tidak berubah" — beda dgn pola display_count
        // Fase 4 (yg memang selalu re-submit semua nilai tiap saat), array
        // checkbox HTML native memang begini (checkbox yg tidak dicentang
        // TIDAK ikut terkirim di form POST sama sekali).
        $data['hidden_blocks'] = array_values($data['hidden_blocks'] ?? []);

        // Repeater rows may include blank rows the user added then didn't
        // fill in — drop empty entries so the JSON stored is clean.
        $data['tags'] = array_values(array_filter($data['tags'] ?? [], fn ($v) => filled($v)));
        $data['highlights'] = array_values(array_filter($data['highlights'] ?? [], fn ($v) => filled($v)));
        $data['metrics'] = array_values(array_filter($data['metrics'] ?? [], fn ($m) => filled($m['label'] ?? null) || filled($m['value'] ?? null)));

        $techStack = [];
        foreach (['frontend', 'backend', 'database', 'cloudAndDevOps'] as $group) {
            $techStack[$group] = array_values(array_filter($data['tech_stack'][$group] ?? [], fn ($v) => filled($v)));
        }
        $data['tech_stack'] = $techStack;

        unset($data['image_url'], $data['image_file']);

        return $data;
    }

    private function resolveImage(Request $request, ?string $current): ?string
    {
        if ($request->hasFile('image_file')) {
            return Storage::url($request->file('image_file')->store('projects', 'public'));
        }

        if (filled($request->input('image_url'))) {
            return $request->input('image_url');
        }

        return $current;
    }

    private function uniqueKey(string $title): string
    {
        $base = Str::slug($title) ?: 'project';
        $key = $base;
        $i = 2;

        while (Project::where('project_key', $key)->exists()) {
            $key = "{$base}-{$i}";
            $i++;
        }

        return $key;
    }
}
