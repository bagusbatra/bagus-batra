@php
    $navLinks = [
        ['id' => 'hero', 'id_label' => 'Beranda', 'en_label' => 'Home'],
        ['id' => 'about', 'id_label' => 'Tentang', 'en_label' => 'About'],
        ['id' => 'projects', 'id_label' => 'Proyek', 'en_label' => 'Projects'],
        ['id' => 'experience', 'id_label' => 'Pengalaman', 'en_label' => 'Experience'],
        ['id' => 'blog', 'id_label' => 'Artikel Blog', 'en_label' => 'Blog'],
        ['id' => 'contact', 'id_label' => 'Kontak', 'en_label' => 'Contact'],
    ];
@endphp

<header
    id="main-navbar-header"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="isScrolled ? 'py-3 bg-white/65 backdrop-blur-xl shadow-xs border-b border-white/80' : 'py-4 bg-white/40 backdrop-blur-md border-b border-white/50'"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            {{-- Logo / Brand Name --}}
            <button id="brand-logo-btn" @click="scrollTo('hero')" class="flex items-center gap-2.5 group text-left cursor-pointer focus:outline-none">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center text-white shadow-sm shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                    <x-icon name="code2" class="w-5 h-5" />
                </div>
                <div>
                    <div class="flex items-center gap-1.5 font-bold text-slate-900 text-lg tracking-tight group-hover:text-indigo-600 transition-colors">
                        <span>Bagus</span>
                        <span class="text-indigo-600">.dev</span>
                    </div>
                    <p class="text-[11px] text-slate-700 hidden sm:block font-medium">
                        Web Developer &amp; Architect
                    </p>
                </div>
            </button>

            {{-- Desktop Nav Links --}}
            <nav id="desktop-navigation" aria-label="Navigasi Utama" class="hidden md:flex items-center gap-1 bg-white/50 p-1.5 rounded-full border border-white/80 shadow-2xs backdrop-blur-md relative">
                @foreach ($navLinks as $link)
                    <button
                        id="nav-link-{{ $link['id'] }}"
                        @click="scrollTo('{{ $link['id'] }}')"
                        class="relative px-3.5 py-1.5 rounded-full text-xs font-semibold transition-colors duration-200 cursor-pointer"
                        :class="activeSection === '{{ $link['id'] }}' ? 'text-indigo-600 font-bold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/40'"
                    >
                        <span
                            x-show="activeSection === '{{ $link['id'] }}'"
                            x-transition
                            class="absolute inset-0 bg-white/95 rounded-full shadow-2xs border border-white/90 -z-10"
                        ></span>
                        <span x-show="$store.lang.current === 'id'">{{ $link['id_label'] }}</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>{{ $link['en_label'] }}</span>
                    </button>
                @endforeach
            </nav>

            {{-- Action Buttons & Language Switcher --}}
            <div class="hidden lg:flex items-center gap-2.5">
                <button
                    id="lang-toggle-btn"
                    @click="$store.lang.toggle()"
                    class="flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-indigo-600 hover:bg-white/80 rounded-lg transition-colors border border-white/80 bg-white/50 backdrop-blur-md shadow-2xs cursor-pointer hover:scale-105 active:scale-95"
                    :title="$store.lang.current === 'id' ? 'Ganti ke Bahasa Inggris' : 'Switch to Indonesian'"
                >
                    <x-icon name="globe" class="w-3.5 h-3.5 text-slate-500" />
                    <span class="uppercase" x-text="$store.lang.current"></span>
                </button>

                <button id="nav-cv-download-btn" @click="$store.ui.openCv()" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:text-indigo-600 hover:bg-white/80 rounded-lg transition-colors border border-white/80 bg-white/50 backdrop-blur-md shadow-2xs cursor-pointer hover:scale-105 active:scale-95">
                    <x-icon name="file-down" class="w-3.5 h-3.5" />
                    <span>CV</span>
                </button>

                <button id="nav-hire-me-btn" @click="scrollTo('contact')" class="flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold text-white bg-slate-900 hover:bg-indigo-600 rounded-lg transition-all duration-200 shadow-xs hover:shadow-indigo-500/20 cursor-pointer hover:scale-105 active:scale-95">
                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-indigo-300" />
                    <span x-show="$store.lang.current === 'id'">Rekrut Saya</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Hire Me</span>
                </button>
            </div>

            {{-- Mobile Menu & Quick Lang Toggle --}}
            <div class="flex items-center gap-2 md:hidden">
                <button id="mobile-lang-btn" @click="$store.lang.toggle()" class="p-2 text-xs font-bold text-slate-700 bg-white/70 backdrop-blur-md rounded-lg border border-white/80 shadow-2xs cursor-pointer active:scale-90">
                    <span class="uppercase" x-text="$store.lang.current"></span>
                </button>

                <button id="mobile-menu-toggle-btn" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2.5 rounded-xl text-slate-700 hover:text-slate-900 hover:bg-white/80 bg-white/60 backdrop-blur-md border border-white/80 shadow-2xs focus:outline-none cursor-pointer active:scale-90" aria-label="Toggle menu">
                    <x-icon name="menu" class="w-5 h-5" x-show="!mobileMenuOpen" />
                    <x-icon name="x" class="w-5 h-5" x-show="mobileMenuOpen" x-cloak />
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Drawer Menu --}}
    <div
        id="mobile-nav-drawer"
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden bg-white/90 backdrop-blur-2xl border-b border-white/80 px-4 py-5 shadow-xl max-h-[80vh] overflow-y-auto"
        style="display: none;"
    >
        <div class="flex flex-col space-y-1">
            @foreach ($navLinks as $link)
                <button
                    id="mobile-link-{{ $link['id'] }}"
                    @click="scrollTo('{{ $link['id'] }}')"
                    class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-colors cursor-pointer"
                    :class="activeSection === '{{ $link['id'] }}' ? 'bg-white text-indigo-600 font-bold border border-indigo-100 shadow-2xs' : 'text-slate-700 hover:bg-white/60'"
                >
                    <span x-show="$store.lang.current === 'id'">{{ $link['id_label'] }}</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>{{ $link['en_label'] }}</span>
                    <span x-show="activeSection === '{{ $link['id'] }}'" class="w-2 h-2 rounded-full bg-indigo-600 shadow-xs shadow-indigo-500/50"></span>
                </button>
            @endforeach
        </div>

        <div class="pt-4 mt-3 border-t border-slate-200/60 flex flex-col gap-2.5">
            <button id="mobile-cv-btn" @click="mobileMenuOpen = false; $store.ui.openCv()" class="w-full flex items-center justify-center gap-2 py-3 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl shadow-2xs cursor-pointer">
                <x-icon name="file-down" class="w-4 h-4 text-indigo-600" />
                <span x-show="$store.lang.current === 'id'">Unduh Resume / CV (PDF)</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Download CV (PDF)</span>
            </button>
            <button id="mobile-contact-cta-btn" @click="scrollTo('contact')" class="w-full flex items-center justify-center gap-2 py-3 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-600/20 cursor-pointer">
                <x-icon name="sparkles" class="w-4 h-4 text-indigo-200" />
                <span x-show="$store.lang.current === 'id'">Mulai Diskusi Proyek</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Start Project Discussion</span>
            </button>
        </div>
    </div>
</header>
