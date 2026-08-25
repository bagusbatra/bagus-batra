{{--
    Halaman detail Project (Iterasi 11, Fase 2) — menggantikan modal
    on-page lama (project-modal.blade.php, dicabut di iterasi ini). Konten
    mereplikasi penuh 3 tab modal (Overview & Solusi / Arsitektur Stack /
    Simulasi Interaktif) sebagai halaman utuh, server-rendered langsung dari
    $project (bukan lagi lewat $store.ui.activeProject + @js embed), tab
    tetap Alpine lokal (x-data="{ tab: ... }") supaya tanpa reload.
    Lihat docs/RENCANA-PENGEMBANGAN.md #10-11.
--}}
@extends('layouts.app')

@section('meta_title', $project->title.' — Studi Kasus Proyek | Bagus Batra')
@section('meta_description', $project->tagline)
@section('meta_image', $project->image)

@section('content')
    <section class="py-28 sm:py-32 bg-transparent border-b border-white/60">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            {{-- Breadcrumb --}}
            <nav data-reveal aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-semibold text-slate-500 flex-wrap">
                <a href="{{ route('portfolio.index') }}" class="hover:text-indigo-600 transition-colors">
                    <span x-show="$store.lang.current === 'id'">Beranda</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Home</span>
                </a>
                <x-icon name="arrow-right" class="w-3 h-3 text-slate-300 shrink-0" />
                <a href="{{ route('projects.index') }}" class="hover:text-indigo-600 transition-colors">
                    <span x-show="$store.lang.current === 'id'">Proyek</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Projects</span>
                </a>
                <x-icon name="arrow-right" class="w-3 h-3 text-slate-300 shrink-0" />
                <span class="text-slate-800 truncate max-w-[220px] sm:max-w-none">{{ $project->title }}</span>
            </nav>

            {{-- Back link --}}
            <div data-reveal>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span x-show="$store.lang.current === 'id'">Kembali ke Semua Proyek</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Back to All Projects</span>
                </a>
            </div>

            <div data-reveal x-data="{ tab: 'overview' }" class="bg-white/95 backdrop-blur-2xl rounded-3xl shadow-xl border border-white/80 overflow-hidden">
                {{-- Top Bar with Project Banner --}}
                <div class="relative h-64 sm:h-80 bg-slate-900 overflow-hidden">
                    <img
                        src="{{ $project->image }}"
                        alt="{{ $project->title }}"
                        onerror="this.onerror=null;this.src='https://placehold.co/1200x800/0f172a/94a3b8?text=No+Image';"
                        width="1200"
                        height="800"
                        loading="eager"
                        decoding="async"
                        fetchpriority="high"
                        class="w-full h-full object-cover opacity-60 mix-blend-luminosity scale-105"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

                    <div class="absolute bottom-6 left-6 right-6 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 bg-indigo-500/90 backdrop-blur-md text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-xs border border-indigo-400/40">{{ $project->category }}</span>
                            @if ($project->featured)
                                <span class="px-3 py-1 bg-amber-400/95 backdrop-blur-md text-amber-950 text-xs font-bold rounded-lg flex items-center gap-1 shadow-2xs">
                                    <x-icon name="sparkles" class="w-3.5 h-3.5" />
                                    Featured
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $project->title }}</h1>
                        <p class="text-sm text-slate-300 max-w-2xl">{{ $project->tagline }}</p>
                    </div>
                </div>

                {{-- Navigation Sub-Tabs --}}
                <div class="flex border-b border-slate-200/80 bg-white/60 backdrop-blur-md px-6 pt-3 gap-2 overflow-x-auto scrollbar-none">
                    <button id="tab-overview" @click="tab = 'overview'" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors cursor-pointer shrink-0" :class="tab === 'overview' ? 'bg-white text-indigo-600 border-t-2 border-indigo-600 shadow-2xs' : 'text-slate-500 hover:text-slate-900'">
                        <span x-show="$store.lang.current === 'id'">Ikhtisar &amp; Solusi</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Overview &amp; Highlights</span>
                    </button>
                    <button id="tab-architecture" @click="tab = 'architecture'" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors cursor-pointer shrink-0" :class="tab === 'architecture' ? 'bg-white text-indigo-600 border-t-2 border-indigo-600 shadow-2xs' : 'text-slate-500 hover:text-slate-900'">
                        <span x-show="$store.lang.current === 'id'">Arsitektur Stack</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Tech Architecture</span>
                    </button>
                    {{-- Iterasi 28 (Fase 5): tab_preview bisa disembunyikan admin (Project::isBlockHidden(), lihat Admin\ProjectController::HIDEABLE_BLOCKS) — tombol tab & isinya (di bawah) dibungkus @unless yang SAMA supaya tidak ada tombol "mati" yang membuka konten kosong. --}}
                    @unless ($project->isBlockHidden('tab_preview'))
                        <button id="tab-preview" @click="tab = 'preview'" class="px-4 py-2.5 text-xs font-bold rounded-t-xl transition-colors cursor-pointer shrink-0" :class="tab === 'preview' ? 'bg-white text-indigo-600 border-t-2 border-indigo-600 shadow-2xs' : 'text-slate-500 hover:text-slate-900'">
                            <span x-show="$store.lang.current === 'id'">Simulasi Interaktif</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Live Preview</span>
                        </button>
                    @endunless
                </div>

                {{-- Tab Content --}}
                <div class="p-6 sm:p-8 space-y-6">
                    {{-- Overview Tab --}}
                    <div x-show="tab === 'overview'" class="space-y-6">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 text-xs shadow-2xs">
                            <div>
                                <span class="text-slate-400 block font-semibold">Role</span>
                                <span class="font-bold text-slate-800">{{ $project->role }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold">Timeline</span>
                                <span class="font-bold text-slate-800">{{ $project->timeline }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold">Client / Scope</span>
                                <span class="font-bold text-slate-800">{{ $project->client ?: 'Open Project' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block font-semibold">Category</span>
                                <span class="font-bold text-indigo-600">{{ $project->category }}</span>
                            </div>
                        </div>

                        @if (!empty($project->metrics) && !$project->isBlockHidden('metrics'))
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                                    <span x-show="$store.lang.current === 'id'">Hasil &amp; Dampak Terukur</span>
                                    <span x-show="$store.lang.current === 'en'" x-cloak>Key Engineering Metrics</span>
                                </h4>
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach ($project->metrics as $m)
                                        <div class="p-3.5 bg-indigo-50/80 backdrop-blur-md rounded-xl border border-indigo-100/80 text-center shadow-2xs">
                                            <div class="text-xl sm:text-2xl font-extrabold text-indigo-700">{{ $m['value'] ?? '' }}</div>
                                            <div class="text-[11px] font-semibold text-slate-600 mt-0.5">{{ $m['label'] ?? '' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <h4 class="text-sm font-bold text-slate-900">
                                <span x-show="$store.lang.current === 'id'">Tantangan &amp; Solusi Rekayasa</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Engineering Challenge &amp; Solution</span>
                            </h4>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $project->long_description }}</p>
                        </div>

                        @if (!empty($project->highlights) && !$project->isBlockHidden('highlights'))
                            <div class="space-y-2.5">
                                <h4 class="text-sm font-bold text-slate-900">
                                    <span x-show="$store.lang.current === 'id'">Sorotan Fitur &amp; Inovasi</span>
                                    <span x-show="$store.lang.current === 'en'" x-cloak>Key Technical Highlights</span>
                                </h4>
                                <div class="space-y-2">
                                    @foreach ($project->highlights as $h)
                                        <div class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-700">
                                            <x-icon name="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                                            <span>{{ $h }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Architecture Tab --}}
                    <div x-show="tab === 'architecture'" x-cloak class="space-y-6">
                        <div class="space-y-4">
                            @if (!empty($project->tech_stack['frontend'] ?? []) && !$project->isBlockHidden('tech_frontend'))
                                <div>
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Frontend &amp; Client Stack</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($project->tech_stack['frontend'] as $t)
                                            <span class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-bold">{{ $t }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (!empty($project->tech_stack['backend'] ?? []) && !$project->isBlockHidden('tech_backend'))
                                <div>
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Backend &amp; API Services</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($project->tech_stack['backend'] as $t)
                                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold">{{ $t }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (!empty($project->tech_stack['database'] ?? []) && !$project->isBlockHidden('tech_database'))
                                <div>
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Database &amp; Caching Layer</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($project->tech_stack['database'] as $t)
                                            <span class="px-3 py-1.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-xs font-bold">{{ $t }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (!empty($project->tech_stack['cloudAndDevOps'] ?? []) && !$project->isBlockHidden('tech_cloud'))
                                <div>
                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cloud, CI/CD &amp; Deployment</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($project->tech_stack['cloudAndDevOps'] as $t)
                                            <span class="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-bold">{{ $t }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Preview Tab --}}
                    @unless ($project->isBlockHidden('tab_preview'))
                    <div x-show="tab === 'preview'" x-cloak class="space-y-4">
                        <div class="p-5 bg-slate-900 text-slate-200 rounded-2xl border border-slate-800 space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-mono text-slate-400 ml-2">preview.applet.local:3000/{{ $project->project_key }}</span>
                                </div>
                                <span class="text-[11px] font-mono bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded-sm">200 OK</span>
                            </div>

                            <div class="p-6 bg-slate-950 rounded-xl border border-slate-800/80 text-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-indigo-500/30">
                                    <x-icon name="zap" class="w-6 h-6" />
                                </div>
                                <h5 class="font-bold text-white text-base">
                                    {{ $project->title }} — Live Sandbox Active
                                </h5>
                                <p class="text-xs text-slate-400 max-w-md mx-auto">
                                    <span x-show="$store.lang.current === 'id'">Simulasi antarmuka berkinerja tinggi dengan visualisasi real-time dan transisi sub-frame.</span>
                                    <span x-show="$store.lang.current === 'en'" x-cloak>Simulated production build with live data streaming and instant responsiveness.</span>
                                </p>
                                @if ($project->demo_url)
                                    <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition-colors">
                                        <span x-show="$store.lang.current === 'id'">Buka Live Web App</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Launch Full Web App</span>
                                        <x-icon name="external-link" class="w-3.5 h-3.5" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endunless
                </div>

                {{-- Footer Action Strip --}}
                <div class="p-5 sm:p-6 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        @foreach (array_slice($project->tags ?? [], 0, 3) as $tag)
                            <span class="text-xs font-semibold text-slate-600 bg-white px-2.5 py-1 rounded-md border border-slate-200">{{ $tag }}</span>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2.5">
                        @if ($project->github_url)
                            <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-100 text-slate-800 text-xs font-bold rounded-xl border border-slate-200 transition-colors">
                                <x-icon name="github" class="w-4 h-4" />
                                <span>GitHub</span>
                            </a>
                        @endif
                        @if ($project->demo_url)
                            <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                                <span x-show="$store.lang.current === 'id'">Kunjungi Live Demo</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Live Preview</span>
                                <x-icon name="external-link" class="w-4 h-4" />
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Project Lainnya --}}
            @if ($related->isNotEmpty())
                <div data-reveal class="space-y-4 pt-4">
                    <h2 class="text-lg font-extrabold text-slate-900">
                        <span x-show="$store.lang.current === 'id'">Proyek Lainnya</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Other Projects</span>
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($related as $r)
                            <a
                                href="{{ route('projects.show', $r) }}"
                                class="group bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:shadow-lg hover:border-[var(--accent-300)]/80 hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col"
                            >
                                <div class="relative h-32 overflow-hidden bg-slate-100">
                                    <img
                                        src="{{ $r->image }}"
                                        alt="{{ $r->title }}"
                                        onerror="this.onerror=null;this.src='https://placehold.co/600x400/e2e8f0/64748b?text=No+Image';"
                                        width="600"
                                        height="400"
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                </div>
                                <div class="p-4 space-y-1.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">{{ $r->category }}</span>
                                    <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-[var(--accent-600)] transition-colors line-clamp-1">{{ $r->title }}</h3>
                                    <p class="text-xs text-slate-500 line-clamp-2">{{ $r->description }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
