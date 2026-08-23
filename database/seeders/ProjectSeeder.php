<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'project_key' => 'lumina-saas',
                'title' => 'Lumina Analytics Platform',
                'tagline' => 'Enterprise B2B real-time metrics dashboard with sub-second data streaming',
                'description' => 'Platform analitik web modern dengan visualisasi data multi-dimensi, streaming data WebSocket, dan ekspor laporan terotomasi.',
                'long_description' => 'Lumina dirancang untuk membantu tim engineering memantau performa aplikasi web secara real-time. Dibangun dengan React 19, Recharts kustom, dan caching cerdas TanStack Query. Menghasilkan skor Lighthouse 99 pada desktop dan mobile.',
                'category' => 'Full-Stack',
                'role' => 'Lead Frontend Architect',
                'timeline' => '4 bulan (2025)',
                'client' => 'SaaS Global (Singapura)',
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1000&auto=format&fit=crop&q=80',
                'tags' => ['React 19', 'Next.js 15', 'TypeScript', 'Tailwind CSS', 'Recharts', 'PostgreSQL'],
                'metrics' => [
                    ['label' => 'Page Load Speed', 'value' => '0.6s'],
                    ['label' => 'Query Latency', 'value' => '-65%'],
                    ['label' => 'Daily Active Users', 'value' => '45,000+'],
                ],
                'highlights' => [
                    'Implementasi virtualized list untuk merender 50.000+ baris data tanpa lag.',
                    'Sistem dark/light theme otomatis tersinkronisasi dengan preferensi OS pengguna.',
                    'Optimasi Core Web Vitals dengan 0 Layout Shift (CLS: 0.00).',
                ],
                'tech_stack' => [
                    'frontend' => ['React 19', 'Next.js 15', 'TypeScript', 'Tailwind CSS', 'Motion'],
                    'backend' => ['Node.js', 'Express', 'Redis Pub/Sub', 'WebSocket Server'],
                    'database' => ['PostgreSQL', 'TimescaleDB'],
                    'cloudAndDevOps' => ['Docker', 'GCP Cloud Run', 'GitHub Actions'],
                ],
                'demo_url' => 'https://example.com/lumina-demo',
                'github_url' => 'https://github.com/bagusbatra/lumina-analytics',
                'featured' => true,
                'color_gradient' => 'from-blue-500/10 via-indigo-500/5 to-transparent',
                'accent_color' => '#3b82f6',
            ],
            [
                'project_key' => 'aurora-commerce',
                'title' => 'Aurora Headless Commerce',
                'tagline' => 'High-converting luxury e-commerce with lightning-fast instant checkout',
                'description' => 'E-commerce headless premium dengan arsitektur Jamstack, integrasi multi-gateway, dan animasi micro-interaction produk yang memikat.',
                'long_description' => 'Arsitektur e-commerce headless yang menghubungkan Shopify Storefront API dengan frontend React berkecepatan tinggi. Mendukung visualisasi produk interaktif 360°, filter pintar instan tanpa reload, dan checkout terintegrasi.',
                'category' => 'Frontend',
                'role' => 'Senior UI/Frontend Engineer',
                'timeline' => '3 bulan (2024)',
                'client' => 'Nordic Artisan Goods',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1000&auto=format&fit=crop&q=80',
                'tags' => ['React', 'TypeScript', 'Tailwind CSS', 'Shopify Storefront API', 'Motion'],
                'metrics' => [
                    ['label' => 'Conversion Rate', 'value' => '+34%'],
                    ['label' => 'Lighthouse Score', 'value' => '98/100'],
                    ['label' => 'Cart Abandonment', 'value' => '-22%'],
                ],
                'highlights' => [
                    'Desain antarmuka minimalis elegan dengan tipografi kontras tinggi dan layout asimetris.',
                    'Micro-interaction saat menambahkan produk ke keranjang dengan physics spring.',
                    'Dukungan multi-currency otomatis dan lokalisasi multi-bahasa.',
                ],
                'tech_stack' => [
                    'frontend' => ['React', 'TypeScript', 'Tailwind CSS', 'Framer Motion'],
                    'backend' => ['Shopify GraphQL API', 'Stripe Payments Webhook'],
                    'cloudAndDevOps' => ['Vercel Edge Network', 'Cloudflare CDN'],
                ],
                'demo_url' => 'https://example.com/aurora-demo',
                'github_url' => 'https://github.com/bagusbatra/aurora-commerce',
                'featured' => true,
                'color_gradient' => 'from-amber-500/10 via-rose-500/5 to-transparent',
                'accent_color' => '#f59e0b',
            ],
            [
                'project_key' => 'zenith-design-system',
                'title' => 'Zenith UI Design System',
                'tagline' => 'Accessible, tokenized component library & documentation portal for 40+ devs',
                'description' => 'Design system enterprise yang komprehensif dengan 60+ komponen accessible (WAI-ARIA), sinkronisasi token Figma, dan dokumentasi interaktif.',
                'long_description' => 'Zenith dibangun untuk menyatukan standar UI di 6 produk digital perusahaan. Dilengkapi dengan live preview playground, color contrast checker otomatis, keyboard navigation lengkap, dan dukungan multi-theme tanpa CSS bloating.',
                'category' => 'UI/UX & Systems',
                'role' => 'Design System Lead',
                'timeline' => '6 bulan (2024)',
                'client' => 'Fintech Enterprise Indonesia',
                'image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1000&auto=format&fit=crop&q=80',
                'tags' => ['Design System', 'TypeScript', 'Tailwind CSS', 'Storybook', 'Radix UI'],
                'metrics' => [
                    ['label' => 'Dev Velocity', 'value' => '+50%'],
                    ['label' => 'Accessibility', 'value' => 'WCAG AAA'],
                    ['label' => 'Components', 'value' => '64+'],
                ],
                'highlights' => [
                    '100% accessible dengan keyboard navigation dan screen reader tested.',
                    'Automated visual regression testing menggunakan Playwright.',
                    'Kompatibel penuh dengan Tailwind v4 dan utility tokens.',
                ],
                'tech_stack' => [
                    'frontend' => ['React', 'TypeScript', 'Radix UI Primitives', 'Tailwind CSS'],
                    'cloudAndDevOps' => ['Storybook', 'Chromatic', 'NPM Package Registry'],
                ],
                'demo_url' => 'https://example.com/zenith-system',
                'github_url' => 'https://github.com/bagusbatra/zenith-ui',
                'featured' => true,
                'color_gradient' => 'from-emerald-500/10 via-teal-500/5 to-transparent',
                'accent_color' => '#10b981',
            ],
            [
                'project_key' => 'pulse-ai-workspace',
                'title' => 'Pulse AI Collaborative Canvas',
                'tagline' => 'Real-time collaborative markdown & visual workflow canvas for product teams',
                'description' => 'Aplikasi kolaborasi dokumen cerdas dengan asisten AI, integrasi kanban real-time, dan export ke berbagai format dokumen.',
                'long_description' => 'Workspace kolaboratif yang menggabungkan kemudahan menulis markdown dengan bantuan AI context-aware. Memungkinkan tim merancang user stories, diagram alur sistem, dan dokumentasi teknis secara bersamaan.',
                'category' => 'AI & Tools',
                'role' => 'Full-Stack Engineer',
                'timeline' => '3 bulan (2025)',
                'client' => null,
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1000&auto=format&fit=crop&q=80',
                'tags' => ['React', 'TypeScript', 'Gemini AI API', 'Node.js', 'Tailwind CSS'],
                'metrics' => [
                    ['label' => 'Latency', 'value' => '< 40ms'],
                    ['label' => 'AI Gen Speed', 'value' => '80 w/s'],
                    ['label' => 'Export Quality', 'value' => 'Vector PDF'],
                ],
                'highlights' => [
                    'Streaming teks respon AI secara mulus dengan markdown syntax highlighting instan.',
                    'Fitur offline-first dengan sinkronisasi otomatis saat koneksi kembali stabil.',
                    'Shortcuts keyboard intuitif untuk produktivitas maksimal.',
                ],
                'tech_stack' => [
                    'frontend' => ['React 19', 'TypeScript', 'Tailwind CSS', 'Motion'],
                    'backend' => ['Node.js Express', 'Google GenAI SDK', 'WebSocket'],
                    'cloudAndDevOps' => ['Cloud Run', 'GCP Storage'],
                ],
                'demo_url' => 'https://example.com/pulse-ai',
                'github_url' => 'https://github.com/bagusbatra/pulse-ai',
                'featured' => false,
                'color_gradient' => 'from-purple-500/10 via-pink-500/5 to-transparent',
                'accent_color' => '#8b5cf6',
            ],
            [
                'project_key' => 'fast-state-npm',
                'title' => 'FastState — Lightweight Reactive Store',
                'tagline' => 'Zero-dependency 800-byte state management library for modern React apps',
                'description' => 'Library manajemen state open-source ultra-ringan dengan proxy reactivity, selector memoization otomatis, dan TypeScript inference sempurna.',
                'long_description' => 'Dibuat untuk mengatasi overhead boilerplate pada library state konvensional. FastState telah diunduh lebih dari 120.000 kali di NPM dengan rating 5-bintang dari komunitas pengembang global.',
                'category' => 'Open Source',
                'role' => 'Author & Maintainer',
                'timeline' => 'Ongoing (2023 - Sekarang)',
                'client' => null,
                'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1000&auto=format&fit=crop&q=80',
                'tags' => ['TypeScript', 'Open Source', 'NPM', 'React Hooks', 'Vitest'],
                'metrics' => [
                    ['label' => 'NPM Downloads', 'value' => '120k+'],
                    ['label' => 'Bundle Size', 'value' => '840 bytes'],
                    ['label' => 'GitHub Stars', 'value' => '1.4k ⭐'],
                ],
                'highlights' => [
                    'Zero dependencies dengan 100% test coverage menggunakan Vitest.',
                    'Automatic selective re-rendering tanpa manual selector comparison.',
                    'Dukungan penuh untuk Server Components & React Native.',
                ],
                'tech_stack' => [
                    'frontend' => ['TypeScript', 'Rollup / tsup', 'Vitest', 'Microbundle'],
                    'cloudAndDevOps' => ['NPM Registry', 'GitHub Actions CI'],
                ],
                'demo_url' => 'https://faststate.dev',
                'github_url' => 'https://github.com/bagusbatra/faststate',
                'featured' => false,
                'color_gradient' => 'from-cyan-500/10 via-sky-500/5 to-transparent',
                'accent_color' => '#06b6d4',
            ],
        ];

        foreach ($projects as $idx => $project) {
            Project::create($project + ['sort_order' => $idx]);
        }
    }
}
