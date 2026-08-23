{{--
    Iterasi 10 (Fase 2): this partial is included unconditionally from the
    shared layout (`layouts.app`), which is now also rendered by controllers
    other than PortfolioController (e.g. ProjectPageController for /projects).
    Those controllers don't need $personalInfo/$skills/$experiences for their
    own page content, so the partial computes safe fallbacks itself instead
    of assuming every controller supplies them — keeps it self-contained
    without forcing every future controller to duplicate this query.
--}}
@php
    $personalInfo = $personalInfo ?? \App\Models\SiteProfile::current()->toArray();
    $skills = $skills ?? \App\Models\Skill::orderBy('sort_order')->get();
    $experiences = $experiences ?? \App\Models\Experience::orderBy('sort_order')->get();
@endphp
<div
    id="cv-modal-backdrop"
    x-show="$store.ui.cvOpen"
    x-transition:enter="transition ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="$store.ui.closeCv()"
    @keydown.escape.window="$store.ui.closeCv()"
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/70 backdrop-blur-xs overflow-y-auto"
    style="display: none;"
>
    <div
        id="cv-modal-dialog"
        x-show="$store.ui.cvOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95 translate-y-5"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        @click.stop
        class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden my-6 flex flex-col max-h-[90vh]"
    >
        {{-- Action Header --}}
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-2">
                <x-icon name="code2" class="w-5 h-5 text-indigo-400" />
                <span class="font-bold text-sm">Curriculum Vitae — {{ $personalInfo['name'] }}</span>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs">
                    <x-icon name="printer" class="w-3.5 h-3.5" />
                    <span x-show="$store.lang.current === 'id'">Cetak / Unduh PDF</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Print / Download PDF</span>
                </button>

                <button @click="$store.ui.closeCv()" class="p-1.5 text-slate-400 hover:text-white rounded-lg cursor-pointer" aria-label="Tutup">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>
        </div>

        {{-- Printable CV Body --}}
        <div class="p-8 sm:p-10 overflow-y-auto space-y-8 text-slate-800 font-sans">
            {{-- Header Resume Strip --}}
            <div class="border-b border-slate-200 pb-6 space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $personalInfo['name'] }}</h1>
                        <p class="text-sm font-bold text-indigo-600">{{ $personalInfo['title_id'] }} &bull; Senior Software Engineer</p>
                    </div>
                    <div class="text-xs text-slate-500 space-y-1 sm:text-right">
                        <div class="flex items-center sm:justify-end gap-1">
                            <x-icon name="mail" class="w-3.5 h-3.5 text-slate-400" />
                            <span>{{ $personalInfo['email'] }}</span>
                        </div>
                        <div class="flex items-center sm:justify-end gap-1">
                            <x-icon name="map-pin" class="w-3.5 h-3.5 text-slate-400" />
                            <span>{{ $personalInfo['location'] }}</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed pt-2">{{ $personalInfo['bio_id'] }}</p>
            </div>

            {{-- Core Competencies / Skills --}}
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tech Stack &amp; Keahlian Utama</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach ($skills->take(8) as $s)
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                            <div class="font-bold text-slate-800">{{ $s->name }}</div>
                            <div class="text-[10px] text-indigo-600 font-semibold">{{ $s->experience }} exp</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Experience --}}
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <x-icon name="briefcase" class="w-3.5 h-3.5 text-indigo-600" />
                    Pengalaman Kerja Profesional
                </h3>
                <div class="space-y-5">
                    @foreach ($experiences as $exp)
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $exp->role }}</h4>
                                    <p class="text-xs font-semibold text-slate-700">{{ $exp->company }} &bull; {{ $exp->location }}</p>
                                </div>
                                <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">{{ $exp->period }}</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $exp->description }}</p>
                            <ul class="space-y-1 pt-1">
                                @foreach ($exp->achievements as $ach)
                                    <li class="text-[11px] text-slate-600 flex items-start gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-1 shrink-0"></span>
                                        <span>{{ $ach }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Education --}}
            <div class="space-y-2 pt-2 border-t border-slate-200">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <x-icon name="graduation-cap" class="w-3.5 h-3.5 text-indigo-600" />
                    Pendidikan &amp; Sertifikasi
                </h3>
                <div class="flex justify-between items-start text-xs">
                    <div>
                        <div class="font-bold text-slate-900">S.Kom dalam Teknik Informatika / Ilmu Komputer</div>
                        <div class="text-slate-600">Universitas Indonesia (GPA: 3.84 / 4.00)</div>
                    </div>
                    <span class="font-mono text-slate-500">2015 — 2019</span>
                </div>
            </div>
        </div>
    </div>
</div>
