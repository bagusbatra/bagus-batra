@extends('admin.layouts.app')

@section('title', $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
    <div data-reveal class="flex items-center gap-3">
        <a href="{{ route('admin.testimonials') }}" class="p-2 text-slate-400 hover:text-slate-800 hover:bg-white/70 rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-4.5 h-4.5" />
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni' }}</h2>
            <p class="text-sm text-slate-500">{{ $testimonial->exists ? 'Perbarui testimoni dari '.$testimonial->name : 'Isi data testimoni baru.' }}</p>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}"
        class="space-y-6"
        x-data="{
            rating: {{ (int) old('rating', $testimonial->rating ?: 5) }},
            hoverRating: 0,
            avatarPreview: @js(old('avatar_url', $testimonial->avatar)),
        }"
    >
        @csrf
        @if ($testimonial->exists) @method('PUT') @endif

        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="quote" class="w-4 h-4 text-indigo-600" /> Data Testimoni</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Nama *</label>
                    <input type="text" name="name" required value="{{ old('name', $testimonial->name) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('name') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    @if ($testimonial->exists)
                        <p class="text-[11px] text-slate-400">Kunci internal: <code class="font-mono">{{ $testimonial->testimonial_key }}</code> (tidak berubah).</p>
                    @endif
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Role / Jabatan *</label>
                    <input type="text" name="role" required value="{{ old('role', $testimonial->role) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('role') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Perusahaan *</label>
                    <input type="text" name="company" required value="{{ old('company', $testimonial->company) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('company') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Tag Proyek</label>
                    <input type="text" name="project_tag" value="{{ old('project_tag', $testimonial->project_tag) }}" placeholder="Lumina Analytics" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('project_tag') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Isi Testimoni *</label>
                    <textarea name="content" required rows="4" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('content', $testimonial->content) }}</textarea>
                    @error('content') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Star rating picker --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-800">Rating *</label>
                <input type="hidden" name="rating" :value="rating" />
                <div class="flex items-center gap-1" @mouseleave="hoverRating = 0">
                    <template x-for="i in [1,2,3,4,5]" :key="i">
                        <button type="button" @click="rating = i" @mouseenter="hoverRating = i" class="p-0.5 cursor-pointer">
                            <x-icon name="star" class="w-6 h-6 transition-colors" x-bind:class="(hoverRating || rating) >= i ? 'fill-amber-400 text-amber-400' : 'text-slate-300'" />
                        </button>
                    </template>
                    <span class="text-xs font-bold text-slate-500 ml-2" x-text="rating + ' / 5'"></span>
                </div>
                @error('rating') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Avatar --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="palette" class="w-4 h-4 text-indigo-600" /> Avatar</h3>
            <div class="grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-4 items-start">
                <img :src="avatarPreview || 'https://placehold.co/72x72?text=%3F'" alt="" width="72" height="72" loading="lazy" decoding="async" class="w-18 h-18 rounded-full object-cover border border-white/90 shadow-2xs bg-slate-100" />
                <div class="space-y-3 min-w-0">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">URL Avatar</label>
                        <input type="text" name="avatar_url" x-model="avatarPreview" value="{{ old('avatar_url', $testimonial->avatar) }}" placeholder="https://..." class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        @error('avatar_url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Atau Upload File (menggantikan URL di atas)</label>
                        <input type="file" name="avatar_file" accept="image/*" @change="if ($event.target.files[0]) avatarPreview = URL.createObjectURL($event.target.files[0])" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer" />
                        @error('avatar_file') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div data-reveal class="flex justify-end gap-3">
            <a href="{{ route('admin.testimonials') }}" class="px-5 py-3 bg-white/70 hover:bg-white text-slate-700 text-sm font-bold rounded-xl border border-white/90 transition-colors">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all cursor-pointer">
                <x-icon name="check-circle-2" class="w-4.5 h-4.5" />
                {{ $testimonial->exists ? 'Simpan Perubahan' : 'Tambah Testimoni' }}
            </button>
        </div>
    </form>
@endsection
