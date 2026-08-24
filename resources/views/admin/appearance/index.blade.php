@extends('admin.layouts.app')

@section('title', 'Tampilan Halaman Index')

@section('content')
    <div
        x-data="{ tab: '{{ in_array($tab, ['ringkasan', 'branding', 'animasi', 'sections', 'elemen', 'mode']) ? $tab : 'ringkasan' }}' }"
        class="space-y-6"
    >
        <div data-reveal class="space-y-1">
            <h2 class="text-xl font-extrabold text-slate-900">Tampilan Halaman Index</h2>
            <p class="text-sm text-slate-500">
                Kustomisasi tampilan halaman publik (<code class="text-[11px] font-mono bg-slate-100 px-1.5 py-0.5 rounded">/</code>) — preset warna, animasi, urutan section, sub-elemen halaman, dan mode situs.
                Semua perubahan di sini tersimpan sebagai <strong>draft</strong> dulu, TIDAK langsung live — lihat panel status di bawah.
            </p>
        </div>

        {{-- Panel status Draft/Live + aksi Publish/Buang Draft/Buka Preview --}}
        <div data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center border shrink-0 {{ $hasPendingDraft ? 'bg-amber-50/80 text-amber-600 border-amber-100/60' : 'bg-emerald-50/80 text-emerald-600 border-emerald-100/60' }}">
                    <x-icon name="{{ $hasPendingDraft ? 'activity' : 'check-circle-2' }}" class="w-5.5 h-5.5" />
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-extrabold text-slate-900">
                        Status: {{ $hasPendingDraft ? 'Ada draft belum di-publish' : 'Live (tidak ada draft pending)' }}
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if ($hasPendingDraft)
                            Perubahan draft belum terlihat oleh pengunjung biasa. Klik "Buka Preview" untuk melihatnya sebagai admin, lalu "Publish Perubahan" agar berlaku live.
                        @else
                            Semua pengaturan tampilan yang sedang live sama persis dengan yang ada di draft — tidak ada perubahan menunggu publish.
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a
                    href="{{ url('/') }}?preview=1"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition-colors"
                >
                    <x-icon name="eye" class="w-3.5 h-3.5" />
                    Buka Preview
                </a>

                <form method="POST" action="{{ route('admin.appearance.discard') }}">
                    @csrf
                    <button
                        type="submit"
                        {{ $hasPendingDraft ? '' : 'disabled' }}
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-xl border border-rose-200 transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white"
                    >
                        <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                        Buang Draft
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.appearance.publish') }}">
                    @csrf
                    <button
                        type="submit"
                        {{ $hasPendingDraft ? '' : 'disabled' }}
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-slate-900"
                    >
                        <x-icon name="check" class="w-3.5 h-3.5" />
                        Publish Perubahan
                    </button>
                </form>
            </div>
        </div>

        {{-- Tab nav --}}
        <div data-reveal class="flex flex-wrap gap-2 bg-white/60 backdrop-blur-xl rounded-2xl border border-white/80 shadow-2xs p-2">
            @php
                $tabs = [
                    'ringkasan' => ['label' => 'Ringkasan', 'icon' => 'layout'],
                    'branding' => ['label' => 'Tema & Branding', 'icon' => 'palette'],
                    'animasi' => ['label' => 'Animasi & Efek', 'icon' => 'zap'],
                    'sections' => ['label' => 'Urutan & Isi Section', 'icon' => 'sliders'],
                    'elemen' => ['label' => 'Elemen Halaman', 'icon' => 'layers'],
                    'mode' => ['label' => 'Mode Situs', 'icon' => 'server'],
                ];
            @endphp
            @foreach ($tabs as $key => $meta)
                <button
                    type="button"
                    @click="tab = '{{ $key }}'"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                    :class="tab === '{{ $key }}' ? 'bg-indigo-50/90 text-indigo-700 border border-indigo-100' : 'text-slate-600 hover:bg-slate-100/80 border border-transparent'"
                >
                    <x-icon :name="$meta['icon']" class="w-4 h-4 shrink-0" />
                    <span>{{ $meta['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Tab: Ringkasan --}}
        <div x-show="tab === 'ringkasan'" x-cloak data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs p-6 space-y-3">
            <h3 class="text-sm font-extrabold text-slate-900">Tentang menu ini</h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                Menu ini adalah fondasi Iterasi 18 (Fase 4) — mekanisme <strong>Draft → Preview → Publish/Buang Draft</strong> yang akan dipakai semua fitur kustomisasi tampilan berikutnya (preset warna & logo di Iterasi 19, reorder section & jumlah item di Iterasi 20, sub-elemen & custom heading di Iterasi 21, mode maintenance di Iterasi 22).
            </p>
            <p class="text-sm text-slate-600 leading-relaxed">
                Tab <strong>Animasi &amp; Efek</strong> sudah fungsional penuh sebagai bukti konsep alur draft/publish (toggle animasi reveal-on-scroll). Tab lain masih placeholder "segera hadir" — struktur menu dan mekanisme draft-nya sudah siap dipakai begitu form masing-masing dibangun.
            </p>
        </div>

        {{-- Tab: Tema & Branding — FUNGSIONAL (Iterasi 19) --}}
        <div x-show="tab === 'branding'" x-cloak data-reveal class="space-y-4">
            {{-- Preset Warna Aksen --}}
            <form method="POST" action="{{ route('admin.appearance.branding.update') }}" enctype="multipart/form-data" x-data="{ logoType: '{{ $logoType }}' }" class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Preset Warna Aksen</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Diterapkan ke elemen brand-accent saja (tombol CTA utama, pill nav aktif, badge label section, gradient judul Hero, border hover card, tombol filter aktif) — bukan warna netral (teks/latar/border slate).</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ($accentPresets as $key => $preset)
                        <label class="relative flex flex-col items-center gap-2 p-4 rounded-2xl border cursor-pointer transition-colors {{ $accentPreset === $key ? 'border-slate-900 bg-slate-50' : 'border-slate-200/70 hover:bg-slate-50/60' }}">
                            <input type="radio" name="accent_preset" value="{{ $key }}" class="sr-only peer" {{ $accentPreset === $key ? 'checked' : '' }}>
                            <span class="w-9 h-9 rounded-full border-2 border-white shadow-md" style="background-color: {{ $preset['swatch'] }};"></span>
                            <span class="text-xs font-bold text-slate-700">{{ $preset['label'] }}</span>
                            @if ($accentPreset === $key)
                                <span class="absolute top-2 right-2 w-4 h-4 rounded-full bg-slate-900 text-white flex items-center justify-center">
                                    <x-icon name="check" class="w-2.5 h-2.5" />
                                </span>
                            @endif
                        </label>
                    @endforeach
                </div>

                <div class="border-t border-slate-200/70 pt-5 space-y-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Logo &amp; Branding</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih logo teks (default, "Bagus.dev") atau upload gambar logo sendiri. Kalau tipe gambar dipilih tapi belum ada file yang diunggah, situs tetap fallback ke logo teks (tidak pernah kosong).</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer text-xs font-bold transition-colors" :class="logoType === 'text' ? 'border-slate-900 bg-slate-50 text-slate-900' : 'border-slate-200/70 text-slate-600 hover:bg-slate-50/60'">
                            <input type="radio" name="logo_type" value="text" x-model="logoType" class="sr-only">
                            <x-icon name="code2" class="w-3.5 h-3.5" />
                            Logo Teks
                        </label>
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer text-xs font-bold transition-colors" :class="logoType === 'image' ? 'border-slate-900 bg-slate-50 text-slate-900' : 'border-slate-200/70 text-slate-600 hover:bg-slate-50/60'">
                            <input type="radio" name="logo_type" value="image" x-model="logoType" class="sr-only">
                            <x-icon name="image" class="w-3.5 h-3.5" />
                            Logo Gambar
                        </label>
                    </div>

                    <div x-show="logoType === 'image'" x-cloak class="p-4 rounded-2xl border border-slate-200/70 bg-slate-50/60 space-y-3">
                        @if ($logoImage)
                            <div class="flex items-center gap-3">
                                <img src="{{ $logoImage }}" alt="Logo saat ini" width="40" height="40" loading="lazy" class="h-10 w-auto object-contain bg-white rounded-lg border border-slate-200 p-1.5" />
                                <span class="text-[11px] text-slate-500">Logo saat ini (draft/live). Upload file baru di bawah untuk menggantinya.</span>
                            </div>
                        @endif
                        <input type="file" name="logo_image_file" accept="image/*" class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-indigo-600 file:cursor-pointer cursor-pointer" />
                        @error('logo_image_file') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors">
                    <x-icon name="check" class="w-3.5 h-3.5" />
                    Simpan sebagai Draft
                </button>
            </form>
        </div>

        {{-- Tab: Animasi & Efek — FUNGSIONAL (bukti konsep Iterasi 18) --}}
        <div x-show="tab === 'animasi'" x-cloak data-reveal class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs p-6 space-y-5">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Animasi &amp; Efek</h3>
                <p class="text-xs text-slate-500 mt-0.5">Bukti konsep end-to-end mekanisme draft/publish Iterasi 18. Perubahan di sini tersimpan sebagai draft — cek "Buka Preview" untuk melihatnya sebelum Publish.</p>
            </div>

            <form method="POST" action="{{ route('admin.appearance.animations.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border border-slate-200/70 bg-slate-50/60">
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-slate-800">Reveal-on-scroll</div>
                        <p class="text-xs text-slate-500 mt-0.5">Efek elemen muncul perlahan (fade + geser) saat di-scroll ke area layar. Nonaktifkan supaya konten langsung tampil tanpa animasi.</p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="animations_enabled" value="1" class="peer sr-only" {{ $animationsEnabled ? 'checked' : '' }}>
                        <div class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-indigo-600 transition-colors relative">
                            <div class="w-5 h-5 rounded-full bg-white shadow-sm absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition-transform"></div>
                        </div>
                    </label>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors">
                    <x-icon name="check" class="w-3.5 h-3.5" />
                    Simpan sebagai Draft
                </button>
            </form>
        </div>

        {{-- Tab: Urutan & Isi Section — on/off (Iterasi 1) + reorder drag-drop & jumlah item (Iterasi 20) --}}
        <div x-show="tab === 'sections'" x-cloak class="space-y-4">
            {{-- Reorder drag-drop & jumlah item — FUNGSIONAL (Iterasi 20) --}}
            @php
                $countLabels = [
                    'projects' => 'Default: 3 (featured, fallback 3 pertama)',
                    'blog' => 'Default: semua artikel',
                    'testimonials' => 'Default: semua testimoni',
                ];
                $reorderItems = $orderedTopLevelSections->map(fn ($s) => [
                    'key' => $s->section_key,
                    'label' => $s->label,
                    'hasCount' => in_array($s->section_key, ['projects', 'blog', 'testimonials'], true),
                    // Iterasi 20: coalesce null -> '' SEBELUM di-@js() ke JSON —
                    // assign JS `null` langsung ke properti .value elemen
                    // <input> lewat x-model bisa berakhir sbg literal string
                    // "null" di beberapa browser (bukan field kosong yg
                    // diharapkan), jadi dihindari di sini dari sisi server.
                    'count' => $s->effective('display_count', true) ?? '',
                ])->values();
            @endphp

            <form
                method="POST"
                action="{{ route('admin.appearance.sections.update') }}"
                x-data="sectionReorder(@js($reorderItems))"
                data-reveal
                class="bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs p-5 sm:p-6 space-y-4"
            >
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Urutan Tampil &amp; Jumlah Item</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Geser (drag &amp; drop) kartu untuk mengubah urutan 7 section utama di halaman index. Isi "Jumlah tampil" untuk Proyek/Blog/Testimoni (kosongkan = pakai default). Section "Keahlian &amp; Tech Stack" tidak ada di sini karena menyatu dengan section "Tentang Saya" (togglenya tetap ada di daftar bawah).
                    </p>
                </div>

                @error('order') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror

                <ul class="space-y-2">
                    <template x-for="(item, index) in items" :key="item.key">
                        <li
                            draggable="true"
                            @dragstart="dragStart(index)"
                            @dragover.prevent="dragOverItem(index)"
                            @dragend="dragEnd()"
                            class="flex flex-wrap items-center gap-3 p-3 rounded-2xl border border-slate-200/70 bg-white/70 cursor-grab active:cursor-grabbing transition-opacity"
                            :class="dragIndex === index ? 'opacity-40' : 'opacity-100'"
                        >
                            <x-icon name="grip-vertical" class="w-4 h-4 text-slate-400 shrink-0" />
                            <span class="text-[11px] font-mono font-bold text-slate-400 w-5 text-right shrink-0" x-text="index"></span>
                            <span class="text-sm font-bold text-slate-800 flex-1 min-w-[8rem]" x-text="item.label"></span>

                            <template x-if="item.hasCount">
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <label class="text-[11px] text-slate-400 whitespace-nowrap">Jumlah tampil:</label>
                                    <input
                                        type="number"
                                        min="1"
                                        max="50"
                                        x-model.number="item.count"
                                        :name="'display_count[' + item.key + ']'"
                                        class="w-16 px-2 py-1.5 text-xs rounded-lg border border-slate-200 text-center focus:outline-indigo-500"
                                    />
                                </div>
                            </template>

                            <input type="hidden" :name="'order[]'" :value="item.key" />
                        </li>
                    </template>
                </ul>

                <div class="flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-slate-400">
                    @foreach ($countLabels as $key => $label)
                        <span><strong class="text-slate-500">{{ $key }}</strong>: {{ $label }}</span>
                    @endforeach
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition-colors">
                    <x-icon name="check" class="w-3.5 h-3.5" />
                    Simpan sebagai Draft
                </button>
            </form>

            @include('admin.section-settings._list')
        </div>

        {{-- Tab: Elemen Halaman (placeholder — Iterasi 21) --}}
        <div x-show="tab === 'elemen'" x-cloak data-reveal class="flex flex-col items-center justify-center text-center py-16 bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50/80 text-indigo-600 flex items-center justify-center border border-indigo-100/60">
                <x-icon name="layers" class="w-6 h-6" />
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900">Elemen Halaman — Segera Hadir</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">Toggle CTA navbar, floating widget, dan social bar Hero direncanakan Iterasi 21.</p>
            </div>
        </div>

        {{-- Tab: Mode Situs (placeholder — Iterasi 22) --}}
        <div x-show="tab === 'mode'" x-cloak data-reveal class="flex flex-col items-center justify-center text-center py-16 bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xs space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50/80 text-indigo-600 flex items-center justify-center border border-indigo-100/60">
                <x-icon name="server" class="w-6 h-6" />
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900">Mode Situs — Segera Hadir</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">Mode maintenance ("Segera Hadir" untuk visitor, admin tetap bisa lihat situs normal) direncanakan Iterasi 22.</p>
            </div>
        </div>
    </div>
@endsection
