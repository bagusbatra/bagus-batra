@extends('admin.layouts.app')

@section('title', 'Pesan Masuk')

@section('content')
    <div data-reveal class="flex items-center justify-between gap-3">
        <div class="space-y-1">
            <h2 class="text-xl font-extrabold text-slate-900">Pesan Masuk</h2>
            <p class="text-sm text-slate-500">Pesan dari form kontak halaman publik. Buka pesan untuk menandainya dibaca otomatis.</p>
        </div>
        @if ($unreadCount > 0)
            <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                {{ $unreadCount }} belum dibaca
            </span>
        @endif
    </div>

    {{-- Filter --}}
    <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-4 sm:p-5 border border-white/80 shadow-2xs flex items-center gap-1.5">
        <a href="{{ route('admin.messages', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $status === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Semua</a>
        <a href="{{ route('admin.messages', ['status' => 'unread']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $status === 'unread' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Belum Dibaca</a>
    </div>

    <div
        data-reveal
        x-data="{ modalOpen: false, deleteUrl: '', deleteLabel: '' }"
        class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs overflow-hidden"
    >
        <div class="px-5 sm:px-6 py-4 border-b border-slate-200/70">
            <h3 class="text-sm font-extrabold text-slate-900">Daftar Pesan ({{ $messages->total() }})</h3>
        </div>

        @if ($messages->isEmpty())
            <div class="py-16 text-center text-sm text-slate-400">
                {{ $status === 'unread' ? 'Tidak ada pesan yang belum dibaca.' : 'Belum ada pesan masuk.' }}
            </div>
        @else
            <ul class="divide-y divide-slate-200/70">
                @foreach ($messages as $msg)
                    <li class="flex items-center justify-between gap-4 px-5 sm:px-6 py-4 {{ ! $msg->is_read ? 'bg-indigo-50/40' : '' }}">
                        <a href="{{ route('admin.messages.show', $msg) }}" class="flex items-center gap-3.5 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center border shrink-0 {{ ! $msg->is_read ? 'bg-indigo-100 text-indigo-600 border-indigo-200' : 'bg-slate-100 text-slate-400 border-slate-200' }}">
                                <x-icon name="mail" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm {{ ! $msg->is_read ? 'font-extrabold text-slate-900' : 'font-semibold text-slate-700' }} truncate">{{ $msg->name }}</span>
                                    @if (! $msg->is_read)
                                        <span class="w-2 h-2 rounded-full bg-indigo-600 shrink-0"></span>
                                    @endif
                                    @if ($msg->project_type)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-white/80 text-slate-500 border border-slate-200 shrink-0">{{ $msg->project_type }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500 truncate">{{ $msg->email }} &bull; {{ \Illuminate\Support\Str::limit($msg->message, 80) }}</div>
                            </div>
                        </a>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-[11px] text-slate-400 font-mono hidden sm:block">{{ $msg->created_at->format('d M Y, H:i') }}</span>
                            <button
                                type="button"
                                @click="modalOpen = true; deleteUrl = '{{ route('admin.messages.destroy', $msg) }}'; deleteLabel = '{{ addslashes($msg->name) }}'"
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
                {{ $messages->links() }}
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
                    <h4 class="font-bold text-slate-900 text-sm">Hapus pesan ini?</h4>
                    <p class="text-xs text-slate-500">Anda akan menghapus pesan dari <strong x-text="deleteLabel"></strong> secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
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
