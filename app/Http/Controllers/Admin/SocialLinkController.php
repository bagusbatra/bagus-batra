<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocialLinkController extends Controller
{
    public function index(): View
    {
        $socialLinks = SocialLink::orderBy('sort_order')->get();

        return view('admin.social-links.index', compact('socialLinks'));
    }

    public function create(): View
    {
        return view('admin.social-links.form', ['socialLink' => new SocialLink()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) (SocialLink::max('sort_order') ?? -1) + 1;

        SocialLink::create($data);

        return redirect()->route('admin.social-links')->with('success', 'Social link baru berhasil ditambahkan.');
    }

    public function edit(SocialLink $socialLink): View
    {
        return view('admin.social-links.form', compact('socialLink'));
    }

    public function update(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update($this->validated($request));

        return redirect()->route('admin.social-links')->with('success', 'Social link berhasil diperbarui.');
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();

        return redirect()->route('admin.social-links')->with('success', 'Social link berhasil dihapus.');
    }

    /**
     * Move one row up/down in display order by swapping sort_order with
     * its neighbour — simple, dependable reordering for a short list
     * (~6 items) without needing a drag-and-drop library.
     */
    public function move(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $neighbour = $direction === 'up'
            ? SocialLink::where('sort_order', '<', $socialLink->sort_order)->orderBy('sort_order', 'desc')->first()
            : SocialLink::where('sort_order', '>', $socialLink->sort_order)->orderBy('sort_order', 'asc')->first();

        if ($neighbour) {
            [$a, $b] = [$socialLink->sort_order, $neighbour->sort_order];
            $socialLink->update(['sort_order' => $b]);
            $neighbour->update(['sort_order' => $a]);
        }

        return redirect()->route('admin.social-links');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'platform' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:2048'],
            'username' => ['nullable', 'string', 'max:255'],
            'icon' => ['required', 'string', 'max:50'],
            'bg_color' => ['nullable', 'string', 'max:255'],
            'text_color' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
