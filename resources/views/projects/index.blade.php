{{--
    Halaman katalog lengkap Projects (Iterasi 10, Fase 2). Extend layout
    utama yang sama persis dipakai halaman index (`layouts.app`) supaya
    navbar/footer/ambient background/lang store/reveal-on-scroll identik
    tanpa duplikasi layout — lihat docs/RENCANA-PENGEMBANGAN.md #10.

    Tombol "Detail Case Study" adalah <a> biasa ke halaman
    /projects/{project_key} (route projects.show) — modal case-study lama
    dicabut penuh di Iterasi 11 (lihat project-modal.blade.php di riwayat git).
--}}
@extends('layouts.app')

@section('meta_title', 'Semua Proyek — Bagus Batra')
@section('meta_description', 'Katalog lengkap studi kasus proyek web & arsitektur sistem yang dikerjakan Bagus Batra — dari platform SaaS full-stack hingga open source.')

@section('content')
    @php
        $projectCategories = [
            ['id' => 'All', 'id_label' => 'Semua Proyek', 'en_label' => 'All Projects'],
            ['id' => 'Full-Stack', 'id_label' => 'Full-Stack', 'en_label' => 'Full-Stack'],
            ['id' => 'Frontend', 'id_label' => 'Frontend Apps', 'en_label' => 'Frontend Apps'],
            ['id' => 'UI/UX & Systems', 'id_label' => 'Design Systems', 'en_label' => 'Design Systems'],
            ['id' => 'Open Source', 'id_label' => 'Open Source', 'en_label' => 'Open Source'],
            ['id' => 'AI & Tools', 'id_label' => 'AI & Tools', 'en_label' => 'AI & Tools'],
        ];
    @endphp

    <section id="projects-catalog" x-data="projectsSection()" class="py-28 sm:py-32 bg-transparent border-b border-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
            {{-- Back link --}}
            <div data-reveal>
                <a href="{{ route('portfolio.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span x-show="$store.lang.current === 'id'">Kembali ke Beranda</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Back to Home</span>
                </a>
            </div>

            {{-- Section Title --}}
            <div data-reveal class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-[var(--accent-50)]/80 backdrop-blur-md text-[var(--accent-700)] border border-[var(--accent-100)] text-xs font-bold uppercase tracking-wider">
                        <x-icon name="folder-git-2" class="w-3.5 h-3.5" />
                        <span x-show="$store.lang.current === 'id'">Katalog Lengkap</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Full Catalog</span>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        <span x-show="$store.lang.current === 'id'">Semua Proyek &amp; Studi Kasus</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>All Projects &amp; Case Studies</span>
                    </h1>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        <span x-show="$store.lang.current === 'id'">Seluruh produk digital yang pernah saya rancang dari tahap konsep hingga deployment — {{ $projects->total() }} proyek dan bertambah.</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Every digital product I've shipped from concept to deployment — {{ $projects->total() }} projects and counting.</span>
                    </p>
                </div>

                {{-- Category Filter Tabs --}}
                <div class="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 shadow-2xs overflow-x-auto max-w-full pb-1.5 sm:pb-1.5 scrollbar-none">
                    @foreach ($projectCategories as $cat)
                        <button
                            id="catalog-cat-{{ \Illuminate\Support\Str::slug($cat['id']) }}"
                            @click="category = '{{ $cat['id'] }}'"
                            class="relative px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0"
                            :class="category === '{{ $cat['id'] }}' ? 'text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'"
                        >
                            <span x-show="category === '{{ $cat['id'] }}'" class="absolute inset-0 bg-[var(--accent-600)] rounded-xl shadow-xs -z-10"></span>
                            <span x-show="$store.lang.current === 'id'">{{ $cat['id_label'] }}</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>{{ $cat['en_label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Projects Grid --}}
            @if ($projects->isEmpty())
                <div data-reveal class="py-20 text-center text-sm text-slate-400">
                    <span x-show="$store.lang.current === 'id'">Belum ada proyek yang ditambahkan.</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>No projects have been added yet.</span>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
                    @foreach ($projects as $project)
                        <article
                            data-reveal
                            x-show="category === 'All' || category === '{{ $project->category }}'"
                            x-transition
                            id="catalog-card-{{ $project->project_key }}"
                            class="group bg-white/60 backdrop-blur-lg rounded-3xl border border-white/80 shadow-2xs hover:shadow-xl hover:border-[var(--accent-300)]/80 hover:-translate-y-1.5 transition-all duration-300 flex flex-col overflow-hidden"
                        >
                            {{-- Project Image Banner --}}
                            <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100">
                                <img
                                    src="{{ $project->image }}"
                                    alt="{{ $project->title }}"
                                    onerror="this.onerror=null;this.src='https://placehold.co/800x600/e2e8f0/64748b?text=No+Image';"
                                    width="800"
                                    height="600"
                                    loading="lazy"
                                    decoding="async"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>

                                <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between">
                                    <span class="px-2.5 py-1 bg-white/85 backdrop-blur-md text-slate-800 text-[11px] font-bold rounded-lg shadow-2xs border border-white/60">{{ $project->category }}</span>
                                    @if ($project->featured)
                                        <span class="px-2.5 py-1 bg-amber-400/95 text-amber-950 text-[11px] font-bold rounded-lg shadow-2xs flex items-center gap-1">
                                            <x-icon name="sparkles" class="w-3 h-3" />
                                            Featured
                                        </span>
                                    @endif
                                </div>

                                <div class="absolute bottom-3 left-3 right-3 flex items-center gap-2">
                                    @foreach (array_slice($project->metrics ?? [], 0, 2) as $m)
                                        <span class="px-2.5 py-1 bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-mono font-medium rounded-lg border border-white/10">
                                            <strong class="text-indigo-300 font-bold">{{ $m['value'] }}</strong> {{ $m['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Card Content Body --}}
                            <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-[var(--accent-600)] transition-colors">{{ $project->title }}</h3>
                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $project->description }}</p>
                                </div>

                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach (array_slice($project->tags ?? [], 0, 4) as $tag)
                                        <span class="px-2.5 py-1 bg-white/70 backdrop-blur-xs text-slate-600 rounded-md text-[11px] font-semibold border border-white/60 shadow-2xs">{{ $tag }}</span>
                                    @endforeach
                                </div>

                                <div class="pt-3 border-t border-slate-200/50 flex items-center justify-between">
                                    <a
                                        id="catalog-view-study-{{ $project->project_key }}"
                                        href="{{ route('projects.show', $project) }}"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 py-1"
                                    >
                                        <span x-show="$store.lang.current === 'id'">Detail Case Study</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Case Study</span>
                                        <x-icon name="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                                    </a>

                                    <div class="flex items-center gap-1.5">
                                        @if ($project->github_url)
                                            <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="p-2 text-slate-500 hover:text-slate-900 hover:bg-white/80 rounded-lg transition-colors border border-transparent hover:border-white/80 shadow-2xs" title="Lihat Repositori GitHub">
                                                <x-icon name="github" class="w-4 h-4" />
                                            </a>
                                        @endif
                                        @if ($project->demo_url)
                                            <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/80 rounded-lg transition-colors border border-transparent hover:border-indigo-100 shadow-2xs" title="Buka Live Demo">
                                                <x-icon name="external-link" class="w-4 h-4" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($projects->hasPages())
                    <div data-reveal class="pt-4">
                        {{ $projects->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
