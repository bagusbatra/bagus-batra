<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SectionSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionSettingController extends Controller
{
    /**
     * Daftar seluruh section publik beserta status aktif/nonaktifnya,
     * diurutkan sesuai urutan tampil di halaman index (sort_order).
     */
    public function index(): View
    {
        $sections = SectionSetting::orderBy('sort_order')->get();

        return view('admin.section-settings.index', compact('sections'));
    }

    /**
     * Toggle is_active satu section via AJAX (dipanggil dari switch di
     * halaman index) — auto-save, tanpa reload halaman. Mengembalikan
     * JSON supaya switch di frontend bisa langsung sinkron dgn state DB.
     */
    public function toggle(Request $request, SectionSetting $sectionSetting): JsonResponse
    {
        $sectionSetting->is_active = ! $sectionSetting->is_active;
        $sectionSetting->save();

        return response()->json([
            'success' => true,
            'section_key' => $sectionSetting->section_key,
            'is_active' => $sectionSetting->is_active,
        ]);
    }
}
