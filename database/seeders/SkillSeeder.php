<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Frontend
            [
                'name' => 'React 19 & Next.js 15',
                'category' => 'frontend',
                'level' => 96,
                'experience' => '6 tahun',
                'icon_name' => 'Code2',
                'highlight_text' => 'Server Components, Suspense, Parallel Routes, Optimistic UI',
            ],
            [
                'name' => 'TypeScript (Strict Mode)',
                'category' => 'frontend',
                'level' => 94,
                'experience' => '5 tahun',
                'icon_name' => 'FileCode',
                'highlight_text' => 'Generics, Conditional Types, Schema Validation (Zod)',
            ],
            [
                'name' => 'Tailwind CSS & Modern UI',
                'category' => 'frontend',
                'level' => 98,
                'experience' => '5 tahun',
                'icon_name' => 'Palette',
                'highlight_text' => 'Design tokens, Responsive grid, CSS variables & Motion FX',
            ],
            [
                'name' => 'Motion & Micro-interactions',
                'category' => 'frontend',
                'level' => 92,
                'experience' => '4 tahun',
                'icon_name' => 'Sparkles',
                'highlight_text' => 'Spring physics, Layout transitions, Gesture handling',
            ],
            [
                'name' => 'State Management',
                'category' => 'frontend',
                'level' => 93,
                'experience' => '5 tahun',
                'icon_name' => 'Layers',
                'highlight_text' => 'Zustand, TanStack Query, Redux Toolkit, Context',
            ],
            // Backend & Cloud
            [
                'name' => 'Node.js & Express / NestJS',
                'category' => 'backend',
                'level' => 88,
                'experience' => '4+ tahun',
                'icon_name' => 'Server',
                'highlight_text' => 'RESTful API, WebSocket, Microservices, Middleware',
            ],
            [
                'name' => 'PostgreSQL & Drizzle / Prisma',
                'category' => 'backend',
                'level' => 86,
                'experience' => '4 tahun',
                'icon_name' => 'Database',
                'highlight_text' => 'Complex queries, Indexing optimization, Type-safe ORMs',
            ],
            [
                'name' => 'GraphQL & Apollo / urql',
                'category' => 'backend',
                'level' => 84,
                'experience' => '3 tahun',
                'icon_name' => 'Cpu',
                'highlight_text' => 'Schema stitching, Codegen, Query caching strategies',
            ],
            [
                'name' => 'Cloud & Serverless (GCP / AWS)',
                'category' => 'devops',
                'level' => 82,
                'experience' => '3 tahun',
                'icon_name' => 'Cloud',
                'highlight_text' => 'Cloud Run, S3, Docker containers, Edge functions',
            ],
            [
                'name' => 'CI/CD & Testing',
                'category' => 'devops',
                'level' => 85,
                'experience' => '4 tahun',
                'icon_name' => 'GitBranch',
                'highlight_text' => 'GitHub Actions, Vitest, Playwright, Lighthouse CI',
            ],
            // Tools
            [
                'name' => 'Figma to Production Code',
                'category' => 'tools',
                'level' => 95,
                'experience' => '5 tahun',
                'icon_name' => 'Layout',
                'highlight_text' => 'Design tokens sync, Component library, Pixel-perfect',
            ],
            [
                'name' => 'Web Vitals & Performance',
                'category' => 'tools',
                'level' => 94,
                'experience' => '4 tahun',
                'icon_name' => 'Zap',
                'highlight_text' => 'LCP/CLS/INP optimization, Code-splitting, Image pipelines',
            ],
        ];

        foreach ($skills as $idx => $skill) {
            Skill::create($skill + ['sort_order' => $idx]);
        }
    }
}
