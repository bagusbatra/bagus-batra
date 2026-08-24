<footer id="main-footer" class="bg-slate-900/90 backdrop-blur-xl text-slate-400 py-16 border-t border-slate-800/80 relative">
    <div data-reveal class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            {{-- Brand Col --}}
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600/90 backdrop-blur-md flex items-center justify-center text-white shadow-sm shadow-indigo-500/30 border border-indigo-400/30">
                        <x-icon name="code2" class="w-5 h-5" />
                    </div>
                    <div class="font-bold text-white text-lg tracking-tight">
                        <span>Bagus</span>
                        <span class="text-indigo-400">.dev</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                    <span x-show="$store.lang.current === 'id'">Senior Web Developer &amp; Technical Writer berfokus pada arsitektur frontend skala enterprise, optimasi performa web, dan antarmuka elegan.</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Senior Web Developer crafting performant, accessible, and elegant web solutions for global products.</span>
                </p>
                <div class="text-xs text-slate-500 font-mono">
                    Jakarta / Bali, ID &bull; {{ date('Y') }}
                </div>
            </div>

            {{-- Quick Nav Links --}}
            <div class="md:col-span-4 grid grid-cols-2 gap-4 text-xs">
                <div class="space-y-2.5">
                    <div class="font-bold text-slate-200 uppercase tracking-wider text-[11px]">Navigasi</div>
                    <ul class="space-y-2">
                        <li><a href="#hero" class="hover:text-indigo-400 transition-colors">Beranda</a></li>
                        <li><a href="#about" class="hover:text-indigo-400 transition-colors">Tentang &amp; Prinsip</a></li>
                        <li><a href="#skills" class="hover:text-indigo-400 transition-colors">Tech Stack</a></li>
                        <li><a href="#projects" class="hover:text-indigo-400 transition-colors">Showcase Proyek</a></li>
                    </ul>
                </div>
                <div class="space-y-2.5">
                    <div class="font-bold text-slate-200 uppercase tracking-wider text-[11px]">Eksplorasi</div>
                    <ul class="space-y-2">
                        <li><a href="#experience" class="hover:text-indigo-400 transition-colors">Pengalaman</a></li>
                        <li><a href="#blog" class="hover:text-indigo-400 transition-colors">Artikel &amp; Blog</a></li>
                        <li><a href="#contact" class="hover:text-indigo-400 transition-colors">Kontak &amp; Brief</a></li>
                    </ul>
                </div>
            </div>

            {{-- Back to top & Tech badge --}}
            <div class="md:col-span-3 flex flex-col items-start md:items-end justify-between space-y-4">
                <button id="back-to-top-btn" @click="scrollToTop()" class="flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 backdrop-blur-md text-slate-200 text-xs font-bold rounded-xl border border-white/10 transition-all cursor-pointer shadow-sm hover:scale-105">
                    <x-icon name="arrow-up" class="w-3.5 h-3.5" />
                    <span x-show="$store.lang.current === 'id'">Kembali ke Atas</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Back to Top</span>
                </button>

                <div class="text-[11px] text-slate-500 md:text-right">
                    Built with Laravel, Alpine.js &amp; Tailwind CSS.
                </div>
            </div>
        </div>

        {{-- Bottom copyright line --}}
        <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>&copy; {{ date('Y') }} Bagus Batra. All rights reserved.</div>
            <div class="flex items-center gap-1">
                <span>Crafted with</span>
                <x-icon name="heart" class="w-3.5 h-3.5 text-rose-500 fill-rose-500" />
                <span>and obsessive attention to detail.</span>
            </div>
        </div>
    </div>
</footer>
