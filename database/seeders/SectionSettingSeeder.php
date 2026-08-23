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
            ['section_key' => 'hero', 'label' => 'Hero', 'sort_order' => 0],
            ['section_key' => 'about', 'label' => 'About', 'sort_order' => 1],
            ['section_key' => 'skills', 'label' => 'Skills', 'sort_order' => 2],
            ['section_key' => 'projects', 'label' => 'Projects', 'sort_order' => 3],
            ['section_key' => 'playground', 'label' => 'Interactive Playground', 'sort_order' => 4],
            ['section_key' => 'experience', 'label' => 'Experience', 'sort_order' => 5],
            ['section_key' => 'blog', 'label' => 'Blog', 'sort_order' => 6],
            ['section_key' => 'testimonials', 'label' => 'Testimonials', 'sort_order' => 7],
            ['section_key' => 'contact', 'label' => 'Contact', 'sort_order' => 8],
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
