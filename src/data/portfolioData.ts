import { Project, BlogPost, Experience, SkillItem, Testimonial, SocialLink } from '../types';

export const PERSONAL_INFO = {
  name: 'Bagus Batra',
  nickname: 'Bagus',
  titleId: 'Senior Web Developer & Frontend Specialist',
  titleEn: 'Senior Full-Stack & Frontend Architect',
  taglineId: 'Membangun aplikasi web berkinerja tinggi, antarmuka elegan, dan arsitektur kode yang tangguh.',
  taglineEn: 'Crafting high-performance web applications, intuitive interfaces, and scalable front-end systems.',
  bioId: 'Halo! Saya seorang Software Engineer dengan pengalaman 6+ tahun dalam ekosistem JavaScript & TypeScript modern (React, Next.js, Node.js, Tailwind CSS). Saya memiliki antusiasme mendalam pada performa web, desain sistem yang modular, dan micro-interactions yang mulus tanpa mengorbankan aksesibilitas.',
  bioEn: "Hi! I'm a software engineer with 6+ years of experience crafting fast, accessible, and delightful web products. I specialize in the modern TypeScript ecosystem (React, Next.js, Tailwind, Node.js, GraphQL) with a relentless focus on runtime performance and UI craftsmanship.",
  location: 'Jakarta & Bali, Indonesia (Available Remote Worldwide)',
  email: 'bagusbatr@gmail.com',
  phone: '+62 812-3456-7890',
  github: 'https://github.com/bagusbatra',
  linkedin: 'https://linkedin.com/in/bagusbatra',
  twitter: 'https://twitter.com/bagusbatra',
  availableForWork: true,
  yearsOfExp: '6+',
  completedProjects: '45+',
  clientSatisfaction: '99.4%',
  openSourceContributions: '320+',
  avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80',
  secondaryAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&auto=format&fit=crop&q=80',
};

export const SOCIAL_LINKS: SocialLink[] = [
  {
    platform: 'GitHub',
    name: 'GitHub',
    url: 'https://github.com',
    username: '@bagusbatra',
    icon: 'Github',
    bgColor: 'hover:bg-slate-900 hover:text-white',
    textColor: 'text-slate-800',
    description: '30+ repositori publik & kontribusi open-source'
  },
  {
    platform: 'LinkedIn',
    name: 'LinkedIn',
    url: 'https://linkedin.com',
    username: 'Bagus Batra',
    icon: 'Linkedin',
    bgColor: 'hover:bg-[#0A66C2] hover:text-white',
    textColor: 'text-[#0A66C2]',
    description: 'Koneksi profesional, artikel karier & rekomendasi'
  },
  {
    platform: 'Twitter',
    name: 'X (Twitter)',
    url: 'https://twitter.com',
    username: '@bagusbatra',
    icon: 'Twitter',
    bgColor: 'hover:bg-black hover:text-white',
    textColor: 'text-slate-900',
    description: 'Berbagi tips kilat seputar React, CSS & UI engineering'
  },
  {
    platform: 'YouTube',
    name: 'YouTube',
    url: 'https://youtube.com',
    username: 'Bagus Dev Journey',
    icon: 'Youtube',
    bgColor: 'hover:bg-[#FF0000] hover:text-white',
    textColor: 'text-[#FF0000]',
    description: 'Tutorial arsitektur web & live-coding showcase'
  },
  {
    platform: 'Instagram',
    name: 'Instagram',
    url: 'https://instagram.com',
    username: '@bagusbatra',
    icon: 'Instagram',
    bgColor: 'hover:bg-[#E1306C] hover:text-white',
    textColor: 'text-[#E1306C]',
    description: 'Behind-the-scenes workflow, remote setup & tech gear'
  },
  {
    platform: 'Email',
    name: 'Direct Email',
    url: 'mailto:bagusbatr@gmail.com',
    username: 'bagusbatr@gmail.com',
    icon: 'Mail',
    bgColor: 'hover:bg-indigo-600 hover:text-white',
    textColor: 'text-indigo-600',
    description: 'Respon cepat dalam kurun waktu < 24 jam'
  }
];

export const SKILLS_DATA: SkillItem[] = [
  // Frontend
  {
    name: 'React 19 & Next.js 15',
    category: 'frontend',
    level: 96,
    experience: '6 tahun',
    iconName: 'Code2',
    highlightText: 'Server Components, Suspense, Parallel Routes, Optimistic UI'
  },
  {
    name: 'TypeScript (Strict Mode)',
    category: 'frontend',
    level: 94,
    experience: '5 tahun',
    iconName: 'FileCode',
    highlightText: 'Generics, Conditional Types, Schema Validation (Zod)'
  },
  {
    name: 'Tailwind CSS & Modern UI',
    category: 'frontend',
    level: 98,
    experience: '5 tahun',
    iconName: 'Palette',
    highlightText: 'Design tokens, Responsive grid, CSS variables & Motion FX'
  },
  {
    name: 'Motion & Micro-interactions',
    category: 'frontend',
    level: 92,
    experience: '4 tahun',
    iconName: 'Sparkles',
    highlightText: 'Spring physics, Layout transitions, Gesture handling'
  },
  {
    name: 'State Management',
    category: 'frontend',
    level: 93,
    experience: '5 tahun',
    iconName: 'Layers',
    highlightText: 'Zustand, TanStack Query, Redux Toolkit, Context'
  },
  // Backend & Cloud
  {
    name: 'Node.js & Express / NestJS',
    category: 'backend',
    level: 88,
    experience: '4+ tahun',
    iconName: 'Server',
    highlightText: 'RESTful API, WebSocket, Microservices, Middleware'
  },
  {
    name: 'PostgreSQL & Drizzle / Prisma',
    category: 'backend',
    level: 86,
    experience: '4 tahun',
    iconName: 'Database',
    highlightText: 'Complex queries, Indexing optimization, Type-safe ORMs'
  },
  {
    name: 'GraphQL & Apollo / urql',
    category: 'backend',
    level: 84,
    experience: '3 tahun',
    iconName: 'Cpu',
    highlightText: 'Schema stitching, Codegen, Query caching strategies'
  },
  {
    name: 'Cloud & Serverless (GCP / AWS)',
    category: 'devops',
    level: 82,
    experience: '3 tahun',
    iconName: 'Cloud',
    highlightText: 'Cloud Run, S3, Docker containers, Edge functions'
  },
  {
    name: 'CI/CD & Testing',
    category: 'devops',
    level: 85,
    experience: '4 tahun',
    iconName: 'GitBranch',
    highlightText: 'GitHub Actions, Vitest, Playwright, Lighthouse CI'
  },
  // Tools
  {
    name: 'Figma to Production Code',
    category: 'tools',
    level: 95,
    experience: '5 tahun',
    iconName: 'Layout',
    highlightText: 'Design tokens sync, Component library, Pixel-perfect'
  },
  {
    name: 'Web Vitals & Performance',
    category: 'tools',
    level: 94,
    experience: '4 tahun',
    iconName: 'Zap',
    highlightText: 'LCP/CLS/INP optimization, Code-splitting, Image pipelines'
  }
];

export const PROJECTS_DATA: Project[] = [
  {
    id: 'lumina-saas',
    title: 'Lumina Analytics Platform',
    tagline: 'Enterprise B2B real-time metrics dashboard with sub-second data streaming',
    description: 'Platform analitik web modern dengan visualisasi data multi-dimensi, streaming data WebSocket, dan ekspor laporan terotomasi.',
    longDescription: 'Lumina dirancang untuk membantu tim engineering memantau performa aplikasi web secara real-time. Dibangun dengan React 19, Recharts kustom, dan caching cerdas TanStack Query. Menghasilkan skor Lighthouse 99 pada desktop dan mobile.',
    category: 'Full-Stack',
    role: 'Lead Frontend Architect',
    timeline: '4 bulan (2025)',
    client: 'SaaS Global (Singapura)',
    image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1000&auto=format&fit=crop&q=80',
    tags: ['React 19', 'Next.js 15', 'TypeScript', 'Tailwind CSS', 'Recharts', 'PostgreSQL'],
    metrics: [
      { label: 'Page Load Speed', value: '0.6s' },
      { label: 'Query Latency', value: '-65%' },
      { label: 'Daily Active Users', value: '45,000+' }
    ],
    highlights: [
      'Implementasi virtualized list untuk merender 50.000+ baris data tanpa lag.',
      'Sistem dark/light theme otomatis tersinkronisasi dengan preferensi OS pengguna.',
      'Optimasi Core Web Vitals dengan 0 Layout Shift (CLS: 0.00).'
    ],
    techStack: {
      frontend: ['React 19', 'Next.js 15', 'TypeScript', 'Tailwind CSS', 'Motion'],
      backend: ['Node.js', 'Express', 'Redis Pub/Sub', 'WebSocket Server'],
      database: ['PostgreSQL', 'TimescaleDB'],
      cloudAndDevOps: ['Docker', 'GCP Cloud Run', 'GitHub Actions']
    },
    demoUrl: 'https://example.com/lumina-demo',
    githubUrl: 'https://github.com/bagusbatra/lumina-analytics',
    featured: true,
    colorGradient: 'from-blue-500/10 via-indigo-500/5 to-transparent',
    accentColor: '#3b82f6'
  },
  {
    id: 'aurora-commerce',
    title: 'Aurora Headless Commerce',
    tagline: 'High-converting luxury e-commerce with lightning-fast instant checkout',
    description: 'E-commerce headless premium dengan arsitektur Jamstack, integrasi multi-gateway, dan animasi micro-interaction produk yang memikat.',
    longDescription: 'Arsitektur e-commerce headless yang menghubungkan Shopify Storefront API dengan frontend React berkecepatan tinggi. Mendukung visualisasi produk interaktif 360°, filter pintar instan tanpa reload, dan checkout terintegrasi.',
    category: 'Frontend',
    role: 'Senior UI/Frontend Engineer',
    timeline: '3 bulan (2024)',
    client: 'Nordic Artisan Goods',
    image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1000&auto=format&fit=crop&q=80',
    tags: ['React', 'TypeScript', 'Tailwind CSS', 'Shopify Storefront API', 'Motion'],
    metrics: [
      { label: 'Conversion Rate', value: '+34%' },
      { label: 'Lighthouse Score', value: '98/100' },
      { label: 'Cart Abandonment', value: '-22%' }
    ],
    highlights: [
      'Desain antarmuka minimalis elegan dengan tipografi kontras tinggi dan layout asimetris.',
      'Micro-interaction saat menambahkan produk ke keranjang dengan physics spring.',
      'Dukungan multi-currency otomatis dan lokalisasi multi-bahasa.'
    ],
    techStack: {
      frontend: ['React', 'TypeScript', 'Tailwind CSS', 'Framer Motion'],
      backend: ['Shopify GraphQL API', 'Stripe Payments Webhook'],
      cloudAndDevOps: ['Vercel Edge Network', 'Cloudflare CDN']
    },
    demoUrl: 'https://example.com/aurora-demo',
    githubUrl: 'https://github.com/bagusbatra/aurora-commerce',
    featured: true,
    colorGradient: 'from-amber-500/10 via-rose-500/5 to-transparent',
    accentColor: '#f59e0b'
  },
  {
    id: 'zenith-design-system',
    title: 'Zenith UI Design System',
    tagline: 'Accessible, tokenized component library & documentation portal for 40+ devs',
    description: 'Design system enterprise yang komprehensif dengan 60+ komponen accessible (WAI-ARIA), sinkronisasi token Figma, dan dokumentasi interaktif.',
    longDescription: 'Zenith dibangun untuk menyatukan standar UI di 6 produk digital perusahaan. Dilengkapi dengan live preview playground, color contrast checker otomatis, keyboard navigation lengkap, dan dukungan multi-theme tanpa CSS bloating.',
    category: 'UI/UX & Systems',
    role: 'Design System Lead',
    timeline: '6 bulan (2024)',
    client: 'Fintech Enterprise Indonesia',
    image: 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1000&auto=format&fit=crop&q=80',
    tags: ['Design System', 'TypeScript', 'Tailwind CSS', 'Storybook', 'Radix UI'],
    metrics: [
      { label: 'Dev Velocity', value: '+50%' },
      { label: 'Accessibility', value: 'WCAG AAA' },
      { label: 'Components', value: '64+' }
    ],
    highlights: [
      '100% accessible dengan keyboard navigation dan screen reader tested.',
      'Automated visual regression testing menggunakan Playwright.',
      'Kompatibel penuh dengan Tailwind v4 dan utility tokens.'
    ],
    techStack: {
      frontend: ['React', 'TypeScript', 'Radix UI Primitives', 'Tailwind CSS'],
      cloudAndDevOps: ['Storybook', 'Chromatic', 'NPM Package Registry']
    },
    demoUrl: 'https://example.com/zenith-system',
    githubUrl: 'https://github.com/bagusbatra/zenith-ui',
    featured: true,
    colorGradient: 'from-emerald-500/10 via-teal-500/5 to-transparent',
    accentColor: '#10b981'
  },
  {
    id: 'pulse-ai-workspace',
    title: 'Pulse AI Collaborative Canvas',
    tagline: 'Real-time collaborative markdown & visual workflow canvas for product teams',
    description: 'Aplikasi kolaborasi dokumen cerdas dengan asisten AI, integrasi kanban real-time, dan export ke berbagai format dokumen.',
    longDescription: 'Workspace kolaboratif yang menggabungkan kemudahan menulis markdown dengan bantuan AI context-aware. Memungkinkan tim merancang user stories, diagram alur sistem, dan dokumentasi teknis secara bersamaan.',
    category: 'AI & Tools',
    role: 'Full-Stack Engineer',
    timeline: '3 bulan (2025)',
    image: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1000&auto=format&fit=crop&q=80',
    tags: ['React', 'TypeScript', 'Gemini AI API', 'Node.js', 'Tailwind CSS'],
    metrics: [
      { label: 'Latency', value: '< 40ms' },
      { label: 'AI Gen Speed', value: '80 w/s' },
      { label: 'Export Quality', value: 'Vector PDF' }
    ],
    highlights: [
      'Streaming teks respon AI secara mulus dengan markdown syntax highlighting instan.',
      'Fitur offline-first dengan sinkronisasi otomatis saat koneksi kembali stabil.',
      'Shortcuts keyboard intuitif untuk produktivitas maksimal.'
    ],
    techStack: {
      frontend: ['React 19', 'TypeScript', 'Tailwind CSS', 'Motion'],
      backend: ['Node.js Express', 'Google GenAI SDK', 'WebSocket'],
      cloudAndDevOps: ['Cloud Run', 'GCP Storage']
    },
    demoUrl: 'https://example.com/pulse-ai',
    githubUrl: 'https://github.com/bagusbatra/pulse-ai',
    featured: false,
    colorGradient: 'from-purple-500/10 via-pink-500/5 to-transparent',
    accentColor: '#8b5cf6'
  },
  {
    id: 'fast-state-npm',
    title: 'FastState — Lightweight Reactive Store',
    tagline: 'Zero-dependency 800-byte state management library for modern React apps',
    description: 'Library manajemen state open-source ultra-ringan dengan proxy reactivity, selector memoization otomatis, dan TypeScript inference sempurna.',
    longDescription: 'Dibuat untuk mengatasi overhead boilerplate pada library state konvensional. FastState telah diunduh lebih dari 120.000 kali di NPM dengan rating 5-bintang dari komunitas pengembang global.',
    category: 'Open Source',
    role: 'Author & Maintainer',
    timeline: 'Ongoing (2023 - Sekarang)',
    image: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1000&auto=format&fit=crop&q=80',
    tags: ['TypeScript', 'Open Source', 'NPM', 'React Hooks', 'Vitest'],
    metrics: [
      { label: 'NPM Downloads', value: '120k+' },
      { label: 'Bundle Size', value: '840 bytes' },
      { label: 'GitHub Stars', value: '1.4k ⭐' }
    ],
    highlights: [
      'Zero dependencies dengan 100% test coverage menggunakan Vitest.',
      'Automatic selective re-rendering tanpa manual selector comparison.',
      'Dukungan penuh untuk Server Components & React Native.'
    ],
    techStack: {
      frontend: ['TypeScript', 'Rollup / tsup', 'Vitest', 'Microbundle'],
      cloudAndDevOps: ['NPM Registry', 'GitHub Actions CI']
    },
    demoUrl: 'https://faststate.dev',
    githubUrl: 'https://github.com/bagusbatra/faststate',
    featured: false,
    colorGradient: 'from-cyan-500/10 via-sky-500/5 to-transparent',
    accentColor: '#06b6d4'
  }
];

export const BLOG_POSTS: BlogPost[] = [
  {
    id: 'post-1',
    title: 'Membangun Arsitektur React 19 yang Scalable & Maintainable untuk Tim Enterprise',
    slug: 'react-19-enterprise-architecture',
    summary: 'Panduan komprehensif mengenai pemanfaatan Server Actions, Suspense boundary bertingkat, dan manajemen state modular pada aplikasi skala besar.',
    category: 'Frontend Architecture',
    tags: ['React 19', 'TypeScript', 'Clean Code', 'Enterprise'],
    readTime: '6 menit baca',
    publishedAt: '28 Jan 2026',
    coverImage: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80',
    author: {
      name: 'Bagus Batra',
      role: 'Senior Web Developer',
      avatar: PERSONAL_INFO.avatar
    },
    likes: 142,
    views: '3.4k',
    sections: [
      {
        heading: '1. Mengapa Perlu Repensasi Struktur Modular?',
        body: 'Seiring pertumbuhan basis kode dari 10.000 baris menjadi ratusan ribu baris, struktur folder berbasis tipe (seperti /components, /hooks, /services) cenderung membuat developer sering berganti-ganti folder untuk satu fitur kecil. Pendekatan Feature-Sliced atau Domain-Driven Frontend mempermudah pemeliharaan jangka panjang.'
      },
      {
        heading: '2. Pola Pemisahan Server & Client Components',
        body: 'Aturan praktis yang kami terapkan: biarkan fetching data dan komputasi berat berada di Server Component (RSC), dan turunkan data yang telah siap ke Client Component tipis yang hanya mengurus interaktivitas.',
        codeSnippet: {
          language: 'tsx',
          filename: 'src/features/dashboard/components/MetricsWidget.tsx',
          code: `// ✅ Server Component: Fetch langsung tanpa overhead useEffect di client
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
}`
        },
        tip: 'Tips: Hindari membungkus seluruh halaman dengan "use client". Gunakan pembatas sekecil mungkin pada elemen yang membutuhkan event listener atau state internal.'
      },
      {
        heading: '3. Standarisasi Schema Validation dengan Zod',
        body: 'Keamanan tipe data di compile-time saja tidak cukup ketika berhadapan dengan data API eksternal. Runtime schema validation memastikan struktur data tetap konsisten dan mencegah runtime null pointer exception.',
        codeSnippet: {
          language: 'ts',
          filename: 'src/schemas/userProfile.ts',
          code: `import { z } from 'zod';

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

export type UserProfile = z.infer<typeof UserProfileSchema>;`
        }
      }
    ]
  },
  {
    id: 'post-2',
    title: 'Optimasi Web Vitals: Cara Kami Mempercepat Waktu Muat Website dari 3.8s ke 0.7s',
    slug: 'optimasi-web-vitals-lighthouse-99',
    summary: 'Studi kasus nyata mengikis beban JavaScript, optimasi Largest Contentful Paint (LCP), eliminasi Cumulative Layout Shift (CLS), dan strategi font caching.',
    category: 'Performance',
    tags: ['Web Vitals', 'Performance', 'Lighthouse', 'Next.js'],
    readTime: '8 menit baca',
    publishedAt: '15 Jan 2026',
    coverImage: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80',
    author: {
      name: 'Bagus Batra',
      role: 'Senior Web Developer',
      avatar: PERSONAL_INFO.avatar
    },
    likes: 218,
    views: '5.1k',
    sections: [
      {
        heading: 'Diagnosa Awal Masalah Performa',
        body: 'Banyak website modern mengalami penurunan performa bukan karena framework yang buruk, melainkan karena third-party scripts yang tidak terkontrol, ukuran gambar tanpa optimasi, dan font loading blocking render.'
      },
      {
        heading: 'Langkah 1: Font Display Swap & Subsetting',
        body: 'Menggunakan format WOFF2 modern dan memuat subset karakter latin penting terlebih dahulu dapat memangkas waktu tunggu FCP secara signifikan.',
        codeSnippet: {
          language: 'css',
          filename: 'src/styles/fonts.css',
          code: `@font-face {
  font-family: 'Outfit';
  src: url('/fonts/outfit-subset.woff2') format('woff2');
  font-weight: 400 700;
  font-display: swap;
  unicode-range: U+0000-00FF, U+0131, U+0152-0153;
}`
        }
      },
      {
        heading: 'Langkah 2: Lazy Load Non-Critical Components',
        body: 'Gunakan dynamic import untuk modal, visualizer berat, atau chart yang berada di bawah fold viewport (below the fold).',
        codeSnippet: {
          language: 'tsx',
          filename: 'src/components/HeavyDashboard.tsx',
          code: `import dynamic from 'next/dynamic';

// Modal interaktif di-load hanya saat pengunjung menekan tombol pemicu
const AnalyticsModal = dynamic(
  () => import('./AnalyticsModal').then((mod) => mod.AnalyticsModal),
  { ssr: false }
);`
        },
        tip: 'Hasil: Bundle awal berkurang sebesar 42% (dari 320kb ke 185kb gzipped).'
      }
    ]
  },
  {
    id: 'post-3',
    title: 'Desain Sistem & Micro-Interactions: Menghadirkan Pengalaman Web yang Hidup Tanpa Kaku',
    slug: 'modern-ui-micro-interactions',
    summary: 'Bagaimana memadukan prinsip fisika spring, kontras tipografi elegan, dan tactile feedback untuk membuat website terasa responsif dan menyenangkan.',
    category: 'Clean Code',
    tags: ['UI/UX', 'CSS & Motion', 'Design System', 'Accessibility'],
    readTime: '5 menit baca',
    publishedAt: '03 Jan 2026',
    coverImage: 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80',
    author: {
      name: 'Bagus Batra',
      role: 'Senior Web Developer',
      avatar: PERSONAL_INFO.avatar
    },
    likes: 189,
    views: '4.2k',
    sections: [
      {
        heading: 'Prinsip "Subtle Motion": Lebih Sedikit, Lebih Bermakna',
        body: 'Animasi yang baik tidak mencolok atau memakan waktu pengguna. Tujuannya adalah memberikan konfirmasi visual bahwa interaksi berhasil dilakukan, seperti penekanan tombol atau transisi state.'
      },
      {
        heading: 'Implementasi Spring Physics dengan Motion',
        body: 'Alih-alih menggunakan timing cubic-bezier statis, gunakan parameter spring (stiffness, damping) untuk memberikan bobot alami pada elemen UI.',
        codeSnippet: {
          language: 'tsx',
          filename: 'src/components/InteractiveCard.tsx',
          code: `import { motion } from 'motion/react';

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
}`
        },
        tip: 'Selalu hormati preferensi pengguna dengan menyertakan media query "prefers-reduced-motion".'
      }
    ]
  },
  {
    id: 'post-4',
    title: 'Panduan Lengkap TypeScript Generics untuk Web Developer: Dari Dasar Hingga Utility Patterns',
    slug: 'typescript-generics-masterclass',
    summary: 'Kupas tuntas generic functions, conditional types, mapped types, dan bagaimana membangun API client dengan autocomplete presisi tinggi.',
    category: 'Full-Stack',
    tags: ['TypeScript', 'Best Practices', 'API Design', 'Backend'],
    readTime: '7 menit baca',
    publishedAt: '18 Des 2025',
    coverImage: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format&fit=crop&q=80',
    author: {
      name: 'Bagus Batra',
      role: 'Senior Web Developer',
      avatar: PERSONAL_INFO.avatar
    },
    likes: 275,
    views: '6.8k',
    sections: [
      {
        heading: 'Generic Functions dalam HTTP Client',
        body: 'Membangun wrapper fetch yang otomatis memvalidasi response body dan menyuntikkan tipe data kembalian secara presisi.',
        codeSnippet: {
          language: 'ts',
          filename: 'src/lib/apiClient.ts',
          code: `interface ApiResponse<TData> {
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
    throw new Error(\`API error: \${response.statusText}\`);
  }

  const data: T = await response.json();
  return { data, status: response.status };
}`
        }
      }
    ]
  }
];

export const EXPERIENCES_DATA: Experience[] = [
  {
    id: 'exp-1',
    period: '2023 — Sekarang',
    role: 'Lead Frontend / Full-Stack Engineer',
    company: 'Nexa Digital Technologies',
    location: 'Jakarta (Hybrid / Remote)',
    type: 'Full-Time',
    description: 'Memimpin tim engineering frontend (6 developer) dalam merancang arsitektur aplikasi SaaS multi-tenant, migrasi ke React 19, dan standardisasi design system.',
    achievements: [
      'Memangkas waktu render halaman dashboard utama sebesar 58% melalui implementasi SSR & caching berjenjang.',
      'Membangun shared UI component library yang digunakan oleh 3 anak perusahaan, menghemat ~300 jam kerja pengembang per kuartal.',
      'Mentoring junior & mid-level developers dalam clean code architecture dan automated end-to-end testing.'
    ],
    skills: ['React 19', 'Next.js', 'TypeScript', 'Tailwind CSS', 'GraphQL', 'Docker', 'GCP'],
    featured: true
  },
  {
    id: 'exp-2',
    period: '2021 — 2023',
    role: 'Senior Frontend Developer',
    company: 'Finova Global Financial',
    location: 'Singapura (Remote)',
    type: 'Full-Time',
    description: 'Mengembangkan antarmuka aplikasi perbankan digital dan payment gateway berkeamanan tinggi dengan standar kepatuhan regulasi finansial.',
    achievements: [
      'Mengembangkan fitur multi-currency wallet dengan kalkulator kurs real-time dan zero-latency visual update.',
      'Mencapai skor keamanan OWASP level A dan 100% skor kepatuhan WCAG 2.1 AA untuk aksesibilitas pengguna disabilitas.',
      'Berkolaborasi erat dengan tim Product Manager dan UI/UX Designer untuk mempercepat siklus rilis sprint dua mingguan.'
    ],
    skills: ['React', 'TypeScript', 'Redux Toolkit', 'Zod', 'Jest / Vitest', 'Tailwind CSS'],
    featured: true
  },
  {
    id: 'exp-3',
    period: '2019 — 2021',
    role: 'Full-Stack Web Developer',
    company: 'PixelCraft Studio',
    location: 'Bandung, Indonesia',
    type: 'Full-Time',
    description: 'Membangun puluhan website korporat, e-commerce kustom, dan portal web interaktif untuk berbagai klien brand terkemuka.',
    achievements: [
      'Menyelesaikan 25+ proyek website klien dengan tingkat kepuasan di atas 98% dan tepat waktu sesuai deadline.',
      'Mengintegrasikan sistem pembayaran lokal (Midtrans, Xendit) dan kurir otomatis (JNE, SiCepat API).',
      'Menginisiasi penggunaan Git workflow dan CI/CD otomatis di lingkungan internal studio.'
    ],
    skills: ['JavaScript / TS', 'React', 'Node.js Express', 'PostgreSQL', 'Tailwind CSS', 'REST API'],
    featured: false
  }
];

export const TESTIMONIALS_DATA: Testimonial[] = [
  {
    id: 'test-1',
    name: 'Budi Santoso',
    role: 'Chief Technology Officer (CTO)',
    company: 'Nexa Digital Corp',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80',
    content: 'Bagus adalah salah satu engineer terbaik yang pernah bekerja sama dengan saya. Kemampuannya mengubah kebutuhan bisnis yang rumit menjadi arsitektur web yang rapi, cepat, dan mudah dipelihara sangat luar biasa. Beliau juga memiliki etos kerja dan komunikasi yang sangat profesional.',
    rating: 5,
    projectTag: 'Lumina Analytics Platform'
  },
  {
    id: 'test-2',
    name: 'Sarah Jenkins',
    role: 'Head of Product Design',
    company: 'Nordic Retail Group',
    avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&auto=format&fit=crop&q=80',
    content: 'Collaborating with Bagus was an absolute delight. He has a rare eye for visual detail and micro-interactions while maintaining impeccable code standards. The headless commerce storefront he engineered exceeded our performance targets by over 30%!',
    rating: 5,
    projectTag: 'Aurora Headless Commerce'
  },
  {
    id: 'test-3',
    name: 'Reza Mahendra',
    role: 'Founder & Managing Director',
    company: 'Karsa Venture Lab',
    avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&auto=format&fit=crop&q=80',
    content: 'Proyek website kami selesai lebih cepat dari estimasi dengan kualitas yang melebihi ekspektasi. Bagus tidak hanya sekadar coding, tapi memberikan masukan strategis terkait UX dan SEO yang berdampak langsung pada pertumbuhan konversi klien kami.',
    rating: 5,
    projectTag: 'Corporate Web Architecture'
  }
];
