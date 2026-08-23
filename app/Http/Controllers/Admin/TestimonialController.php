<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $query = Testimonial::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($rating = $request->string('rating')->trim()->value()) {
            $query->where('rating', (int) $rating);
        }

        $testimonials = $query->orderBy('sort_order')->paginate(10)->withQueryString();

        return view('admin.testimonials.index', [
            'testimonials' => $testimonials,
            'search' => $search ?? '',
            'ratingFilter' => $rating ?? '',
        ]);
    }

    public function create(): View
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['testimonial_key'] = $this->uniqueKey($data['name'], $data['company']);
        $data['sort_order'] = (int) (Testimonial::max('sort_order') ?? -1) + 1;
        $data['avatar'] = $this->resolveImage($request, null);

        Testimonial::create($data);

        return redirect()->route('admin.testimonials')->with('success', 'Testimoni baru berhasil ditambahkan.');
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        // testimonial_key is intentionally immutable after creation, same
        // rationale as Project::project_key (see docs/LOG-ITERASI.md Iterasi 4).
        $data = $this->validated($request);
        $data['avatar'] = $this->resolveImage($request, $testimonial->avatar);

        $testimonial->update($data);

        return redirect()->route('admin.testimonials')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials')->with('success', 'Testimoni berhasil dihapus.');
    }

    public function move(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $neighbour = $direction === 'up'
            ? Testimonial::where('sort_order', '<', $testimonial->sort_order)->orderBy('sort_order', 'desc')->first()
            : Testimonial::where('sort_order', '>', $testimonial->sort_order)->orderBy('sort_order', 'asc')->first();

        if ($neighbour) {
            [$a, $b] = [$testimonial->sort_order, $neighbour->sort_order];
            $testimonial->update(['sort_order' => $b]);
            $neighbour->update(['sort_order' => $a]);
        }

        return redirect()->route('admin.testimonials');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'project_tag' => ['nullable', 'string', 'max:100'],
            'avatar_url' => ['nullable', 'string', 'max:2048'],
            'avatar_file' => ['nullable', 'image', 'max:4096'],
        ]);

        unset($data['avatar_url'], $data['avatar_file']);

        return $data;
    }

    private function resolveImage(Request $request, ?string $current): ?string
    {
        if ($request->hasFile('avatar_file')) {
            return Storage::url($request->file('avatar_file')->store('testimonials', 'public'));
        }

        if (filled($request->input('avatar_url'))) {
            return $request->input('avatar_url');
        }

        return $current;
    }

    private function uniqueKey(string $name, string $company): string
    {
        $base = Str::slug($name.'-'.$company) ?: 'testimonial';
        $key = $base;
        $i = 2;

        while (Testimonial::where('testimonial_key', $key)->exists()) {
            $key = "{$base}-{$i}";
            $i++;
        }

        return $key;
    }
}
