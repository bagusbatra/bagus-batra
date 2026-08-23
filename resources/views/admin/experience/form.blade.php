@extends('admin.layouts.app')

@section('title', $experience->exists ? 'Edit Experience' : 'Tambah Experience')

@section('content')
    <div data-reveal class="flex items-center gap-3">
        <a href="{{ route('admin.experience') }}" class="p-2 text-slate-400 hover:text-slate-800 hover:bg-white/70 rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-4.5 h-4.5" />
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $experience->exists ? 'Edit Experience' : 'Tambah Experience' }}</h2>
            <p class="text-sm text-slate-500">{{ $experience->exists ? 'Perbarui data '.$experience->role.' — '.$experience->company : 'Isi data pengalaman kerja baru.' }}</p>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $experience->exists ? route('admin.experience.update', $experience) : route('admin.experience.store') }}"
        class="space-y-6"
        x-data="{
            featured: {{ old('featured', $experience->exists ? $experience->featured : false) ? 'true' : 'false' }},
            achievements: @js(old('achievements', $experience->achievements ?? [])),
            skills: @js(old('skills', $experience->skills ?? [])),
        }"
    >
        @csrf
        @if ($experience->exists) @method('PUT') @endif

        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="briefcase" class="w-4 h-4 text-indigo-600" /> Informasi Posisi</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Role / Jabatan *</label>
                    <input type="text" name="role" required value="{{ old('role', $experience->role) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('role') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    @if ($experience->exists)
                        <p class="text-[11px] text-slate-400">Kunci internal: <code class="font-mono">{{ $experience->experience_key }}</code> (tidak berubah).</p>
                    @endif
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Perusahaan *</label>
                    <input type="text" name="company" required value="{{ old('company', $experience->company) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('company') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Periode *</label>
                    <input type="text" name="period" required value="{{ old('period', $experience->period) }}" placeholder="2023 — Sekarang" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('period') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Lokasi *</label>
                    <input type="text" name="location" required value="{{ old('location', $experience->location) }}" placeholder="Jakarta, Indonesia (Remote)" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('location') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Tipe Kerja *</label>
                    <input type="text" name="type" required value="{{ old('type', $experience->type) }}" placeholder="Full-Time" list="type-suggestions" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    <datalist id="type-suggestions">
                        <option value="Full-Time" />
                        <option value="Part-Time" />
                        <option value="Contract" />
                        <option value="Freelance" />
                        <option value="Internship" />
                    </datalist>
                    @error('type') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Deskripsi *</label>
                    <textarea name="description" required rows="3" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('description', $experience->description) }}</textarea>
                    @error('description') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-3 pt-1 select-none cursor-pointer w-fit">
                <input type="hidden" name="featured" :value="featured ? 1 : 0" />
                <button type="button" @click="featured = !featured" class="w-11 h-6 rounded-full transition-colors relative p-0.5 shrink-0" :class="featured ? 'bg-indigo-600' : 'bg-slate-300'">
                    <div class="w-5 h-5 rounded-full bg-white transition-transform shadow-sm" :class="featured ? 'translate-x-5' : 'translate-x-0'"></div>
                </button>
                <span class="text-xs font-bold text-slate-700">Featured</span>
            </label>
        </div>

        {{-- Achievements (repeater) --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="check-circle-2" class="w-4 h-4 text-indigo-600" /> Pencapaian Kunci (Achievements)</h3>
                <button type="button" @click="achievements.push('')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah
                </button>
            </div>
            <p class="text-xs text-slate-400" x-show="achievements.length === 0">Belum ada achievement. Klik "Tambah".</p>
            <div class="space-y-2">
                <template x-for="(item, index) in achievements" :key="index">
                    <div class="flex items-start gap-2">
                        <textarea :name="`achievements[${index}]`" x-model="achievements[index]" rows="2" class="flex-1 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none"></textarea>
                        <button type="button" @click="achievements.splice(index, 1)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer shrink-0">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Skills terkait (repeater) --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="tag" class="w-4 h-4 text-indigo-600" /> Skills Terkait</h3>
                <button type="button" @click="skills.push('')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah
                </button>
            </div>
            <p class="text-xs text-slate-400" x-show="skills.length === 0">Belum ada skill. Klik "Tambah".</p>
            <div class="space-y-2">
                <template x-for="(item, index) in skills" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="text" :name="`skills[${index}]`" x-model="skills[index]" class="flex-1 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        <button type="button" @click="skills.splice(index, 1)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer shrink-0">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div data-reveal class="flex justify-end gap-3">
            <a href="{{ route('admin.experience') }}" class="px-5 py-3 bg-white/70 hover:bg-white text-slate-700 text-sm font-bold rounded-xl border border-white/90 transition-colors">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all cursor-pointer">
                <x-icon name="check-circle-2" class="w-4.5 h-4.5" />
                {{ $experience->exists ? 'Simpan Perubahan' : 'Tambah Experience' }}
            </button>
        </div>
    </form>
@endsection
