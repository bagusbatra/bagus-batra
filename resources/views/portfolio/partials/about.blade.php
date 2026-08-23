@php
    $skillIconMap = [
        'Code2' => 'code2', 'FileCode' => 'file-code', 'Palette' => 'palette', 'Sparkles' => 'sparkles',
        'Layers' => 'layers', 'Server' => 'server', 'Database' => 'database', 'Cpu' => 'cpu',
        'Cloud' => 'cloud', 'GitBranch' => 'git-branch', 'Layout' => 'layout', 'Zap' => 'zap',
    ];

    $principles = [
        [
            'title_id' => 'Performance-First', 'title_en' => 'Performance-First',
            'desc_id' => 'Zero layout shift, bundle splitting, dan sub-second response time adalah standar tanpa kompromi.',
            'desc_en' => 'Zero layout shift, smart code-splitting, and sub-second page loads are foundational standards.',
            'icon' => 'zap', 'icon_color' => 'text-amber-500', 'bg' => 'bg-amber-50 border-amber-200/80',
        ],
        [
            'title_id' => 'Aksesibilitas & WAI-ARIA', 'title_en' => 'Accessibility by Default',
            'desc_id' => 'Antarmuka web harus dapat diakses dengan nyaman oleh semua pengguna, termasuk keyboard & screen reader.',
            'desc_en' => 'Interfaces that are naturally navigable via keyboard and assistive screen readers.',
            'icon' => 'shield-check', 'icon_color' => 'text-emerald-500', 'bg' => 'bg-emerald-50 border-emerald-200/80',
        ],
        [
            'title_id' => 'Arsitektur Modular & Bersih', 'title_en' => 'Modular Clean Architecture',
            'desc_id' => 'Struktur kode rapi, type-safe, dan terdokumentasi dengan baik agar mudah diskalakan bersama tim.',
            'desc_en' => 'Type-safe, maintainable patterns designed for scalable team development.',
            'icon' => 'layers', 'icon_color' => 'text-indigo-500', 'bg' => 'bg-indigo-50 border-indigo-200/80',
        ],
        [
            'title_id' => 'Micro-Interactions yang Alami', 'title_en' => 'Natural Micro-Interactions',
            'desc_id' => 'Sentuhan animasi berbasis fisika nyata yang membuat aplikasi web terasa hidup dan tidak kaku.',
            'desc_en' => 'Delightful physics-based transitions that give digital products tactile elegance.',
            'icon' => 'sparkles', 'icon_color' => 'text-purple-500', 'bg' => 'bg-purple-50 border-purple-200/80',
        ],
    ];

    $skillCategories = [
        ['id' => 'all', 'id_label' => 'Semua', 'en_label' => 'All'],
        ['id' => 'frontend', 'id_label' => 'Frontend', 'en_label' => 'Frontend'],
        ['id' => 'backend', 'id_label' => 'Backend', 'en_label' => 'Backend'],
        ['id' => 'devops', 'id_label' => 'DevOps & Cloud', 'en_label' => 'DevOps'],
        ['id' => 'tools', 'id_label' => 'Tools & UI', 'en_label' => 'Tools'],
    ];
@endphp

<section id="about" class="py-20 sm:py-24 bg-transparent border-b border-white/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16 sm:space-y-20">
        {{-- Section Header --}}
        <div data-reveal class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
                <x-icon name="award" class="w-3.5 h-3.5" />
                <span x-show="$store.lang.current === 'id'">Tentang &amp; Prinsip Rekayasa</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>About &amp; Engineering Values</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <span x-show="$store.lang.current === 'id'">Menghubungkan Desain Presisi dengan Kode Berkinerja Tinggi</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Bridging Pixel Precision with High-Performance Architecture</span>
            </h2>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                <span x-show="$store.lang.current === 'id'">{{ $personalInfo['bio_id'] }}</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>{{ $personalInfo['bio_en'] }}</span>
            </p>
        </div>

        {{-- 4 Core Principles Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            @foreach ($principles as $p)
                <div data-reveal class="p-5 sm:p-6 rounded-3xl border backdrop-blur-lg {{ $p['bg'] }} transition-all duration-300 shadow-2xs hover:shadow-lg hover:-translate-y-1.25">
                    <div class="w-11 h-11 rounded-2xl bg-white/90 shadow-2xs flex items-center justify-center mb-4 border border-white/80">
                        <x-icon :name="$p['icon']" class="w-5 h-5 {{ $p['icon_color'] }}" />
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-2">
                        <span x-show="$store.lang.current === 'id'">{{ $p['title_id'] }}</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>{{ $p['title_en'] }}</span>
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        <span x-show="$store.lang.current === 'id'">{{ $p['desc_id'] }}</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>{{ $p['desc_en'] }}</span>
                    </p>
                </div>
            @endforeach
        </div>

        {{--
            Tech Stack & Skills Matrix — this lives inside the same
            <section id="about"> as the About copy above (one physical
            HTML section, two section_settings rows: "about" and
            "skills"). Toggling "about" off hides this whole block too
            (it's nested); toggling "skills" off while "about" stays on
            hides just this sub-block, keeping the About copy visible.
        --}}
        @if ($sectionActive['skills'] ?? true)
        <div id="skills" x-data="aboutSection()" class="pt-4 sm:pt-8 space-y-6 sm:space-y-8">
            <div data-reveal class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="max-w-2xl">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        <span x-show="$store.lang.current === 'id'">Tech Stack &amp; Matriks Keahlian</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Tech Stack &amp; Skills Matrix</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        <span x-show="$store.lang.current === 'id'">Teknologi modern yang saya gunakan sehari-hari dalam membangun produk digital kelas dunia.</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Modern technologies and tooling leveraged in production-grade software.</span>
                    </p>
                </div>

                {{-- Filter Pills --}}
                <div class="flex items-center gap-1 p-1.5 bg-white/50 backdrop-blur-md rounded-2xl border border-white/80 shadow-2xs overflow-x-auto max-w-full pb-1.5 sm:pb-1.5 scrollbar-none">
                    @foreach ($skillCategories as $cat)
                        <button
                            id="skill-filter-{{ $cat['id'] }}"
                            @click="category = '{{ $cat['id'] }}'"
                            class="relative px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors cursor-pointer shrink-0"
                            :class="category === '{{ $cat['id'] }}' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'"
                        >
                            <span x-show="category === '{{ $cat['id'] }}'" class="absolute inset-0 bg-white shadow-2xs border border-white/90 rounded-xl -z-10"></span>
                            <span x-show="$store.lang.current === 'id'">{{ $cat['id_label'] }}</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>{{ $cat['en_label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Skill Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-4.5">
                @foreach ($skills as $skill)
                    <div
                        data-reveal
                        id="skill-card-{{ \Illuminate\Support\Str::slug($skill->name) }}"
                        x-show="category === 'all' || category === '{{ $skill->category }}'"
                        x-transition
                        class="p-5 sm:p-5.5 bg-white/60 backdrop-blur-lg rounded-3xl border border-white/80 hover:border-indigo-300/80 shadow-2xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden"
                    >
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-white/80 border border-white flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors shrink-0 shadow-2xs">
                                <x-icon :name="$skillIconMap[$skill->icon_name] ?? 'code2'" class="w-5 h-5" />
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-mono font-bold text-slate-400 group-hover:text-indigo-600 transition-colors">{{ $skill->level }}%</span>
                                <p class="text-[11px] text-slate-400">{{ $skill->experience }}</p>
                            </div>
                        </div>

                        <h4 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $skill->name }}</h4>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $skill->highlight_text }}</p>

                        {{-- Visual Progress Bar --}}
                        <div class="w-full bg-slate-200/50 h-1.5 rounded-full overflow-hidden mt-3.5">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all duration-700" style="width: {{ $skill->level }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
