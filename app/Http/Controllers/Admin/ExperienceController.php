<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ExperienceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Experience::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('role', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($type = $request->string('type')->trim()->value()) {
            $query->where('type', $type);
        }

        $experiences = $query->orderBy('sort_order')->paginate(10)->withQueryString();

        $typeOptions = Experience::query()->distinct()->orderBy('type')->pluck('type');

        return view('admin.experience.index', [
            'experiences' => $experiences,
            'typeOptions' => $typeOptions,
            'search' => $search ?? '',
            'typeFilter' => $type ?? '',
        ]);
    }

    public function create(): View
    {
        return view('admin.experience.form', ['experience' => new Experience()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['experience_key'] = $this->uniqueKey($data['company'], $data['role']);
        $data['sort_order'] = (int) (Experience::max('sort_order') ?? -1) + 1;

        Experience::create($data);

        return redirect()->route('admin.experience')->with('success', 'Experience baru berhasil ditambahkan.');
    }

    public function edit(Experience $experience): View
    {
        return view('admin.experience.form', compact('experience'));
    }

    public function update(Request $request, Experience $experience): RedirectResponse
    {
        // experience_key is intentionally immutable after creation, same
        // rationale as Project::project_key (see docs/LOG-ITERASI.md Iterasi 4).
        $experience->update($this->validated($request));

        return redirect()->route('admin.experience')->with('success', 'Experience berhasil diperbarui.');
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();

        return redirect()->route('admin.experience')->with('success', 'Experience berhasil dihapus.');
    }

    public function move(Request $request, Experience $experience): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $neighbour = $direction === 'up'
            ? Experience::where('sort_order', '<', $experience->sort_order)->orderBy('sort_order', 'desc')->first()
            : Experience::where('sort_order', '>', $experience->sort_order)->orderBy('sort_order', 'asc')->first();

        if ($neighbour) {
            [$a, $b] = [$experience->sort_order, $neighbour->sort_order];
            $experience->update(['sort_order' => $b]);
            $neighbour->update(['sort_order' => $a]);
        }

        return redirect()->route('admin.experience');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'period' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'achievements' => ['nullable', 'array'],
            'achievements.*' => ['nullable', 'string', 'max:500'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['nullable', 'string', 'max:100'],
            'featured' => ['nullable', 'boolean'],
        ]);

        $data['featured'] = $request->boolean('featured');
        $data['achievements'] = array_values(array_filter($data['achievements'] ?? [], fn ($v) => filled($v)));
        $data['skills'] = array_values(array_filter($data['skills'] ?? [], fn ($v) => filled($v)));

        return $data;
    }

    private function uniqueKey(string $company, string $role): string
    {
        $base = Str::slug($company.'-'.$role) ?: 'experience';
        $key = $base;
        $i = 2;

        while (Experience::where('experience_key', $key)->exists()) {
            $key = "{$base}-{$i}";
            $i++;
        }

        return $key;
    }
}
