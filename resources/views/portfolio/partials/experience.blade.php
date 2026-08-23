<section id="experience" class="py-20 sm:py-24 bg-transparent border-b border-white/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-14">
        {{-- Section Header --}}
        <div data-reveal class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-indigo-50/80 backdrop-blur-md text-indigo-700 border border-indigo-100 text-xs font-bold uppercase tracking-wider">
                <x-icon name="briefcase" class="w-3.5 h-3.5" />
                <span x-show="$store.lang.current === 'id'">Karier &amp; Pengalaman</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Career &amp; Experience</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <span x-show="$store.lang.current === 'id'">Jejak Rekam Profesional &amp; Kepemimpinan Teknis</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Professional Journey &amp; Technical Leadership</span>
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                <span x-show="$store.lang.current === 'id'">Perjalanan saya dalam mengembangkan ekosistem web berskala besar, memimpin tim engineering, dan menyelesaikan tantangan arsitektur kompleks.</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Over 6 years of architecting scalable web applications, mentoring development teams, and shipping production-grade software.</span>
            </p>
        </div>

        {{-- Timeline List --}}
        <div class="relative border-l-2 border-indigo-200/80 ml-3 sm:ml-8 space-y-8 sm:space-y-12 pl-5 sm:pl-10">
            @foreach ($experiences as $exp)
                <div data-reveal id="experience-item-{{ $exp->experience_key }}" class="relative group">
                    {{-- Timeline Indicator Dot --}}
                    <div class="absolute -left-[31px] sm:-left-[51px] top-2 w-5 sm:w-6 h-5 sm:h-6 rounded-full bg-white/95 backdrop-blur-md border-3 sm:border-4 border-indigo-600 shadow-sm group-hover:scale-125 group-hover:border-indigo-500 transition-transform duration-300"></div>

                    <div class="bg-white/60 backdrop-blur-lg rounded-3xl p-5 sm:p-8 border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 hover:-translate-y-1 transition-all duration-300 space-y-4">
                        {{-- Role Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold font-mono text-indigo-600 tracking-wide uppercase">{{ $exp->period }}</span>
                                <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5">{{ $exp->role }}</h3>
                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs text-slate-500 mt-1">
                                    <span class="font-bold text-slate-800">{{ $exp->company }}</span>
                                    <span>&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <x-icon name="map-pin" class="w-3 h-3 text-slate-400" />
                                        {{ $exp->location }}
                                    </span>
                                    <span>&bull;</span>
                                    <span class="px-2 py-0.5 bg-white/70 border border-white/80 text-slate-700 rounded-md font-semibold text-[11px] shadow-2xs">{{ $exp->type }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $exp->description }}</p>

                        {{-- Key Achievements --}}
                        <div class="space-y-2 pt-1">
                            <h4 class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span x-show="$store.lang.current === 'id'">Pencapaian Kunci:</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Key Achievements:</span>
                            </h4>
                            <div class="space-y-2">
                                @foreach ($exp->achievements as $ach)
                                    <div class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-700">
                                        <x-icon name="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" />
                                        <span>{{ $ach }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tech Stack Pills --}}
                        <div class="pt-2 flex flex-wrap gap-1.5">
                            @foreach ($exp->skills as $sk)
                                <span class="px-2.5 py-1 bg-white/70 backdrop-blur-xs text-slate-700 rounded-md text-[11px] font-semibold border border-white/60 shadow-2xs">{{ $sk }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
