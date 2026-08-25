<section id="testimonials" class="py-20 sm:py-24 bg-transparent border-b border-white/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 sm:space-y-12">
        {{-- Header --}}
        <div data-reveal class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-amber-50/80 backdrop-blur-md text-amber-800 border border-amber-200 text-xs font-bold uppercase tracking-wider">
                <x-icon name="star" class="w-3.5 h-3.5 fill-amber-500 text-amber-500" />
                <span x-show="$store.lang.current === 'id'">Rekomendasi &amp; Testimoni</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Testimonials &amp; Endorsements</span>
            </div>
            {{-- Iterasi 21 (Fase 4, Bagian B): custom heading/subheading, fallback ke teks hardcoded kalau kosong — lihat docs/LOG-ITERASI.md entri Iterasi 21. --}}
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <span x-show="$store.lang.current === 'id'">{{ $topLevelSections['testimonials']->effective('heading_id', $appearancePreview ?? false) ?: 'Apa Kata Rekan Kerja, CTO & Klien Kolaborator' }}</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>{{ $topLevelSections['testimonials']->effective('heading_en', $appearancePreview ?? false) ?: 'What Engineering Leaders & Clients Say' }}</span>
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                <span x-show="$store.lang.current === 'id'">{{ $topLevelSections['testimonials']->effective('subheading_id', $appearancePreview ?? false) ?: 'Testimoni nyata dari para pemimpin teknologi dan founder yang telah berkolaborasi dalam proyek-proyek penting.' }}</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>{{ $topLevelSections['testimonials']->effective('subheading_en', $appearancePreview ?? false) ?: 'Endorsements from engineering leaders, product managers, and founders on delivered solutions.' }}</span>
            </p>
        </div>

        {{-- Testimonial Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($testimonials as $item)
                <div data-reveal id="testimonial-card-{{ $item->testimonial_key }}" class="bg-white/60 backdrop-blur-lg rounded-3xl p-6 sm:p-7 border border-white/80 shadow-2xs hover:shadow-xl hover:border-indigo-300/80 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between space-y-6 relative">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                @for ($i = 0; $i < $item->rating; $i++)
                                    <x-icon name="star" class="w-4 h-4 fill-amber-400 text-amber-400" />
                                @endfor
                            </div>
                            <span class="text-[11px] font-semibold text-indigo-700 bg-indigo-50/80 backdrop-blur-md px-2.5 py-0.5 rounded-md border border-indigo-100/80">{{ $item->project_tag }}</span>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic">"{{ $item->content }}"</p>
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-slate-200/50">
                        <img src="{{ $item->avatar }}" alt="{{ $item->name }}" width="44" height="44" loading="lazy" decoding="async" class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-2xs" />
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">{{ $item->name }}</h4>
                            <p class="text-xs text-slate-500">{{ $item->role }}</p>
                            <p class="text-[11px] font-semibold text-slate-400">{{ $item->company }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
