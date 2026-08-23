@extends('admin.layouts.app')

@section('title', 'Projects')

@section('content')
    <div data-reveal class="flex items-center justify-between gap-3">
        <div class="space-y-1">
            <h2 class="text-xl font-extrabold text-slate-900">Projects</h2>
            <p class="text-sm text-slate-500">Showcase studi kasus proyek yang tampil di section Projects halaman publik.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors">
            <x-icon name="sparkles" class="w-4 h-4" />
            Tambah Project
        </a>
    </div>

    {{-- Search & Filter --}}
    <form data-reveal method="GET" action="{{ route('admin.projects') }}" class="bg-white/60 backdrop-blur-xl rounded-3xl p-4 sm:p-5 border border-white/80 shadow-2xs flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <x-icon name="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul project..." class="w-full pl-10 pr-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
        </div>
        <select name="category" class="px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}" @selected($categoryFilter === $cat)>{{ $cat }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl transition-colors cursor-pointer">Filter</button>
        @if ($search || $categoryFilter)
            <a href="{{ route('admin.projects') }}" class="px-5 py-2.5 bg-white/70 hover:bg-white text-slate-600 text-sm font-bold rounded-xl border border-white/90 transition-colors text-center">Reset</a>
        @endif
    </form>

    <div
        data-reveal
        x-data="{ modalOpen: false, deleteUrl: '', deleteLabel: '' }"
        class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs overflow-hidden"
    >
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70">
            <h3 class="text-sm font-extrabold text-slate-900">Daftar Project ({{ $projects->total() }})</h3>
        </div>

        @if ($projects->isEmpty())
            <div class="py-16 text-center text-sm text-slate-400">Tidak ada project yang cocok dengan filter ini.</div>
        @else
            <ul class="divide-y divide-slate-200/70">
                @foreach ($projects as $project)
                    <li class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="flex flex-col shrink-0">
                                <form method="POST" action="{{ route('admin.projects.move', $project) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="direction" value="up" />
                                    <button type="submit" class="block text-slate-400 hover:text-indigo-600 cursor-pointer p-0.5" title="Naikkan urutan">
                                        <x-icon name="arrow-up" class="w-3.5 h-3.5" />
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.projects.move', $project) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="direction" value="down" />
                                    <button type="submit" class="block text-slate-400 hover:text-indigo-600 cursor-pointer p-0.5" title="Turunkan urutan">
                                        <x-icon name="arrow-up" class="w-3.5 h-3.5 rotate-180" />
                                    </button>
                                </form>
                            </div>

                            <img src="{{ $project->image }}" alt="" class="w-14 h-14 rounded-xl object-cover border border-white/90 shadow-2xs bg-slate-100 shrink-0" />

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-slate-800 truncate">{{ $project->title }}</span>
                                    @if ($project->featured)
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-200 shrink-0 flex items-center gap-1">
                                            <x-icon name="sparkles" class="w-2.5 h-2.5" /> Featured
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 truncate">{{ $project->category }} &bull; <span class="font-mono">#{{ $project->project_key }}</span></div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50/80 rounded-lg transition-colors" title="Edit">
                                <x-icon name="file-code" class="w-4 h-4" />
                            </a>
                            <button
                                type="button"
                                @click="modalOpen = true; deleteUrl = '{{ route('admin.projects.destroy', $project) }}'; deleteLabel = '{{ addslashes($project->title) }}'"
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer"
                                title="Hapus"
                            >
                                <x-icon name="x" class="w-4 h-4" />
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="px-5 sm:px-6 py-4 border-t border-slate-200/70">
                {{ $projects->links() }}
            </div>
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
                    <h4 class="font-bold text-slate-900 text-sm">Hapus project ini?</h4>
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
