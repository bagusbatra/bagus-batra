@extends('admin.layouts.app')

@section('title', $socialLink->exists ? 'Edit Social Link' : 'Tambah Social Link')

@section('content')
    @php
        $iconOptions = ['Github' => 'GitHub', 'Linkedin' => 'LinkedIn', 'Twitter' => 'Twitter / X', 'Youtube' => 'YouTube', 'Instagram' => 'Instagram', 'Mail' => 'Email'];
    @endphp

    <div data-reveal class="flex items-center gap-3">
        <a href="{{ route('admin.social-links') }}" class="p-2 text-slate-400 hover:text-slate-800 hover:bg-white/70 rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-4.5 h-4.5" />
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $socialLink->exists ? 'Edit Social Link' : 'Tambah Social Link' }}</h2>
            <p class="text-sm text-slate-500">{{ $socialLink->exists ? 'Perbarui data '.$socialLink->name : 'Isi data platform media sosial baru.' }}</p>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $socialLink->exists ? route('admin.social-links.update', $socialLink) : route('admin.social-links.store') }}"
        class="space-y-6"
        x-data="{ isActive: {{ old('is_active', $socialLink->exists ? $socialLink->is_active : true) ? 'true' : 'false' }} }"
    >
        @csrf
        @if ($socialLink->exists) @method('PUT') @endif

        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Platform (identifier) *</label>
                    <input type="text" name="platform" required value="{{ old('platform', $socialLink->platform) }}" placeholder="GitHub" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('platform') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Nama Tampilan *</label>
                    <input type="text" name="name" required value="{{ old('name', $socialLink->name) }}" placeholder="GitHub" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('name') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">URL *</label>
                    <input type="text" name="url" required value="{{ old('url', $socialLink->url) }}" placeholder="https://github.com/username" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Username / Handle</label>
                    <input type="text" name="username" value="{{ old('username', $socialLink->username) }}" placeholder="@bagusbatra" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('username') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Ikon *</label>
                    <select name="icon" required class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
                        @foreach ($iconOptions as $value => $iconLabel)
                            <option value="{{ $value }}" @selected(old('icon', $socialLink->icon) === $value)>{{ $iconLabel }}</option>
                        @endforeach
                    </select>
                    @error('icon') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Deskripsi Singkat</label>
                    <input type="text" name="description" value="{{ old('description', $socialLink->description) }}" placeholder="30+ repositori publik & kontribusi open-source" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('description') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Kelas Warna Hover (Tailwind)</label>
                    <input type="text" name="bg_color" value="{{ old('bg_color', $socialLink->bg_color) }}" placeholder="hover:bg-slate-900 hover:text-white" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs font-mono text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('bg_color') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Kelas Warna Teks (Tailwind)</label>
                    <input type="text" name="text_color" value="{{ old('text_color', $socialLink->text_color) }}" placeholder="text-slate-800" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs font-mono text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('text_color') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-3 pt-1 select-none cursor-pointer w-fit">
                <input type="hidden" name="is_active" :value="isActive ? 1 : 0" />
                <button type="button" @click="isActive = !isActive" class="w-11 h-6 rounded-full transition-colors relative p-0.5 shrink-0" :class="isActive ? 'bg-indigo-600' : 'bg-slate-300'">
                    <div class="w-5 h-5 rounded-full bg-white transition-transform shadow-sm" :class="isActive ? 'translate-x-5' : 'translate-x-0'"></div>
                </button>
                <span class="text-xs font-bold text-slate-700">Tampilkan di halaman publik</span>
            </label>
        </div>

        <div data-reveal class="flex justify-end gap-3">
            <a href="{{ route('admin.social-links') }}" class="px-5 py-3 bg-white/70 hover:bg-white text-slate-700 text-sm font-bold rounded-xl border border-white/90 transition-colors">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all cursor-pointer">
                <x-icon name="check-circle-2" class="w-4.5 h-4.5" />
                {{ $socialLink->exists ? 'Simpan Perubahan' : 'Tambah Social Link' }}
            </button>
        </div>
    </form>
@endsection
