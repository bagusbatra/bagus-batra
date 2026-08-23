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

<section id="projects" x-data="projectsSection()" class="py-20 sm:py-24 bg-transparent border-b border-white/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {{-- Section Title --}}
        <div data-reveal class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="max-w-2xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
                    <x-icon name="folder-git-2" class="w-3.5 h-3.5" />
                    <span x-show="$store.lang.current === 'id'">Karya Pilihan</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Featured Work</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    <span x-show="$store.lang.current === 'id'">Studi Kasus Proyek Web &amp; Arsitektur Sistem</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Selected Web Projects &amp; Systems Architecture</span>
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    <span x-show="$store.lang.current === 'id'">Koleksi produk digital nyata yang saya rancang dari tahap konsep hingga deployment dengan fokus kecepatan &amp; pengalaman pengguna.</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Production applications built with a focus on runtime performance, accessibility, and high conversion.</span>
                </p>
            </div>

            {{-- Category Filter Tabs --}}
            <div class="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 shadow-2xs overflow-x-auto max-w-full pb-1.5 sm:pb-1.5 scrollbar-none">
                @foreach ($projectCategories as $cat)
                    <button
                        id="project-cat-{{ \Illuminate\Support\Str::slug($cat['id']) }}"
                        @click="category = '{{ $cat['id'] }}'"
                        class="relative px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0"
                        :class="category === '{{ $cat['id'] }}' ? 'text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'"
                    >
                        <span x-show="category === '{{ $cat['id'] }}'" class="absolute inset-0 bg-indigo-600 rounded-xl shadow-xs -z-10"></span>
                        <span x-show="$store.lang.current === 'id'">{{ $cat['id_label'] }}</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>{{ $cat['en_label'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
            @foreach ($projects as $project)
                <article
                    data-reveal
                    x-data="{ project: @js($project->toJs()) }"
                    x-show="category === 'All' || category === '{{ $project->category }}'"
                    x-transition
                    id="project-card-{{ $project->project_key }}"
                    class="group bg-white/60 backdrop-blur-lg rounded-3xl border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 hover:-translate-y-1.5 transition-all duration-300 flex flex-col overflow-hidden"
                >
                    {{-- Project Image Banner --}}
                    <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100">
                        <img src="{{ $project->image }}" alt="{{ $project->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
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
                            @foreach (array_slice($project->metrics, 0, 2) as $m)
                                <span class="px-2.5 py-1 bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-mono font-medium rounded-lg border border-white/10">
                                    <strong class="text-indigo-300 font-bold">{{ $m['value'] }}</strong> {{ $m['label'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Card Content Body --}}
                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $project->title }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">{{ $project->description }}</p>
                        </div>

                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @foreach (array_slice($project->tags, 0, 4) as $tag)
                                <span class="px-2.5 py-1 bg-white/70 backdrop-blur-xs text-slate-600 rounded-md text-[11px] font-semibold border border-white/60 shadow-2xs">{{ $tag }}</span>
                            @endforeach
                        </div>

                        <div class="pt-3 border-t border-slate-200/50 flex items-center justify-between">
                            <button
                                id="view-study-{{ $project->project_key }}"
                                @click="$store.ui.openProject(project)"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 cursor-pointer py-1"
                            >
                                <span x-show="$store.lang.current === 'id'">Detail Case Study</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Case Study</span>
                                <x-icon name="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                            </button>

                            <div class="flex items-center gap-1.5">
                                @if ($project->github_url)
                                    <a id="card-github-{{ $project->project_key }}" href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="p-2 text-slate-500 hover:text-slate-900 hover:bg-white/80 rounded-lg transition-colors border border-transparent hover:border-white/80 shadow-2xs" title="Lihat Repositori GitHub">
                                        <x-icon name="github" class="w-4 h-4" />
                                    </a>
                                @endif
                                @if ($project->demo_url)
                                    <a id="card-demo-{{ $project->project_key }}" href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/80 rounded-lg transition-colors border border-transparent hover:border-indigo-100 shadow-2xs" title="Buka Live Demo">
                                        <x-icon name="external-link" class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
