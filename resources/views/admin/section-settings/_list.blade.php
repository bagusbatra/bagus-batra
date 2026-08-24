{{--
    Iterasi 1 (Fase 1): daftar on/off + urutan section publik.
    Iterasi 18 (Fase 4): diekstrak dari admin/section-settings/index.blade.php
    jadi partial supaya bisa dipakai ulang di tab "Urutan & Isi Section" pada
    menu baru "Tampilan Halaman Index" (admin/appearance/index.blade.php)
    TANPA duplikasi markup/logic. Route lama (admin.section-settings) tetap
    ada & @include partial ini juga, jadi kedua tempat selalu sinkron.

    Membutuhkan $sections (Collection<SectionSetting>) dari controller.
--}}
@php
    $iconMap = [
        'hero' => 'layout',
        'about' => 'user',
        'skills' => 'cpu',
        'projects' => 'folder-git-2',
        'playground' => 'zap',
        'experience' => 'briefcase',
        'blog' => 'book-open',
        'testimonials' => 'quote',
        'contact' => 'mail',
    ];
@endphp

<div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 flex items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-extrabold text-slate-900">Daftar Section ({{ $sections->count() }})</h3>
            <p class="text-xs text-slate-500 mt-0.5">Urutan sesuai tampilan di halaman index. Navbar &amp; Footer selalu tampil (tidak bisa dimatikan).</p>
        </div>
    </div>

    <ul class="divide-y divide-slate-200/70">
        @foreach ($sections as $section)
            <li
                data-reveal
                x-data="sectionToggle({{ $section->id }}, {{ $section->is_active ? 'true' : 'false' }})"
                class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 transition-colors"
                :class="active ? '' : 'bg-slate-50/60'"
            >
                <div class="flex items-center gap-3.5 min-w-0">
                    <span class="text-[11px] font-mono font-bold text-slate-400 w-5 text-right shrink-0">{{ $section->sort_order }}</span>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center border shrink-0 transition-colors" :class="active ? 'bg-indigo-50/80 text-indigo-600 border-indigo-100/60' : 'bg-slate-100 text-slate-400 border-slate-200'">
                        <x-icon :name="$iconMap[$section->section_key] ?? 'layout'" class="w-5 h-5" />
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-slate-800 truncate">{{ $section->label }}</div>
                        <div class="text-[11px] text-slate-400 font-mono">#{{ $section->section_key }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    {{-- Inline save feedback --}}
                    <span
                        x-show="feedback"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-x-1"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="text-[11px] font-bold"
                        :class="feedback === 'ok' ? 'text-emerald-600' : 'text-rose-600'"
                        x-cloak
                    >
                        <span x-show="feedback === 'ok'">Tersimpan</span>
                        <span x-show="feedback === 'error'">Gagal, dicoba lagi</span>
                    </span>

                    <span class="text-xs font-bold w-16 text-right" :class="active ? 'text-emerald-600' : 'text-slate-400'" x-text="active ? 'Aktif' : 'Nonaktif'"></span>

                    {{-- Modern pill switch — style dipakai ulang dari toggle "Backdrop Blur" di Playground publik --}}
                    <button
                        type="button"
                        @click="toggle()"
                        :disabled="loading"
                        :aria-pressed="active"
                        :aria-label="'Toggle section ' + '{{ $section->label }}'"
                        class="w-11 h-6 rounded-full transition-colors relative p-0.5 shrink-0 disabled:opacity-60"
                        :class="active ? 'bg-indigo-600' : 'bg-slate-300'"
                    >
                        <div class="w-5 h-5 rounded-full bg-white transition-transform shadow-sm" :class="active ? 'translate-x-5' : 'translate-x-0'"></div>
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
</div>

<p data-reveal class="text-xs text-slate-400 text-center">Section yang dimatikan langsung hilang dari halaman publik tanpa perlu deploy ulang. Reorder drag-drop, jumlah item per section, dan custom heading/subheading direncanakan Iterasi 20-21 (Fase 4) — lihat docs/RENCANA-KUSTOMISASI-TAMPILAN.md.</p>
