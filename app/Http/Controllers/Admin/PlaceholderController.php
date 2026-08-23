<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaceholderController extends Controller
{
    /**
     * Generic "Segera Hadir" page for admin menus whose CRUD/feature has
     * not been built yet — keeps the sidebar fully clickable without 404s
     * while making it explicit which future iteration owns the feature.
     */
    public function show(string $title, string $iterationNote): View
    {
        return view('admin.placeholder', [
            'title' => $title,
            'iterationNote' => $iterationNote,
        ]);
    }
}
