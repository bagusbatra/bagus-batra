<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Singleton form — Profil & Hero (site_profiles, id = 1). No
     * create/delete, only edit, per docs/RENCANA-PENGEMBANGAN.md #5.
     */
    public function edit(): View
    {
        $profile = SiteProfile::current();

        return view('admin.profile.edit', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = SiteProfile::current();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'title_id' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'tagline_id' => ['required', 'string'],
            'tagline_en' => ['required', 'string'],
            'bio_id' => ['required', 'string'],
            'bio_en' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'github' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'available_for_work' => ['nullable', 'boolean'],
            'years_of_exp' => ['required', 'string', 'max:20'],
            'completed_projects' => ['required', 'string', 'max:20'],
            'client_satisfaction' => ['required', 'string', 'max:20'],
            'open_source_contributions' => ['required', 'string', 'max:20'],
            'avatar_url' => ['nullable', 'string', 'max:2048'],
            'avatar_file' => ['nullable', 'image', 'max:4096'],
            'secondary_avatar_url' => ['nullable', 'string', 'max:2048'],
            'secondary_avatar_file' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['available_for_work'] = $request->boolean('available_for_work');

        // Image field supports either a direct URL OR an uploaded file
        // (per docs/RENCANA-PENGEMBANGAN.md #4 "Upload gambar"). An
        // uploaded file takes precedence over the URL text field.
        $data['avatar'] = $this->resolveImage($request, 'avatar_file', $data['avatar_url'] ?? null, $profile->avatar);
        $data['secondary_avatar'] = $this->resolveImage($request, 'secondary_avatar_file', $data['secondary_avatar_url'] ?? null, $profile->secondary_avatar);

        unset($data['avatar_url'], $data['avatar_file'], $data['secondary_avatar_url'], $data['secondary_avatar_file']);

        $profile->update($data);

        return redirect()->route('admin.profile')->with('success', 'Profil & Hero berhasil diperbarui.');
    }

    /**
     * Resolve the final image value: uploaded file wins, then the typed
     * URL, then the value that was already saved (so leaving both blank
     * keeps whatever image was already there instead of clearing it).
     */
    private function resolveImage(Request $request, string $fileField, ?string $url, ?string $current): ?string
    {
        if ($request->hasFile($fileField)) {
            $path = $request->file($fileField)->store('avatars', 'public');

            return Storage::url($path);
        }

        if (filled($url)) {
            return $url;
        }

        return $current;
    }
}
