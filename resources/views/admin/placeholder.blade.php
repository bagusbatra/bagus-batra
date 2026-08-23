@extends('admin.layouts.app')

@section('title', $title)

@section('content')
    <div data-reveal class="flex flex-col items-center justify-center text-center py-20 sm:py-28 bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs space-y-4">
        <div class="w-14 h-14 rounded-2xl bg-indigo-50/80 text-indigo-600 flex items-center justify-center border border-indigo-100/60">
            <x-icon name="rotate-ccw" class="w-7 h-7" />
        </div>
        <div class="space-y-1.5">
            <h2 class="text-lg font-extrabold text-slate-900">{{ $title }} — Segera Hadir</h2>
            <p class="text-sm text-slate-500 max-w-md mx-auto">{{ $iterationNote }}.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-3.5 h-3.5" />
            Kembali ke Dashboard
        </a>
    </div>
@endsection
