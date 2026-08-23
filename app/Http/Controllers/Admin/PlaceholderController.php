<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    /**
     * Generic "Segera Hadir" page for admin menus whose CRUD/feature has
     * not been built yet — keeps the sidebar fully clickable without 404s
     * while making it explicit which future iteration owns the feature.
     *
     * [route name suffix (= URI slug) => [menu title, "Segera Hadir" note]]
     *
     * Iterasi 16 (Fase 3): sebelumnya `routes/admin.php` mendaftarkan
     * route ini lewat closure (`fn () => app(...)->show(...)`) supaya bisa
     * meneruskan title/note per slug. Closure route TIDAK bisa dipakai
     * dengan `php artisan route:cache` (Laravel melempar error saat build
     * cache kalau ada route berbasis Closure), jadi data title/note
     * dipindah ke sini sebagai konstanta, dan route didaftarkan sebagai
     * pemanggilan Controller@method biasa (lihat routes/admin.php) —
     * slug ditentukan dari nama route saat runtime, bukan dari parameter
     * closure lagi.
     */
    public const PLACEHOLDERS = [
        'playground' => ['Playground', 'Segera hadir — saklar aktif/nonaktifnya sudah ada di menu Pengaturan Section'],
    ];

    public function show(Request $request): View
    {
        $slug = Str::after($request->route()->getName(), 'admin.');

        [$title, $iterationNote] = self::PLACEHOLDERS[$slug] ?? [Str::title($slug), 'Segera hadir.'];

        return view('admin.placeholder', [
            'title' => $title,
            'iterationNote' => $iterationNote,
        ]);
    }
}
