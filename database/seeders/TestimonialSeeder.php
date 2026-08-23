<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'testimonial_key' => 'test-1',
                'name' => 'Budi Santoso',
                'role' => 'Chief Technology Officer (CTO)',
                'company' => 'Nexa Digital Corp',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80',
                'content' => 'Bagus adalah salah satu engineer terbaik yang pernah bekerja sama dengan saya. Kemampuannya mengubah kebutuhan bisnis yang rumit menjadi arsitektur web yang rapi, cepat, dan mudah dipelihara sangat luar biasa. Beliau juga memiliki etos kerja dan komunikasi yang sangat profesional.',
                'rating' => 5,
                'project_tag' => 'Lumina Analytics Platform',
            ],
            [
                'testimonial_key' => 'test-2',
                'name' => 'Sarah Jenkins',
                'role' => 'Head of Product Design',
                'company' => 'Nordic Retail Group',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&auto=format&fit=crop&q=80',
                'content' => 'Collaborating with Bagus was an absolute delight. He has a rare eye for visual detail and micro-interactions while maintaining impeccable code standards. The headless commerce storefront he engineered exceeded our performance targets by over 30%!',
                'rating' => 5,
                'project_tag' => 'Aurora Headless Commerce',
            ],
            [
                'testimonial_key' => 'test-3',
                'name' => 'Reza Mahendra',
                'role' => 'Founder & Managing Director',
                'company' => 'Karsa Venture Lab',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&auto=format&fit=crop&q=80',
                'content' => 'Proyek website kami selesai lebih cepat dari estimasi dengan kualitas yang melebihi ekspektasi. Bagus tidak hanya sekadar coding, tapi memberikan masukan strategis terkait UX dan SEO yang berdampak langsung pada pertumbuhan konversi klien kami.',
                'rating' => 5,
                'project_tag' => 'Corporate Web Architecture',
            ],
        ];

        foreach ($testimonials as $idx => $testimonial) {
            Testimonial::create($testimonial + ['sort_order' => $idx]);
        }
    }
}
