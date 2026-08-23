<?php

namespace Database\Seeders;

use App\Models\SectionSetting;
use Illuminate\Database\Seeder;

class SectionSettingSeeder extends Seeder
{
    /**
     * Seeds the 8 toggleable public sections, all active by default,
     * ordered exactly as they appear on the index page. Navbar & footer
     * are structural and intentionally excluded.
     */
    public function run(): void
    {
        $sections = [
            ['section_key' => 'hero', 'label' => 'Hero / Beranda', 'sort_order' => 0],
            ['section_key' => 'about', 'label' => 'Tentang Saya', 'sort_order' => 1],
            ['section_key' => 'skills', 'label' => 'Keahlian & Tech Stack', 'sort_order' => 2],
            ['section_key' => 'projects', 'label' => 'Proyek / Portfolio', 'sort_order' => 3],
            ['section_key' => 'playground', 'label' => 'Interactive Playground', 'sort_order' => 4],
            ['section_key' => 'experience', 'label' => 'Pengalaman Kerja', 'sort_order' => 5],
            ['section_key' => 'blog', 'label' => 'Artikel Blog', 'sort_order' => 6],
            ['section_key' => 'testimonials', 'label' => 'Testimoni', 'sort_order' => 7],
            ['section_key' => 'contact', 'label' => 'Kontak', 'sort_order' => 8],
        ];

        foreach ($sections as $section) {
            SectionSetting::updateOrCreate(
                ['section_key' => $section['section_key']],
                [
                    'label' => $section['label'],
                    'is_active' => true,
                    'sort_order' => $section['sort_order'],
                ]
            );
        }
    }
}
