<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = [
            [
                'experience_key' => 'exp-1',
                'period' => '2023 — Sekarang',
                'role' => 'Lead Frontend / Full-Stack Engineer',
                'company' => 'Nexa Digital Technologies',
                'location' => 'Jakarta (Hybrid / Remote)',
                'type' => 'Full-Time',
                'description' => 'Memimpin tim engineering frontend (6 developer) dalam merancang arsitektur aplikasi SaaS multi-tenant, migrasi ke React 19, dan standardisasi design system.',
                'achievements' => [
                    'Memangkas waktu render halaman dashboard utama sebesar 58% melalui implementasi SSR & caching berjenjang.',
                    'Membangun shared UI component library yang digunakan oleh 3 anak perusahaan, menghemat ~300 jam kerja pengembang per kuartal.',
                    'Mentoring junior & mid-level developers dalam clean code architecture dan automated end-to-end testing.',
                ],
                'skills' => ['React 19', 'Next.js', 'TypeScript', 'Tailwind CSS', 'GraphQL', 'Docker', 'GCP'],
                'featured' => true,
            ],
            [
                'experience_key' => 'exp-2',
                'period' => '2021 — 2023',
                'role' => 'Senior Frontend Developer',
                'company' => 'Finova Global Financial',
                'location' => 'Singapura (Remote)',
                'type' => 'Full-Time',
                'description' => 'Mengembangkan antarmuka aplikasi perbankan digital dan payment gateway berkeamanan tinggi dengan standar kepatuhan regulasi finansial.',
                'achievements' => [
                    'Mengembangkan fitur multi-currency wallet dengan kalkulator kurs real-time dan zero-latency visual update.',
                    'Mencapai skor keamanan OWASP level A dan 100% skor kepatuhan WCAG 2.1 AA untuk aksesibilitas pengguna disabilitas.',
                    'Berkolaborasi erat dengan tim Product Manager dan UI/UX Designer untuk mempercepat siklus rilis sprint dua mingguan.',
                ],
                'skills' => ['React', 'TypeScript', 'Redux Toolkit', 'Zod', 'Jest / Vitest', 'Tailwind CSS'],
                'featured' => true,
            ],
            [
                'experience_key' => 'exp-3',
                'period' => '2019 — 2021',
                'role' => 'Full-Stack Web Developer',
                'company' => 'PixelCraft Studio',
                'location' => 'Bandung, Indonesia',
                'type' => 'Full-Time',
                'description' => 'Membangun puluhan website korporat, e-commerce kustom, dan portal web interaktif untuk berbagai klien brand terkemuka.',
                'achievements' => [
                    'Menyelesaikan 25+ proyek website klien dengan tingkat kepuasan di atas 98% dan tepat waktu sesuai deadline.',
                    'Mengintegrasikan sistem pembayaran lokal (Midtrans, Xendit) dan kurir otomatis (JNE, SiCepat API).',
                    'Menginisiasi penggunaan Git workflow dan CI/CD otomatis di lingkungan internal studio.',
                ],
                'skills' => ['JavaScript / TS', 'React', 'Node.js Express', 'PostgreSQL', 'Tailwind CSS', 'REST API'],
                'featured' => false,
            ],
        ];

        foreach ($experiences as $idx => $experience) {
            Experience::create($experience + ['sort_order' => $idx]);
        }
    }
}
