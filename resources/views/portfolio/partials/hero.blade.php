<section id="hero" class="relative pt-28 pb-20 md:pt-36 md:pb-28 overflow-hidden bg-gradient-to-b from-slate-50/60 via-white to-slate-50/40">
    {{-- Subtle background ambient mesh --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[600px] pointer-events-none overflow-hidden opacity-60">
        <div class="absolute -top-32 left-1/4 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl"></div>
        <div class="absolute top-10 right-1/4 w-80 h-80 bg-blue-200/30 rounded-full blur-3xl"></div>
        <div class="absolute top-48 left-1/2 w-72 h-72 bg-amber-200/20 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            {{-- Left Column: Heading & Value Proposition --}}
            <div class="lg:col-span-7 space-y-7">
                {{-- Status Beacon --}}
                <div data-reveal class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/70 backdrop-blur-md border border-white/90 shadow-2xs text-xs font-semibold text-slate-800">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span x-show="$store.lang.current === 'id'">Tersedia untuk Proyek Baru &amp; Konsultasi</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Available for New Projects &amp; Contracts</span>
                </div>

                {{-- Main Headline --}}
                <div data-reveal class="space-y-3">
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15] sm:leading-[1.12]">
                        <span x-show="$store.lang.current === 'id'">Merancang Web <span class="text-transparent bg-clip-text bg-gradient-to-r from-[var(--accent-600)] via-blue-600 to-[var(--accent-500)]">Cepat, Elegan</span> &amp; Intuitif.</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Engineering <span class="text-transparent bg-clip-text bg-gradient-to-r from-[var(--accent-600)] via-blue-600 to-[var(--accent-500)]">Fast, Elegant</span> &amp; Scalable Web Apps.</span>
                    </h1>
                    <p class="text-base sm:text-lg lg:text-xl text-slate-600 font-normal leading-relaxed max-w-2xl">
                        <span x-show="$store.lang.current === 'id'">{{ $personalInfo['tagline_id'] }}</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>{{ $personalInfo['tagline_en'] }}</span>
                    </p>
                </div>

                {{-- Quick Experience Pills --}}
                <div data-reveal class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-600">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/60 backdrop-blur-md text-slate-700 border border-white/80 shadow-2xs">
                        <x-icon name="map-pin" class="w-3.5 h-3.5 text-indigo-500" />
                        {{ $personalInfo['location'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/60 backdrop-blur-md text-slate-700 border border-white/80 shadow-2xs">
                        <x-icon name="zap" class="w-3.5 h-3.5 text-amber-500" />
                        <span x-show="$store.lang.current === 'id'">Spesialis React 19 &amp; Next.js</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>React 19 &amp; Next.js Specialist</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/60 backdrop-blur-md text-slate-700 border border-white/80 shadow-2xs">
                        <x-icon name="check-circle-2" class="w-3.5 h-3.5 text-emerald-500" />
                        Lighthouse 95+ Score
                    </span>
                </div>

                {{-- CTA Buttons --}}
                <div data-reveal class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3 pt-2">
                    <button id="hero-explore-projects-btn" @click="scrollTo('projects')" class="group flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-slate-900 hover:bg-[var(--accent-600)] text-white font-bold text-sm shadow-md hover:shadow-[var(--accent-500)]/25 transition-all duration-300 cursor-pointer hover:scale-103 hover:-translate-y-0.5 active:scale-97">
                        <span x-show="$store.lang.current === 'id'">Jelajahi Showcase Proyek</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Explore Featured Projects</span>
                        <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </button>

                    <button id="hero-contact-btn" @click="scrollTo('contact')" class="flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-white/80 hover:bg-white text-slate-800 font-bold text-sm border border-white/90 shadow-2xs hover:border-indigo-200 backdrop-blur-md transition-all cursor-pointer hover:scale-103 hover:-translate-y-0.5 active:scale-97">
                        <x-icon name="message-square" class="w-4 h-4 text-indigo-600" />
                        <span x-show="$store.lang.current === 'id'">Diskusikan Proyek</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Let&rsquo;s Talk</span>
                    </button>

                    <button id="hero-cv-btn" @click="$store.ui.openCv()" class="flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl bg-indigo-50/80 hover:bg-indigo-100 text-indigo-700 font-bold text-sm border border-indigo-200/80 backdrop-blur-md transition-colors cursor-pointer hover:scale-103 hover:-translate-y-0.5 active:scale-97" title="Download CV format PDF">
                        <x-icon name="file-down" class="w-4 h-4" />
                        <span x-show="$store.lang.current === 'id'">Resume / CV</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Download CV</span>
                    </button>
                </div>

                {{--
                    Social Media Links Bar — Iterasi 21 (Fase 4, Bagian A):
                    toggle `hero_social_bar_visible`. HANYA baris ini (Hero),
                    BUKAN social-bar varian 'cards' di section Contact
                    (contact.blade.php) yang selalu tampil, di luar cakupan
                    toggle ini — lihat docs/LOG-ITERASI.md entri Iterasi 21.
                --}}
                @if ($heroSocialBarVisible ?? true)
                    <div data-reveal class="pt-3 border-t border-slate-200/60">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">
                            <span x-show="$store.lang.current === 'id'">Koneksi &amp; Media Sosial</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Connect on Social Platforms</span>
                        </p>
                        @include('portfolio.partials.social-bar', ['variant' => 'horizontal'])
                    </div>
                @endif
            </div>

            {{-- Right Column: Interactive Developer Profile & Code Card --}}
            <div data-reveal class="lg:col-span-5 relative">
                {{-- Supporting Floating Tech Badges --}}
                <div class="hidden sm:flex float-badge-a absolute -top-4 -left-4 z-20 items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/90 backdrop-blur-md border border-white/90 shadow-md text-xs font-bold text-indigo-700">
                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-amber-500" />
                    <span>React 19 Ready</span>
                </div>

                <div class="hidden sm:flex float-badge-b absolute -bottom-3 -right-3 z-20 items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900/90 text-white backdrop-blur-md border border-slate-800 shadow-md text-xs font-bold font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>TypeScript 5.8</span>
                </div>

                <div class="relative mx-auto max-w-md lg:max-w-none">
                    {{-- Outer Frosted Glass Card --}}
                    <div class="p-1.5 rounded-3xl bg-white/50 backdrop-blur-xl border border-white/80 shadow-lg shadow-slate-200/50">
                        <div class="bg-white/70 backdrop-blur-lg rounded-[22px] p-5 sm:p-7 space-y-5 sm:space-y-6 border border-white/90">
                            {{-- Top Bar with Developer Info --}}
                            <div class="flex items-center justify-between pb-4 border-b border-slate-200/50">
                                <div class="flex items-center gap-3.5">
                                    <div class="relative">
                                        <img src="{{ $personalInfo['avatar'] }}" alt="{{ $personalInfo['name'] }}" width="56" height="56" loading="eager" decoding="async" fetchpriority="high" class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl object-cover border-2 border-white shadow-sm" />
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white"></div>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-slate-900 text-base">{{ $personalInfo['name'] }}</h3>
                                        <p class="text-xs text-indigo-600 font-semibold">
                                            <span x-show="$store.lang.current === 'id'">{{ $personalInfo['title_id'] }}</span>
                                            <span x-show="$store.lang.current === 'en'" x-cloak>{{ $personalInfo['title_en'] }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="px-2.5 py-1 rounded-lg bg-white/70 backdrop-blur-md border border-white/80 text-[11px] font-mono text-slate-600 font-bold">
                                    v2026.1
                                </div>
                            </div>

                            {{-- Micro Terminal Snippet --}}
                            <div class="bg-slate-900/90 backdrop-blur-md text-slate-200 rounded-2xl p-3.5 sm:p-4 font-mono text-xs shadow-inner space-y-2 border border-slate-800 overflow-x-auto">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-800 text-[11px] text-slate-400 min-w-[240px]">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></div>
                                    </div>
                                    <span>developer.config.ts</span>
                                </div>
                                <div class="space-y-1 text-slate-300 text-[11px] leading-relaxed pt-1 min-w-[240px]">
                                    <p class="text-indigo-400">const <span class="text-white font-semibold">developer</span> = &#123;</p>
                                    <p class="pl-4">name: <span class="text-emerald-400">'{{ $personalInfo['name'] }}'</span>,</p>
                                    <p class="pl-4">stack: [<span class="text-amber-300">'React 19'</span>, <span class="text-amber-300">'Next.js'</span>, <span class="text-amber-300">'TypeScript'</span>],</p>
                                    <p class="pl-4">focus: <span class="text-emerald-400">'High-Performance UX'</span>,</p>
                                    <p class="pl-4">coffeeLevel: <span class="text-purple-300">100</span>,</p>
                                    <p class="text-indigo-400">&#125;;</p>
                                </div>
                            </div>

                            {{-- Key Highlights Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <div class="p-3 sm:p-3.5 bg-white/60 backdrop-blur-md rounded-xl border border-white/80 shadow-2xs">
                                    <div class="flex items-center gap-2 text-indigo-600 mb-1">
                                        <x-icon name="cpu" class="w-4 h-4" />
                                        <span class="text-xs font-bold text-slate-700">Code Quality</span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        <span x-show="$store.lang.current === 'id'">TypeScript ketat &amp; modular</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Strict TS &amp; zero-lint errors</span>
                                    </p>
                                </div>

                                <div class="p-3 sm:p-3.5 bg-white/60 backdrop-blur-md rounded-xl border border-white/80 shadow-2xs">
                                    <div class="flex items-center gap-2 text-emerald-600 mb-1">
                                        <x-icon name="trending-up" class="w-4 h-4" />
                                        <span class="text-xs font-bold text-slate-700">Web Vitals</span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        <span x-show="$store.lang.current === 'id'">Sub-second page load</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Sub-second LCP &amp; 0 CLS</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Philosophy Quote --}}
                            <p class="text-xs text-slate-600 italic bg-white/50 backdrop-blur-md p-3 rounded-xl border border-white/70">
                                "<span x-show="$store.lang.current === 'id'">Kode yang hebat adalah kode yang mudah dipahami manusia, bukan hanya dimengerti mesin.</span><span x-show="$store.lang.current === 'en'" x-cloak>Clean code is code that speaks clearly to humans, not just machines.</span>"
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Metrics Bar --}}
        <div data-reveal class="mt-14 sm:mt-16 pt-8 border-t border-slate-200/60 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            <div class="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 hover:-translate-y-0.75 transition-all">
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $personalInfo['years_of_exp'] }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">
                    <span x-show="$store.lang.current === 'id'">Tahun Pengalaman</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Years Experience</span>
                </div>
            </div>

            <div class="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 hover:-translate-y-0.75 transition-all">
                <div class="text-2xl sm:text-3xl font-extrabold text-indigo-600 tracking-tight">{{ $personalInfo['completed_projects'] }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">
                    <span x-show="$store.lang.current === 'id'">Proyek Web Selesai</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Completed Projects</span>
                </div>
            </div>

            <div class="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 hover:-translate-y-0.75 transition-all">
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $personalInfo['client_satisfaction'] }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">
                    <span x-show="$store.lang.current === 'id'">Tingkat Kepuasan Klien</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Client Satisfaction</span>
                </div>
            </div>

            <div class="p-4 sm:p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 shadow-2xs hover:bg-white/80 hover:-translate-y-0.75 transition-all">
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $personalInfo['open_source_contributions'] }}</div>
                <div class="text-xs font-semibold text-slate-500 mt-0.5">
                    <span x-show="$store.lang.current === 'id'">Kontribusi Open Source</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>OSS Contributions</span>
                </div>
            </div>
        </div>
    </div>
</section>
