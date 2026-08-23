@extends('admin.layouts.app')

@section('title', 'Pesan dari '.$message->name)

@section('content')
    <div data-reveal class="flex items-center gap-3">
        <a href="{{ route('admin.messages') }}" class="p-2 text-slate-400 hover:text-slate-800 hover:bg-white/70 rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-4.5 h-4.5" />
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Pesan dari {{ $message->name }}</h2>
            <p class="text-sm text-slate-500">Dikirim {{ $message->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
    </div>

    <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-8 border border-white/80 shadow-2xs space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-white/70 rounded-2xl border border-white/90">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama</span>
                <span class="text-sm font-bold text-slate-800">{{ $message->name }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Email</span>
                <a href="mailto:{{ $message->email }}" class="text-sm font-bold text-indigo-600 hover:underline">{{ $message->email }}</a>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Proyek</span>
                <span class="text-sm font-bold text-slate-800">{{ $message->project_type ?: '—' }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Budget</span>
                <span class="text-sm font-bold text-slate-800">{{ $message->budget ?: '—' }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Timeline</span>
                <span class="text-sm font-bold text-slate-800">{{ $message->timeline ?: '—' }}</span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Status</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                    <x-icon name="check-circle-2" class="w-3.5 h-3.5" /> Sudah Dibaca
                </span>
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Isi Pesan</h3>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $message->message }}</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <a href="mailto:{{ $message->email }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors">
                <x-icon name="mail" class="w-4 h-4" />
                Balas via Email
            </a>
        </div>
    </div>
@endsection
