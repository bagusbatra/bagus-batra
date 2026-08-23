@extends('admin.layouts.app')

@section('title', 'About & Skills')

@section('content')
    @php
        $categoryLabels = ['frontend' => 'Frontend', 'backend' => 'Backend', 'devops' => 'DevOps & Cloud', 'tools' => 'Tools & UI'];
        $categoryColors = ['frontend' => 'bg-indigo-50/80 text-indigo-600 border-indigo-100/60', 'backend' => 'bg-emerald-50/80 text-emerald-600 border-emerald-100/60', 'devops' => 'bg-amber-50/80 text-amber-600 border-amber-100/60', 'tools' => 'bg-purple-50/80 text-purple-600 border-purple-100/60'];
        // Same 12-icon map used to render skills publicly (about.blade.php's
        // $skillIconMap) — kept identical so the admin preview always
        // matches what actually renders on the public page.
        $skillIconMap = [
            'Code2' => 'code2', 'FileCode' => 'file-code', 'Palette' => 'palette', 'Sparkles' => 'sparkles',
            'Layers' => 'layers', 'Server' => 'server', 'Database' => 'database', 'Cpu' => 'cpu',
            'Cloud' => 'cloud', 'GitBranch' => 'git-branch', 'Layout' => 'layout', 'Zap' => 'zap',
        ];
    @endphp

    <div data-reveal class="flex items-center justify-between gap-3">
        <div class="space-y-1">
            <h2 class="text-xl font-extrabold text-slate-900">About & Skills</h2>
            <p class="text-sm text-slate-500">Grid keahlian &amp; tech stack yang tampil di section About / Tech Stack Matrix halaman publik.</p>
        </div>
        <a href="{{ route('admin.about-skills.create') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors">
            <x-icon name="sparkles" class="w-4 h-4" />
            Tambah Skill
        </a>
    </div>

    <div data-reveal class="p-4 rounded-2xl bg-indigo-50/70 backdrop-blur-md border border-indigo-100/70 text-xs text-indigo-800 flex items-start gap-2.5">
        <x-icon name="award" class="w-4 h-4 shrink-0 mt-0.5" />
        <span>4 kartu "Prinsip Kerja" (Performance-First, Aksesibilitas, dst) di atas grid Skills pada halaman publik masih konten statis di Blade — bukan bagian dari CRUD ini. Lihat catatan keputusan di <code class="font-mono">docs/LOG-ITERASI.md</code> (Iterasi 3).</span>
    </div>

    {{-- Search & Filter --}}
    <form data-reveal method="GET" action="{{ route('admin.about-skills') }}" class="bg-white/60 backdrop-blur-xl rounded-3xl p-4 sm:p-5 border border-white/80 shadow-2xs flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <x-icon name="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama skill..." class="w-full pl-10 pr-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
        </div>
        <select name="category" class="px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $value => $categoryLabel)
                <option value="{{ $value }}" @selected($categoryFilter === $value)>{{ $categoryLabel }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl transition-colors cursor-pointer">Filter</button>
        @if ($search || $categoryFilter)
            <a href="{{ route('admin.about-skills') }}" class="px-5 py-2.5 bg-white/70 hover:bg-white text-slate-600 text-sm font-bold rounded-xl border border-white/90 transition-colors text-center">Reset</a>
        @endif
    </form>

    <div
        data-reveal
        x-data="{ modalOpen: false, deleteUrl: '', deleteLabel: '' }"
        class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs overflow-hidden"
    >
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70">
            <h3 class="text-sm font-extrabold text-slate-900">Daftar Skill ({{ $skills->count() }})</h3>
        </div>

        @if ($skills->isEmpty())
            <div class="py-16 text-center text-sm text-slate-400">{{ $search || $categoryFilter ? 'Tidak ada skill yang cocok dengan filter ini.' : 'Belum ada skill. Klik "Tambah Skill" untuk menambahkan.' }}</div>
        @else
            <ul class="divide-y divide-slate-200/70">
                @foreach ($skills as $skill)
                    <li class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="flex flex-col shrink-0">
                                <form method="POST" action="{{ route('admin.about-skills.move', $skill) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="direction" value="up" />
                                    <button type="submit" @if($skill->sort_order <= $minSortOrder) disabled @endif class="block text-slate-400 hover:text-indigo-600 disabled:opacity-25 disabled:cursor-not-allowed cursor-pointer p-0.5" title="Naikkan urutan">
                                        <x-icon name="arrow-up" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.about-skills.move', $skill) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="direction" value="down" />
                                    <button type="submit" @if($skill->sort_order >= $maxSortOrder) disabled @endif class="block text-slate-400 hover:text-indigo-600 disabled:opacity-25 disabled:cursor-not-allowed cursor-pointer p-0.5" title="Turunkan urutan">
                                        <x-icon name="arrow-up" class="w-3.5 h-3.5 rotate-180" />
                                    </button>
                                </form>
                            </div>

                            <div class="w-10 h-10 rounded-xl bg-indigo-50/80 text-indigo-600 flex items-center justify-center border border-indigo-100/60 shrink-0">
                                <x-icon :name="$skillIconMap[$skill->icon_name] ?? 'code2'" class="w-5 h-5" />
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-slate-800 truncate">{{ $skill->name }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-full border shrink-0 {{ $categoryColors[$skill->category] ?? 'bg-slate-100 text-slate-500 border-slate-200' }}">{{ $categoryLabels[$skill->category] ?? $skill->category }}</span>
                                </div>
                                <div class="text-xs text-slate-500 truncate">Level {{ $skill->level }}% &bull; {{ $skill->experience }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('admin.about-skills.edit', $skill) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50/80 rounded-lg transition-colors" title="Edit">
                                <x-icon name="file-code" class="w-4 h-4" />
                            </a>
                            <button
                                type="button"
                                @click="modalOpen = true; deleteUrl = '{{ route('admin.about-skills.destroy', $skill) }}'; deleteLabel = '{{ addslashes($skill->name) }}'"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer"
                                title="Hapus"
                            >
                                <x-icon name="x" class="w-4 h-4" />
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Shared delete-confirmation modal --}}
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="modalOpen = false"
            @keydown.escape.window="modalOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            style="display: none;"
            x-cloak
        >
            <div
                @click.stop
                x-show="modalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-slate-200 space-y-4"
            >
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100">
                    <x-icon name="x" class="w-5 h-5" />
                </div>
                <div class="space-y-1">
                    <h4 class="font-bold text-slate-900 text-sm">Hapus skill ini?</h4>
                    <p class="text-xs text-slate-500">Anda akan menghapus <strong x-text="deleteLabel"></strong> secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="flex items-center gap-2.5 pt-1">
                    <button @click="modalOpen = false" type="button" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">Batal</button>
                    <form method="POST" :action="deleteUrl" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
