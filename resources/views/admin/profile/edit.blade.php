@extends('admin.layouts.app')

@section('title', 'Profil & Hero')

@section('content')
    <div data-reveal class="space-y-1">
        <h2 class="text-xl font-extrabold text-slate-900">Profil & Hero</h2>
        <p class="text-sm text-slate-500">Data singkat ini tampil di section Hero, About, Contact, dan CV halaman publik. Hanya ada 1 profil (tidak bisa tambah/hapus).</p>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ availableForWork: {{ old('available_for_work', $profile->available_for_work) ? 'true' : 'false' }} }">
        @csrf
        @method('PUT')

        {{-- Identitas --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="user" class="w-4 h-4 text-indigo-600" /> Identitas</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Nama Lengkap *</label>
                    <input type="text" name="name" required value="{{ old('name', $profile->name) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('name') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Nama Panggilan</label>
                    <input type="text" name="nickname" value="{{ old('nickname', $profile->nickname) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('nickname') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Title (Bahasa Indonesia) *</label>
                    <input type="text" name="title_id" required value="{{ old('title_id', $profile->title_id) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('title_id') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Title (English) *</label>
                    <input type="text" name="title_en" required value="{{ old('title_en', $profile->title_en) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('title_en') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Lokasi *</label>
                    <input type="text" name="location" required value="{{ old('location', $profile->location) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('location') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-3 pt-1 select-none cursor-pointer w-fit">
                <input type="hidden" name="available_for_work" :value="availableForWork ? 1 : 0" />
                <button type="button" @click="availableForWork = !availableForWork" class="w-11 h-6 rounded-full transition-colors relative p-0.5 shrink-0" :class="availableForWork ? 'bg-indigo-600' : 'bg-slate-300'">
                    <div class="w-5 h-5 rounded-full bg-white transition-transform shadow-sm" :class="availableForWork ? 'translate-x-5' : 'translate-x-0'"></div>
                </button>
                <span class="text-xs font-bold text-slate-700">Tersedia untuk proyek baru (badge di Hero)</span>
            </label>
        </div>

        {{-- Tagline & Bio --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="sparkles" class="w-4 h-4 text-indigo-600" /> Tagline &amp; Bio</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Tagline (Bahasa Indonesia) *</label>
                    <textarea name="tagline_id" required rows="2" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('tagline_id', $profile->tagline_id) }}</textarea>
                    @error('tagline_id') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Tagline (English) *</label>
                    <textarea name="tagline_en" required rows="2" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('tagline_en', $profile->tagline_en) }}</textarea>
                    @error('tagline_en') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Bio (Bahasa Indonesia) *</label>
                    <textarea name="bio_id" required rows="4" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('bio_id', $profile->bio_id) }}</textarea>
                    @error('bio_id') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Bio (English) *</label>
                    <textarea name="bio_en" required rows="4" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('bio_en', $profile->bio_en) }}</textarea>
                    @error('bio_en') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Kontak & profil singkat --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="mail" class="w-4 h-4 text-indigo-600" /> Kontak &amp; Profil Singkat</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Email *</label>
                    <input type="email" name="email" required value="{{ old('email', $profile->email) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('email') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('phone') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">GitHub URL</label>
                    <input type="text" name="github" value="{{ old('github', $profile->github) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('github') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">LinkedIn URL</label>
                    <input type="text" name="linkedin" value="{{ old('linkedin', $profile->linkedin) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('linkedin') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Twitter / X URL</label>
                    <input type="text" name="twitter" value="{{ old('twitter', $profile->twitter) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('twitter') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-[11px] text-slate-400">Field ini dipakai untuk atribut kontak singkat di CV/profil. Untuk daftar tombol social media lengkap yang tampil sebagai kartu di halaman publik, kelola di menu <a href="{{ route('admin.social-links') }}" class="text-indigo-600 font-semibold hover:underline">Social Links</a>.</p>
        </div>

        {{-- Statistik Hero --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="trending-up" class="w-4 h-4 text-indigo-600" /> Statistik Hero</h3>
            <p class="text-xs text-slate-500 -mt-3">Ditulis bebas sebagai teks (mis. "6+", "45+", "99.4%") supaya bisa memuat simbol seperti "+" atau "%".</p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Tahun Pengalaman</label>
                    <input type="text" name="years_of_exp" required value="{{ old('years_of_exp', $profile->years_of_exp) }}" class="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('years_of_exp') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Proyek Selesai</label>
                    <input type="text" name="completed_projects" required value="{{ old('completed_projects', $profile->completed_projects) }}" class="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('completed_projects') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Kepuasan Klien</label>
                    <input type="text" name="client_satisfaction" required value="{{ old('client_satisfaction', $profile->client_satisfaction) }}" class="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('client_satisfaction') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Kontribusi OSS</label>
                    <input type="text" name="open_source_contributions" required value="{{ old('open_source_contributions', $profile->open_source_contributions) }}" class="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('open_source_contributions') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Avatar --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-6">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="palette" class="w-4 h-4 text-indigo-600" /> Foto Profil</h3>

            @foreach ([['field' => 'avatar', 'label' => 'Avatar Utama (kartu Hero)', 'current' => $profile->avatar], ['field' => 'secondary_avatar', 'label' => 'Avatar Sekunder', 'current' => $profile->secondary_avatar]] as $img)
                <div
                    x-data="{ preview: @js(old($img['field'].'_url', $img['current'])) }"
                    class="grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-4 items-start pt-1"
                >
                    <img :src="preview || 'https://placehold.co/96x96?text=%3F'" alt="" width="96" height="96" loading="lazy" decoding="async" class="w-24 h-24 rounded-2xl object-cover border border-white/90 shadow-2xs bg-slate-100" />

                    <div class="space-y-3 min-w-0">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-800">{{ $img['label'] }} — URL Gambar</label>
                            <input
                                type="text"
                                name="{{ $img['field'] }}_url"
                                x-model="preview"
                                value="{{ old($img['field'].'_url', $img['current']) }}"
                                placeholder="https://..."
                                class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors"
                            />
                            @error($img['field'].'_url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-800">Atau Upload File (menggantikan URL di atas)</label>
                            <input
                                type="file"
                                name="{{ $img['field'] }}_file"
                                accept="image/*"
                                @change="if ($event.target.files[0]) preview = URL.createObjectURL($event.target.files[0])"
                                class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer"
                            />
                            @error($img['field'].'_file') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div data-reveal class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all cursor-pointer">
                <x-icon name="check-circle-2" class="w-4.5 h-4.5" />
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection
