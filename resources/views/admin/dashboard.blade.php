@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div data-reveal class="space-y-1">
        <h2 class="text-xl font-extrabold text-slate-900">Selamat datang, {{ auth()->user()->name }} 👋</h2>
        <p class="text-sm text-slate-500">Ringkasan konten portfolio dan status section publik saat ini.</p>
    </div>

    {{-- Stat cards --}}
    @php
        $cards = [
            ['label' => 'Projects', 'value' => $stats['projects'], 'icon' => 'folder-git-2', 'color' => 'indigo'],
            ['label' => 'Blog Posts', 'value' => $stats['blog_posts'], 'icon' => 'book-open', 'color' => 'blue'],
            ['label' => 'Experience', 'value' => $stats['experiences'], 'icon' => 'briefcase', 'color' => 'emerald'],
            ['label' => 'Testimonials', 'value' => $stats['testimonials'], 'icon' => 'quote', 'color' => 'amber'],
            ['label' => 'Skills', 'value' => $stats['skills'], 'icon' => 'cpu', 'color' => 'purple'],
            ['label' => 'Pesan Masuk', 'value' => $stats['contact_messages'], 'icon' => 'mail', 'color' => 'rose'],
        ];
        $colorMap = [
            'indigo' => 'bg-indigo-50/80 text-indigo-600 border-indigo-100/60',
            'blue' => 'bg-blue-50/80 text-blue-600 border-blue-100/60',
            'emerald' => 'bg-emerald-50/80 text-emerald-600 border-emerald-100/60',
            'amber' => 'bg-amber-50/80 text-amber-600 border-amber-100/60',
            'purple' => 'bg-purple-50/80 text-purple-600 border-purple-100/60',
            'rose' => 'bg-rose-50/80 text-rose-600 border-rose-100/60',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @foreach ($cards as $card)
            <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 border border-white/80 shadow-2xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center border shrink-0 {{ $colorMap[$card['color']] }}">
                    <x-icon :name="$card['icon']" class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-slate-900 tabular-nums">{{ $card['value'] }}</div>
                    <div class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Section status --}}
    <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Status Section Halaman Publik</h3>
                <p class="text-xs text-slate-500 mt-0.5">Read-only di Iterasi 0 — saklar akan berfungsi mulai Iterasi 1 (Pengaturan Section).</p>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200">Read-only</span>
        </div>
        <ul class="divide-y divide-slate-200/70">
            @foreach ($sections as $section)
                <li class="flex items-center justify-between px-5 sm:px-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="text-[11px] font-mono font-bold text-slate-400 w-5 text-right shrink-0">{{ $section->sort_order }}</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $section->label }}</span>
                        <span class="text-[11px] text-slate-400 font-mono">#{{ $section->section_key }}</span>
                    </div>
                    <span class="flex items-center gap-1.5 text-xs font-bold {{ $section->is_active ? 'text-emerald-600' : 'text-slate-400' }}">
                        <span class="w-2 h-2 rounded-full {{ $section->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                        {{ $section->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
