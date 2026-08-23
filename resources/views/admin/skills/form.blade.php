@extends('admin.layouts.app')

@section('title', $skill->exists ? 'Edit Skill' : 'Tambah Skill')

@section('content')
    @php
        $categories = ['frontend' => 'Frontend', 'backend' => 'Backend', 'devops' => 'DevOps & Cloud', 'tools' => 'Tools & UI'];
        $iconOptions = [
            'Code2' => 'Code2', 'FileCode' => 'FileCode', 'Palette' => 'Palette', 'Sparkles' => 'Sparkles',
            'Layers' => 'Layers', 'Server' => 'Server', 'Database' => 'Database', 'Cpu' => 'Cpu',
            'Cloud' => 'Cloud', 'GitBranch' => 'GitBranch', 'Layout' => 'Layout', 'Zap' => 'Zap',
        ];
    @endphp

    <div data-reveal class="flex items-center gap-3">
        <a href="{{ route('admin.about-skills') }}" class="p-2 text-slate-400 hover:text-slate-800 hover:bg-white/70 rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-4.5 h-4.5" />
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $skill->exists ? 'Edit Skill' : 'Tambah Skill' }}</h2>
            <p class="text-sm text-slate-500">{{ $skill->exists ? 'Perbarui data '.$skill->name : 'Isi data keahlian/tech stack baru.' }}</p>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $skill->exists ? route('admin.about-skills.update', $skill) : route('admin.about-skills.store') }}"
        class="space-y-6"
        x-data="{ level: {{ old('level', $skill->level ?? 80) }} }"
    >
        @csrf
        @if ($skill->exists) @method('PUT') @endif

        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Nama Skill *</label>
                    <input type="text" name="name" required value="{{ old('name', $skill->name) }}" placeholder="React 19 & Next.js 15" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('name') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Kategori *</label>
                    <select name="category" required class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
                        @foreach ($categories as $value => $categoryLabel)
                            <option value="{{ $value }}" @selected(old('category', $skill->category) === $value)>{{ $categoryLabel }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Ikon *</label>
                    <select name="icon_name" required class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
                        @foreach ($iconOptions as $value => $iconLabel)
                            <option value="{{ $value }}" @selected(old('icon_name', $skill->icon_name) === $value)>{{ $iconLabel }}</option>
                        @endforeach
                    </select>
                    @error('icon_name') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Lama Pengalaman *</label>
                    <input type="text" name="experience" required value="{{ old('experience', $skill->experience) }}" placeholder="6 tahun" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('experience') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Level (0-100) *</label>
                    <div class="flex items-center gap-3">
                        <input type="range" min="0" max="100" name="level_range" x-model.number="level" class="flex-1 accent-indigo-600 cursor-pointer" />
                        <input type="number" min="0" max="100" name="level" required x-model.number="level" class="w-20 px-2.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 text-center focus:bg-white focus:outline-indigo-500 shadow-2xs" />
                    </div>
                    <div class="w-full bg-slate-200/50 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all" :style="`width: ${level}%`"></div>
                    </div>
                    @error('level') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Highlight Text</label>
                    <input type="text" name="highlight_text" value="{{ old('highlight_text', $skill->highlight_text) }}" placeholder="Server Components, Suspense, Parallel Routes, Optimistic UI" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('highlight_text') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div data-reveal class="flex justify-end gap-3">
            <a href="{{ route('admin.about-skills') }}" class="px-5 py-3 bg-white/70 hover:bg-white text-slate-700 text-sm font-bold rounded-xl border border-white/90 transition-colors">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all cursor-pointer">
                <x-icon name="check-circle-2" class="w-4.5 h-4.5" />
                {{ $skill->exists ? 'Simpan Perubahan' : 'Tambah Skill' }}
            </button>
        </div>
    </form>
@endsection
