@extends('admin.layouts.app')

@section('title', $project->exists ? 'Edit Project' : 'Tambah Project')

@section('content')
    @php
        $techGroups = [
            'frontend' => 'Frontend & Client Stack',
            'backend' => 'Backend & API Services',
            'database' => 'Database & Caching Layer',
            'cloudAndDevOps' => 'Cloud, CI/CD & Deployment',
        ];
        $initial = [
            'tags' => old('tags', $project->tags ?? []),
            'highlights' => old('highlights', $project->highlights ?? []),
            'metrics' => old('metrics', $project->metrics ?? []),
            'techStack' => old('tech_stack', $project->tech_stack ?? []),
        ];
    @endphp

    <div data-reveal class="flex items-center gap-3">
        <a href="{{ route('admin.projects') }}" class="p-2 text-slate-400 hover:text-slate-800 hover:bg-white/70 rounded-xl transition-colors">
            <x-icon name="arrow-left" class="w-4.5 h-4.5" />
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">{{ $project->exists ? 'Edit Project' : 'Tambah Project' }}</h2>
            <p class="text-sm text-slate-500">{{ $project->exists ? 'Perbarui data '.$project->title : 'Isi data studi kasus proyek baru.' }}</p>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
        enctype="multipart/form-data"
        class="space-y-6"
        x-data="{
            featured: {{ old('featured', $project->exists ? $project->featured : false) ? 'true' : 'false' }},
            imagePreview: @js(old('image_url', $project->image)),
            tags: @js($initial['tags']),
            highlights: @js($initial['highlights']),
            metrics: @js($initial['metrics']),
            tech: {
                frontend: @js($initial['techStack']['frontend'] ?? []),
                backend: @js($initial['techStack']['backend'] ?? []),
                database: @js($initial['techStack']['database'] ?? []),
                cloudAndDevOps: @js($initial['techStack']['cloudAndDevOps'] ?? []),
            },
            accentColor: @js(old('accent_color', $project->accent_color ?: '#3b82f6')),
        }"
    >
        @csrf
        @if ($project->exists) @method('PUT') @endif

        {{-- Informasi Dasar --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="folder-git-2" class="w-4 h-4 text-indigo-600" /> Informasi Dasar</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Judul Project *</label>
                    <input type="text" name="title" required value="{{ old('title', $project->title) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('title') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    @if ($project->exists)
                        <p class="text-[11px] text-slate-400">Kunci internal: <code class="font-mono">{{ $project->project_key }}</code> (tidak berubah walau judul diedit).</p>
                    @endif
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Tagline *</label>
                    <input type="text" name="tagline" required value="{{ old('tagline', $project->tagline) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('tagline') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Deskripsi Singkat (kartu grid) *</label>
                    <textarea name="description" required rows="2" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('description', $project->description) }}</textarea>
                    @error('description') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-800">Deskripsi Lengkap (tab Overview modal) *</label>
                    <textarea name="long_description" required rows="4" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none">{{ old('long_description', $project->long_description) }}</textarea>
                    @error('long_description') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Kategori *</label>
                    <select name="category" required class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" @selected(old('category', $project->category) === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Role *</label>
                    <input type="text" name="role" required value="{{ old('role', $project->role) }}" placeholder="Lead Frontend Architect" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('role') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Timeline *</label>
                    <input type="text" name="timeline" required value="{{ old('timeline', $project->timeline) }}" placeholder="4 bulan (2025)" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('timeline') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Client / Scope</label>
                    <input type="text" name="client" value="{{ old('client', $project->client) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('client') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-3 pt-1 select-none cursor-pointer w-fit">
                <input type="hidden" name="featured" :value="featured ? 1 : 0" />
                <button type="button" @click="featured = !featured" class="w-11 h-6 rounded-full transition-colors relative p-0.5 shrink-0" :class="featured ? 'bg-indigo-600' : 'bg-slate-300'">
                    <div class="w-5 h-5 rounded-full bg-white transition-transform shadow-sm" :class="featured ? 'translate-x-5' : 'translate-x-0'"></div>
                </button>
                <span class="text-xs font-bold text-slate-700">Featured (badge "Featured" di kartu &amp; modal)</span>
            </label>
        </div>

        {{-- Gambar --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="palette" class="w-4 h-4 text-indigo-600" /> Gambar Banner</h3>
            <div class="grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-4 items-start">
                <img :src="imagePreview || 'https://placehold.co/160x100?text=%3F'" alt="" class="w-40 h-24 rounded-2xl object-cover border border-white/90 shadow-2xs bg-slate-100" />
                <div class="space-y-3 min-w-0">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">URL Gambar</label>
                        <input type="text" name="image_url" x-model="imagePreview" value="{{ old('image_url', $project->image) }}" placeholder="https://..." class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        @error('image_url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800">Atau Upload File (menggantikan URL di atas)</label>
                        <input type="file" name="image_file" accept="image/*" @change="if ($event.target.files[0]) imagePreview = URL.createObjectURL($event.target.files[0])" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer" />
                        @error('image_file') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Tags (repeater sederhana) --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="tag" class="w-4 h-4 text-indigo-600" /> Tags</h3>
                <button type="button" @click="tags.push('')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah Tag
                </button>
            </div>
            <p class="text-xs text-slate-400" x-show="tags.length === 0">Belum ada tag. Klik "Tambah Tag".</p>
            <div class="space-y-2">
                <template x-for="(tag, index) in tags" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="text" :name="`tags[${index}]`" x-model="tags[index]" placeholder="React 19" class="flex-1 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        <button type="button" @click="tags.splice(index, 1)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer shrink-0">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Metrics (repeater label+value) --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="trending-up" class="w-4 h-4 text-indigo-600" /> Metrics (Hasil Terukur)</h3>
                <button type="button" @click="metrics.push({ label: '', value: '' })" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah Metric
                </button>
            </div>
            <p class="text-xs text-slate-400" x-show="metrics.length === 0">Belum ada metric. Klik "Tambah Metric".</p>
            <div class="space-y-2">
                <template x-for="(metric, index) in metrics" :key="index">
                    <div class="flex items-center gap-2">
                        <input type="text" :name="`metrics[${index}][value]`" x-model="metrics[index].value" placeholder="0.6s" class="w-28 shrink-0 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm font-bold text-indigo-700 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        <input type="text" :name="`metrics[${index}][label]`" x-model="metrics[index].label" placeholder="Page Load Speed" class="flex-1 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                        <button type="button" @click="metrics.splice(index, 1)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer shrink-0">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Highlights (repeater sederhana, textarea baris) --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="check-circle-2" class="w-4 h-4 text-indigo-600" /> Sorotan Fitur &amp; Inovasi</h3>
                <button type="button" @click="highlights.push('')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah Highlight
                </button>
            </div>
            <p class="text-xs text-slate-400" x-show="highlights.length === 0">Belum ada highlight. Klik "Tambah Highlight".</p>
            <div class="space-y-2">
                <template x-for="(highlight, index) in highlights" :key="index">
                    <div class="flex items-start gap-2">
                        <textarea :name="`highlights[${index}]`" x-model="highlights[index]" rows="2" placeholder="Implementasi virtualized list untuk merender 50.000+ baris data tanpa lag." class="flex-1 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors resize-none"></textarea>
                        <button type="button" @click="highlights.splice(index, 1)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer shrink-0">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Tech Stack (4 grup repeater) --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-6">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="layers" class="w-4 h-4 text-indigo-600" /> Arsitektur Tech Stack</h3>

            @foreach ($techGroups as $groupKey => $groupLabel)
                <div class="space-y-2.5 pt-1 first:pt-0 border-t border-slate-200/60 first:border-t-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $groupLabel }}</h4>
                        <button type="button" @click="tech.{{ $groupKey }}.push('')" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1 cursor-pointer">
                            <x-icon name="sparkles" class="w-3.5 h-3.5" /> Tambah
                        </button>
                    </div>
                    <p class="text-xs text-slate-400" x-show="tech.{{ $groupKey }}.length === 0">Belum ada item.</p>
                    <div class="space-y-2">
                        <template x-for="(item, index) in tech.{{ $groupKey }}" :key="index">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="`tech_stack[{{ $groupKey }}][${index}]`" x-model="tech.{{ $groupKey }}[index]" class="flex-1 px-3.5 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                                <button type="button" @click="tech.{{ $groupKey }}.splice(index, 1)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer shrink-0">
                                    <x-icon name="x" class="w-4 h-4" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Link & Tampilan --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-6 border border-white/80 shadow-2xs space-y-5">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2"><x-icon name="external-link" class="w-4 h-4 text-indigo-600" /> Link &amp; Tampilan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Demo URL</label>
                    <input type="text" name="demo_url" value="{{ old('demo_url', $project->demo_url) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('demo_url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">GitHub URL</label>
                    <input type="text" name="github_url" value="{{ old('github_url', $project->github_url) }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('github_url') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Warna Gradient Kartu (kelas Tailwind)</label>
                    <input type="text" name="color_gradient" value="{{ old('color_gradient', $project->color_gradient) }}" placeholder="from-blue-500/10 via-indigo-500/5 to-transparent" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs font-mono text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    @error('color_gradient') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">Warna Aksen (hex)</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="accentColor" class="w-11 h-10 rounded-lg border border-white/90 cursor-pointer shrink-0 bg-white/70" />
                        <input type="text" name="accent_color" x-model="accentColor" placeholder="#3b82f6" class="flex-1 px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm font-mono text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                    </div>
                    @error('accent_color') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div data-reveal class="flex justify-end gap-3">
            <a href="{{ route('admin.projects') }}" class="px-5 py-3 bg-white/70 hover:bg-white text-slate-700 text-sm font-bold rounded-xl border border-white/90 transition-colors">Batal</a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all cursor-pointer">
                <x-icon name="check-circle-2" class="w-4.5 h-4.5" />
                {{ $project->exists ? 'Simpan Perubahan' : 'Tambah Project' }}
            </button>
        </div>
    </form>
@endsection
