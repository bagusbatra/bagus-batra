<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $avatar = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80';

        $post1Code2 = <<<'CODE'
        // ✅ Server Component: Fetch langsung tanpa overhead useEffect di client
        import { fetchRevenueMetrics } from '@/features/dashboard/api';
        import { MetricsChartClient } from './MetricsChartClient';

        export async function MetricsWidget({ period }: { period: string }) {
          const metrics = await fetchRevenueMetrics(period);

          return (
            <section className="p-6 bg-white rounded-2xl border border-slate-200">
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-bold text-slate-800">Ringkasan Pendapatan</h2>
                <span className="text-xs px-2.5 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full">
                  +18.4% bulan ini
                </span>
              </div>
              {/* Komponen interaktif hanya memegang representasi grafik */}
              <MetricsChartClient data={metrics} />
            </section>
          );
        }
        CODE;

        $post1Code3 = <<<'CODE'
        import { z } from 'zod';

        export const UserProfileSchema = z.object({
          id: z.string().uuid(),
          displayName: z.string().min(2, 'Nama minimal 2 karakter'),
          email: z.string().email(),
          role: z.enum(['admin', 'editor', 'viewer']),
          preferences: z.object({
            theme: z.enum(['light', 'dark', 'system']).default('light'),
            notifications: z.boolean().default(true),
          }),
        });

        export type UserProfile = z.infer<typeof UserProfileSchema>;
        CODE;

        $post2Code1 = <<<'CODE'
        @font-face {
          font-family: 'Outfit';
          src: url('/fonts/outfit-subset.woff2') format('woff2');
          font-weight: 400 700;
          font-display: swap;
          unicode-range: U+0000-00FF, U+0131, U+0152-0153;
        }
        CODE;

        $post2Code2 = <<<'CODE'
        import dynamic from 'next/dynamic';

        // Modal interaktif di-load hanya saat pengunjung menekan tombol pemicu
        const AnalyticsModal = dynamic(
          () => import('./AnalyticsModal').then((mod) => mod.AnalyticsModal),
          { ssr: false }
        );
        CODE;

        $post3Code1 = <<<'CODE'
        import { motion } from 'motion/react';

        export function TactileCard({ children }: { children: React.ReactNode }) {
          return (
            <motion.div
              whileHover={{ y: -4, scale: 1.01 }}
              whileTap={{ scale: 0.98 }}
              transition={{ type: 'spring', stiffness: 400, damping: 25 }}
              className="p-6 bg-white/80 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-sm"
            >
              {children}
            </motion.div>
          );
        }
        CODE;

        $post4Code1 = <<<'CODE'
        interface ApiResponse<TData> {
          data: TData;
          status: number;
          message?: string;
        }

        export async function fetchApi<T>(
          endpoint: string,
          options?: RequestInit
        ): Promise<ApiResponse<T>> {
          const response = await fetch(endpoint, {
            headers: { 'Content-Type': 'application/json' },
            ...options,
          });

          if (!response.ok) {
            throw new Error(`API error: ${response.statusText}`);
          }

          const data: T = await response.json();
          return { data, status: response.status };
        }
        CODE;

        $posts = [
            [
                'post_key' => 'post-1',
                'title' => 'Membangun Arsitektur React 19 yang Scalable & Maintainable untuk Tim Enterprise',
                'slug' => 'react-19-enterprise-architecture',
                'summary' => 'Panduan komprehensif mengenai pemanfaatan Server Actions, Suspense boundary bertingkat, dan manajemen state modular pada aplikasi skala besar.',
                'category' => 'Frontend Architecture',
                'tags' => ['React 19', 'TypeScript', 'Clean Code', 'Enterprise'],
                'read_time' => '6 menit baca',
                'published_at' => '28 Jan 2026',
                'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80',
                'author_name' => 'Bagus Batra',
                'author_role' => 'Senior Web Developer',
                'author_avatar' => $avatar,
                'likes' => 142,
                'views' => '3.4k',
                'sections' => [
                    [
                        'heading' => '1. Mengapa Perlu Repensasi Struktur Modular?',
                        'body' => 'Seiring pertumbuhan basis kode dari 10.000 baris menjadi ratusan ribu baris, struktur folder berbasis tipe (seperti /components, /hooks, /services) cenderung membuat developer sering berganti-ganti folder untuk satu fitur kecil. Pendekatan Feature-Sliced atau Domain-Driven Frontend mempermudah pemeliharaan jangka panjang.',
                    ],
                    [
                        'heading' => '2. Pola Pemisahan Server & Client Components',
                        'body' => 'Aturan praktis yang kami terapkan: biarkan fetching data dan komputasi berat berada di Server Component (RSC), dan turunkan data yang telah siap ke Client Component tipis yang hanya mengurus interaktivitas.',
                        'codeSnippet' => [
                            'language' => 'tsx',
                            'filename' => 'src/features/dashboard/components/MetricsWidget.tsx',
                            'code' => $post1Code2,
                        ],
                        'tip' => 'Tips: Hindari membungkus seluruh halaman dengan "use client". Gunakan pembatas sekecil mungkin pada elemen yang membutuhkan event listener atau state internal.',
                    ],
                    [
                        'heading' => '3. Standarisasi Schema Validation dengan Zod',
                        'body' => 'Keamanan tipe data di compile-time saja tidak cukup ketika berhadapan dengan data API eksternal. Runtime schema validation memastikan struktur data tetap konsisten dan mencegah runtime null pointer exception.',
                        'codeSnippet' => [
                            'language' => 'ts',
                            'filename' => 'src/schemas/userProfile.ts',
                            'code' => $post1Code3,
                        ],
                    ],
                ],
            ],
            [
                'post_key' => 'post-2',
                'title' => 'Optimasi Web Vitals: Cara Kami Mempercepat Waktu Muat Website dari 3.8s ke 0.7s',
                'slug' => 'optimasi-web-vitals-lighthouse-99',
                'summary' => 'Studi kasus nyata mengikis beban JavaScript, optimasi Largest Contentful Paint (LCP), eliminasi Cumulative Layout Shift (CLS), dan strategi font caching.',
                'category' => 'Performance',
                'tags' => ['Web Vitals', 'Performance', 'Lighthouse', 'Next.js'],
                'read_time' => '8 menit baca',
                'published_at' => '15 Jan 2026',
                'cover_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80',
                'author_name' => 'Bagus Batra',
                'author_role' => 'Senior Web Developer',
                'author_avatar' => $avatar,
                'likes' => 218,
                'views' => '5.1k',
                'sections' => [
                    [
                        'heading' => 'Diagnosa Awal Masalah Performa',
                        'body' => 'Banyak website modern mengalami penurunan performa bukan karena framework yang buruk, melainkan karena third-party scripts yang tidak terkontrol, ukuran gambar tanpa optimasi, dan font loading blocking render.',
                    ],
                    [
                        'heading' => 'Langkah 1: Font Display Swap & Subsetting',
                        'body' => 'Menggunakan format WOFF2 modern dan memuat subset karakter latin penting terlebih dahulu dapat memangkas waktu tunggu FCP secara signifikan.',
                        'codeSnippet' => [
                            'language' => 'css',
                            'filename' => 'src/styles/fonts.css',
                            'code' => $post2Code1,
                        ],
                    ],
                    [
                        'heading' => 'Langkah 2: Lazy Load Non-Critical Components',
                        'body' => 'Gunakan dynamic import untuk modal, visualizer berat, atau chart yang berada di bawah fold viewport (below the fold).',
                        'codeSnippet' => [
                            'language' => 'tsx',
                            'filename' => 'src/components/HeavyDashboard.tsx',
                            'code' => $post2Code2,
                        ],
                        'tip' => 'Hasil: Bundle awal berkurang sebesar 42% (dari 320kb ke 185kb gzipped).',
                    ],
                ],
            ],
            [
                'post_key' => 'post-3',
                'title' => 'Desain Sistem & Micro-Interactions: Menghadirkan Pengalaman Web yang Hidup Tanpa Kaku',
                'slug' => 'modern-ui-micro-interactions',
                'summary' => 'Bagaimana memadukan prinsip fisika spring, kontras tipografi elegan, dan tactile feedback untuk membuat website terasa responsif dan menyenangkan.',
                'category' => 'Clean Code',
                'tags' => ['UI/UX', 'CSS & Motion', 'Design System', 'Accessibility'],
                'read_time' => '5 menit baca',
                'published_at' => '03 Jan 2026',
                'cover_image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80',
                'author_name' => 'Bagus Batra',
                'author_role' => 'Senior Web Developer',
                'author_avatar' => $avatar,
                'likes' => 189,
                'views' => '4.2k',
                'sections' => [
                    [
                        'heading' => 'Prinsip "Subtle Motion": Lebih Sedikit, Lebih Bermakna',
                        'body' => 'Animasi yang baik tidak mencolok atau memakan waktu pengguna. Tujuannya adalah memberikan konfirmasi visual bahwa interaksi berhasil dilakukan, seperti penekanan tombol atau transisi state.',
                    ],
                    [
                        'heading' => 'Implementasi Spring Physics dengan Motion',
                        'body' => 'Alih-alih menggunakan timing cubic-bezier statis, gunakan parameter spring (stiffness, damping) untuk memberikan bobot alami pada elemen UI.',
                        'codeSnippet' => [
                            'language' => 'tsx',
                            'filename' => 'src/components/InteractiveCard.tsx',
                            'code' => $post3Code1,
                        ],
                        'tip' => 'Selalu hormati preferensi pengguna dengan menyertakan media query "prefers-reduced-motion".',
                    ],
                ],
            ],
            [
                'post_key' => 'post-4',
                'title' => 'Panduan Lengkap TypeScript Generics untuk Web Developer: Dari Dasar Hingga Utility Patterns',
                'slug' => 'typescript-generics-masterclass',
                'summary' => 'Kupas tuntas generic functions, conditional types, mapped types, dan bagaimana membangun API client dengan autocomplete presisi tinggi.',
                'category' => 'Full-Stack',
                'tags' => ['TypeScript', 'Best Practices', 'API Design', 'Backend'],
                'read_time' => '7 menit baca',
                'published_at' => '18 Des 2025',
                'cover_image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format&fit=crop&q=80',
                'author_name' => 'Bagus Batra',
                'author_role' => 'Senior Web Developer',
                'author_avatar' => $avatar,
                'likes' => 275,
                'views' => '6.8k',
                'sections' => [
                    [
                        'heading' => 'Generic Functions dalam HTTP Client',
                        'body' => 'Membangun wrapper fetch yang otomatis memvalidasi response body dan menyuntikkan tipe data kembalian secara presisi.',
                        'codeSnippet' => [
                            'language' => 'ts',
                            'filename' => 'src/lib/apiClient.ts',
                            'code' => $post4Code1,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($posts as $idx => $post) {
            BlogPost::create($post + ['sort_order' => $idx]);
        }
    }
}
