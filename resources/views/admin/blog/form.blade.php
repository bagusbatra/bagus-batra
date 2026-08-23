@extends('admin.layouts.app')

@section('title', $post->exists ? 'Edit Artikel' : 'Tambah Artikel')

@section('content')
    @php
        $initialSections = old('sections', collect($post->sections ?? [])->map(fn ($s) => [
            'heading' => $s['heading'] ?? '',
            'body' => $s['body'] ?? '',
            'tip' => $s['tip'] ?? '',
            'code_language' => $s['codeSnippet']['language'] ?? '',
            'code_filename' => $s['codeSnippet']['filename'] ?? '',
            'code_code' => $s['codeSnippet']['code'] ?? '',
        ])->values()->all());
    @endphp

    <div data-reveal class="flex items-center gap-3">
        <a href="{{ route('admin.blog') }}" class="p-2 text-slate-400 hover:text-slate-800 hover:bg-white/70 rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-4.5 h-4.5" />
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $post->exists ? 'Edit Artikel' : 'Tambah Artikel' }}</h2>
            <p class="text-sm text-slate-500">{{ $post->exists ? 'Perbarui data '.$post->title : 'Isi data artikel blog baru.' }}</p>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
        enctype="multipart/form-data"
        class="space-y-6"
        x-data="{
            tags: @js(old('tags', $post->tags ?? [])),
            sections: @js($initialSections),
            coverPreview: @js(old('cover_image_url', $post->cover_image)),
            avatarPreview: @js(old('author_avatar_url', $post->author_avatar)),
        }"
    >
        @csrf
        @if ($post->exists) @method('PUT') @endif

        {{-- Informasi Dasar --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="book-open" class="w-4 h-4 text-indigo-600" /> Informasi Dasar</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Judul Artikel *</label>
                    <input type="text" name="title" required value="{{ old('title', $post->title) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('title') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    @if ($post->exists)
                        <p class="text-[11px] text-slate-400">Kunci internal: <code class="font-mono">{{ $post->post_key }}</code>, slug: <code class="font-mono">{{ $post->slug }}</code> (keduanya tidak berubah walau judul diedit).</p>
                    @endif
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Ringkasan (Summary) *</label>
                    <textarea name="summary" required rows="2" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('summary', $post->summary) }}</textarea>
                    @error('summary') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Kategori *</label>
                    <select name="category" required class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" @selected(old('category', $post->category) === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Waktu Baca *</label>
                    <input type="text" name="read_time" required value="{{ old('read_time', $post->read_time) }}" placeholder="6 menit baca" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('read_time') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Tanggal Terbit *</label>
                    <input type="text" name="published_at" required value="{{ old('published_at', $post->published_at) }}" placeholder="28 Jan 2026" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('published_at') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Likes</label>
                    <input type="number" min="0" name="likes" value="{{ old('likes', $post->likes ?? 0) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('likes') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Views</label>
                    <input type="text" name="views" value="{{ old('views', $post->views ?? '0') }}" placeholder="3.4k" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('views') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Tags --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="tag" class="w-4 h-4 text-indigo-600" /> Tags</h3>
                <button type="button" @click="tags.push('')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah Tag
                </button>
            </div>
            <p class="text-xs text-slate-400" x-show="tags.length === 0">Belum ada tag.</p>
            <div class="space-y-2">
                <template x-for="(tag, index) in tags" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="text" :name="`tags[${index}]`" x-model="tags[index]" class="flex-1 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        <button type="button" @click="tags.splice(index, 1)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer shrink-0">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Penulis & Gambar --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-6">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="user" class="w-4 h-4 text-indigo-600" /> Penulis &amp; Gambar</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Nama Penulis *</label>
                    <input type="text" name="author_name" required value="{{ old('author_name', $post->author_name) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('author_name') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Role Penulis *</label>
                    <input type="text" name="author_role" required value="{{ old('author_role', $post->author_role) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('author_role') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <img :src="coverPreview || 'https://placehold.co/96x60?text=%3F'" alt="" width="96" height="64" loading="lazy" decoding="async" class="w-24 h-16 rounded-xl object-cover border border-white/90 shadow-2xs bg-slate-100 shrink-0" />
                        <span class="text-xs font-bold text-slate-600">Cover Image</span>
                    </div>
                    <input type="text" name="cover_image_url" x-model="coverPreview" value="{{ old('cover_image_url', $post->cover_image) }}" placeholder="URL gambar cover" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('cover_image_url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    <input type="file" name="cover_image_file" accept="image/*" @change="if ($event.target.files[0]) coverPreview = URL.createObjectURL($event.target.files[0])" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer" />
                    @error('cover_image_file') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <img :src="avatarPreview || 'https://placehold.co/64x64?text=%3F'" alt="" width="64" height="64" loading="lazy" decoding="async" class="w-16 h-16 rounded-full object-cover border border-white/90 shadow-2xs bg-slate-100 shrink-0" />
                        <span class="text-xs font-bold text-slate-600">Avatar Penulis</span>
                    </div>
                    <input type="text" name="author_avatar_url" x-model="avatarPreview" value="{{ old('author_avatar_url', $post->author_avatar) }}" placeholder="URL avatar" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('author_avatar_url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    <input type="file" name="author_avatar_file" accept="image/*" @change="if ($event.target.files[0]) avatarPreview = URL.createObjectURL($event.target.files[0])" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer" />
                    @error('author_avatar_file') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Sections (repeater dinamis paling kompleks) --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="layers" class="w-4 h-4 text-indigo-600" /> Bagian Artikel (Sections)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tiap bagian: heading + body wajib. Code snippet &amp; tip opsional — kosongkan bila tidak dipakai.</p>
                </div>
                <button type="button" @click="sections.push({ heading: '', body: '', tip: '', code_language: '', code_filename: '', code_code: '' })" class="shrink-0 text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah Bagian
                </button>
            </div>
            <p class="text-xs text-slate-400" x-show="sections.length === 0">Belum ada bagian artikel. Klik "Tambah Bagian".</p>

            <div class="space-y-4">
                <template x-for="(section, index) in sections" :key="index">
                    <div class="p-4 sm:p-5 bg-white/70 rounded-2xl border border-white/90 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bagian <span x-text="index + 1"></span></span>
                            <button type="button" @click="sections.splice(index, 1)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer">
                                <x-icon name="x" class="w-4 h-4" />
                            </button>
                        </div>

                        <input type="text" :name="`sections[${index}][heading]`" x-model="section.heading" placeholder="Heading bagian" class="w-full px-3.5 py-2 bg-white/80 backdrop-blur-md rounded-xl border border-white/90 text-sm font-bold text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        <textarea :name="`sections[${index}][body]`" x-model="section.body" rows="3" placeholder="Isi paragraf bagian ini" class="w-full px-3.5 py-2 bg-white/80 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none"></textarea>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                            <input type="text" :name="`sections[${index}][code_language]`" x-model="section.code_language" placeholder="Bahasa (opsional, mis. tsx)" class="px-3.5 py-2 bg-white/80 backdrop-blur-md rounded-xl border border-white/90 text-xs font-mono text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                            <input type="text" :name="`sections[${index}][code_filename]`" x-model="section.code_filename" placeholder="Nama file (opsional)" class="px-3.5 py-2 bg-white/80 backdrop-blur-md rounded-xl border border-white/90 text-xs font-mono text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        </div>
                        <textarea :name="`sections[${index}][code_code]`" x-model="section.code_code" rows="4" placeholder="Kode program (opsional, kosongkan bila bagian ini tidak punya code snippet)" class="w-full px-3.5 py-2 bg-slate-900 text-slate-100 rounded-xl border border-slate-700 text-xs font-mono focus:outline-indigo-500 transition-colors resize-none"></textarea>

                        <textarea :name="`sections[${index}][tip]`" x-model="section.tip" rows="2" placeholder="Tip tambahan (opsional)" class="w-full px-3.5 py-2 bg-amber-50/70 backdrop-blur-md rounded-xl border border-amber-200/70 text-xs text-amber-900 focus:bg-amber-50 focus:outline-amber-500 transition-colors resize-none"></textarea>
                    </div>
                </template>
            </div>
        </div>

        <div data-reveal class="flex justify-end gap-3">
            <a href="{{ route('admin.blog') }}" class="px-5 py-3 bg-white/70 hover:bg-white text-slate-700 text-sm font-bold rounded-xl border border-white/90 transition-colors">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all cursor-pointer">
                <x-icon name="check-circle-2" class="w-4.5 h-4.5" />
                {{ $post->exists ? 'Simpan Perubahan' : 'Tambah Artikel' }}
            </button>
        </div>
    </form>
@endsection
