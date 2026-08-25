<section id="contact" x-data="contactSection('{{ old('project_type', 'Full-Stack App') }}')" class="py-20 sm:py-24 bg-transparent relative overflow-hidden">
    {{-- Background radial highlight --}}
    <div class="absolute top-1/2 right-0 w-96 h-96 bg-indigo-100/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 sm:space-y-16 relative z-10">
        {{-- Header --}}
        <div data-reveal class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-[var(--accent-50)]/80 backdrop-blur-md text-[var(--accent-700)] border border-[var(--accent-100)] text-xs font-bold uppercase tracking-wider">
                <x-icon name="mail" class="w-3.5 h-3.5" />
                <span x-show="$store.lang.current === 'id'">Mulai Kolaborasi</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Get In Touch</span>
            </div>
            {{--
                Iterasi 21 (Fase 4, Bagian B): custom heading/subheading,
                fallback ke teks hardcoded kalau kosong — lihat
                docs/LOG-ITERASI.md entri Iterasi 21. Default EN memakai
                `{!! !!}` (raw output) + `e()` manual pada bagian dinamisnya
                SENGAJA (BUKAN pola {{ }} biasa yang dipakai section lain) —
                supaya entity `&rsquo;` (kutip lengkung) di teks default asli
                tetap PERSIS sama byte-nya dgn sebelum Iterasi 21 saat
                fallback aktif (kalau ditulis sbg karakter U+2019 literal di
                dalam {{ }}, hasil byte HTML-nya beda dgn entity aslinya
                meski tampil identik di browser — merusak verifikasi
                byte-per-byte "kosongkan field -> kembali PERSIS ke default
                asli"). Custom teks admin (bagian dinamis) tetap di-escape
                manual via e() supaya tidak ada risiko XSS.
            --}}
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                <span x-show="$store.lang.current === 'id'">{{ $topLevelSections['contact']->effective('heading_id', $appearancePreview ?? false) ?: 'Punya Ide Proyek Hebat? Mari Kita Wujudkan Bersama.' }}</span>
                @php $contactHeadingEn = $topLevelSections['contact']->effective('heading_en', $appearancePreview ?? false); @endphp
                <span x-show="$store.lang.current === 'en'" x-cloak>{!! $contactHeadingEn ? e($contactHeadingEn) : 'Have a Project in Mind? Let&rsquo;s Build Something Exceptional.' !!}</span>
            </h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                <span x-show="$store.lang.current === 'id'">{{ $topLevelSections['contact']->effective('subheading_id', $appearancePreview ?? false) ?: 'Tersedia untuk proyek freelance terpilih, kontrak konsultasi arsitektur frontend, maupun peran full-time strategis. Respon dalam < 24 jam.' }}</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>{{ $topLevelSections['contact']->effective('subheading_en', $appearancePreview ?? false) ?: 'Open for selected contract builds, architectural consultations, and full-time remote engineering roles.' }}</span>
            </p>
        </div>

        {{-- Main Grid: Info + Form --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            {{-- Left Column: Direct Contacts & Socials --}}
            <div data-reveal class="lg:col-span-5 space-y-6">
                <div class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-8 border border-white/80 shadow-2xs space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">
                        <span x-show="$store.lang.current === 'id'">Kontak Langsung &amp; Konsultasi</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Direct Contacts &amp; Channels</span>
                    </h3>

                    <div class="space-y-3 sm:space-y-4">
                        {{-- Email Direct Card --}}
                        <div class="p-3.5 sm:p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 shadow-2xs flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50/80 text-indigo-600 flex items-center justify-center border border-indigo-100/50 shrink-0">
                                    <x-icon name="mail" class="w-5 h-5" />
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase">Email Resmi</div>
                                    <a href="mailto:{{ $personalInfo['email'] }}" class="text-xs font-mono font-bold text-slate-800 hover:text-indigo-600 truncate block">{{ $personalInfo['email'] }}</a>
                                </div>
                            </div>
                            <button @click="copyEmail('{{ $personalInfo['email'] }}')" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-white/90 rounded-lg transition-colors cursor-pointer border border-transparent hover:border-white/80 shrink-0" title="Salin Email">
                                <x-icon name="check" class="w-4 h-4 text-emerald-600" x-show="copiedEmail" />
                                <x-icon name="copy" class="w-4 h-4" x-show="!copiedEmail" />
                            </button>
                        </div>

                        {{-- WhatsApp Direct Card --}}
                        <a href="https://wa.me/6281234567890?text=Halo%20Bagus,%20saya%20tertarik%20untuk%20bekerjasama" target="_blank" rel="noopener noreferrer" class="p-3.5 sm:p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 shadow-2xs hover:border-emerald-300 transition-colors flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center border border-emerald-100/50 shrink-0">
                                    <x-icon name="phone" class="w-5 h-5" />
                                </div>
                                <div>
                                    <div class="text-[11px] font-bold text-slate-400 uppercase">WhatsApp Chat</div>
                                    <div class="text-xs font-mono font-bold text-slate-800 group-hover:text-emerald-600">+62 812-3456-7890</div>
                                </div>
                            </div>
                            <x-icon name="external-link" class="w-4 h-4 text-slate-400 group-hover:text-emerald-600" />
                        </a>

                        {{-- Location Card --}}
                        <div class="p-3.5 sm:p-4 bg-white/70 backdrop-blur-md rounded-2xl border border-white/90 shadow-2xs flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50/80 text-blue-600 flex items-center justify-center border border-blue-100/50 shrink-0">
                                <x-icon name="map-pin" class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-slate-400 uppercase">Lokasi Kerja</div>
                                <div class="text-xs font-semibold text-slate-800">{{ $personalInfo['location'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Book 15-min Discovery Call Card --}}
                    <div class="p-5 sm:p-5.5 bg-slate-900/90 backdrop-blur-md text-white rounded-3xl shadow-lg border border-white/10 space-y-3">
                        <div class="flex items-center gap-2 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                            <x-icon name="calendar" class="w-4 h-4" />
                            <span>1-on-1 Discovery Call</span>
                        </div>
                        <h4 class="font-bold text-sm">
                            <span x-show="$store.lang.current === 'id'">Jadwalkan Konsultasi 15 Menit</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Book a 15-Min Quick Intro Call</span>
                        </h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            <span x-show="$store.lang.current === 'id'">Diskusikan kebutuhan arsitektur produk, timeline, atau tanya jawab teknis tanpa komitmen.</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Quick synchronous sync to discuss technical feasibility, timelines, and budget.</span>
                        </p>
                        <button id="open-calendar-call-btn" @click="calendarOpen = true" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer flex items-center justify-center gap-1.5 shadow-md shadow-indigo-600/30">
                            <x-icon name="calendar" class="w-3.5 h-3.5" />
                            <span x-show="$store.lang.current === 'id'">Pilih Jadwal Diskusi</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Select Time Slot</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <x-icon name="shield-check" class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>NDA signed upon request. Zero spam guarantee.</span>
                    </div>
                </div>
            </div>

            {{-- Right Column: Interactive Inquiry Form --}}
            <div data-reveal class="lg:col-span-7">
                <div class="bg-white/60 backdrop-blur-xl rounded-3xl p-5 sm:p-9 border border-white/80 shadow-xl shadow-slate-200/50 space-y-6">
                    <div class="space-y-1">
                        <h3 class="text-xl font-extrabold text-slate-900">
                            <span x-show="$store.lang.current === 'id'">Kirim Pesan atau Brief Proyek</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Send Message or Project Brief</span>
                        </h3>
                        <p class="text-xs text-slate-500">
                            <span x-show="$store.lang.current === 'id'">Isi formulir di bawah untuk mendapatkan estimasi penawaran dan solusi teknis.</span>
                            <span x-show="$store.lang.current === 'en'" x-cloak>Fill out the form below to receive project estimates and architectural feedback.</span>
                        </p>
                    </div>

                    @if (session('contact_success'))
                        <div class="p-6 sm:p-8 text-center bg-emerald-50/80 backdrop-blur-md rounded-2xl border border-emerald-200 space-y-4">
                            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto shadow-md shadow-emerald-600/30">
                                <x-icon name="check-circle-2" class="w-6 h-6" />
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-lg font-bold text-emerald-950">
                                    <span x-show="$store.lang.current === 'id'">Pesan Berhasil Terkirim!</span>
                                    <span x-show="$store.lang.current === 'en'" x-cloak>Message Received Successfully!</span>
                                </h4>
                                <p class="text-xs text-emerald-800 max-w-md mx-auto">
                                    <span x-show="$store.lang.current === 'id'">Terima kasih {{ session('contact_name') }}. Saya akan meninjau rincian proyek Anda dan membalas melalui {{ session('contact_email') }} dalam kurun waktu kurang dari 24 jam.</span>
                                    <span x-show="$store.lang.current === 'en'" x-cloak>Thank you {{ session('contact_name') }}. I will review your requirements and respond back to {{ session('contact_email') }} within 24 hours.</span>
                                </p>
                            </div>
                            <a href="{{ url('/') }}#contact" class="inline-block px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer shadow-xs">
                                <span x-show="$store.lang.current === 'id'">Kirim Pesan Lain</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Send Another Message</span>
                            </a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-800">
                                        <span x-show="$store.lang.current === 'id'">Nama Lengkap *</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Full Name *</span>
                                    </label>
                                    <input type="text" name="name" required placeholder="e.g. Raditya Pratama" value="{{ old('name') }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                                    @error('name') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-800">
                                        <span x-show="$store.lang.current === 'id'">Alamat Email *</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Email Address *</span>
                                    </label>
                                    <input type="email" name="email" required placeholder="e.g. name@company.com" value="{{ old('email') }}" class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors" />
                                    @error('email') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Project Type Selector --}}
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-800">
                                    <span x-show="$store.lang.current === 'id'">Kategori Kebutuhan Proyek</span>
                                    <span x-show="$store.lang.current === 'en'" x-cloak>Project Scope &amp; Type</span>
                                </label>
                                <input type="hidden" name="project_type" :value="projectType" />
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach (['Full-Stack App', 'Frontend System', 'Design System', 'Consulting / Audit'] as $t)
                                        <button
                                            type="button"
                                            @click="projectType = '{{ $t }}'"
                                            class="p-2.5 rounded-xl text-xs font-semibold border text-center transition-all cursor-pointer"
                                            :class="projectType === '{{ $t }}' ? 'bg-indigo-50/90 backdrop-blur-md border-indigo-400 text-indigo-700 font-bold shadow-2xs' : 'bg-white/60 backdrop-blur-xs border-white/80 text-slate-600 hover:bg-white/90'"
                                        >{{ $t }}</button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Budget Selector --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-800">
                                        <span x-show="$store.lang.current === 'id'">Estimasi Budget</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Estimated Budget</span>
                                    </label>
                                    <select name="budget" class="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
                                        <option>&lt; $3k (IDR 20M - 45M)</option>
                                        <option selected>$3k - $8k (IDR 45M - 120M)</option>
                                        <option>$8k - $15k (IDR 120M - 230M)</option>
                                        <option>&gt; $15k (Enterprise Scale)</option>
                                    </select>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-800">
                                        <span x-show="$store.lang.current === 'id'">Target Waktu (Timeline)</span>
                                        <span x-show="$store.lang.current === 'en'" x-cloak>Desired Timeline</span>
                                    </label>
                                    <select name="timeline" class="w-full px-3.5 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs">
                                        <option>Segera (&lt; 1 Bulan)</option>
                                        <option selected>1 - 2 Bulan</option>
                                        <option>3 - 6 Bulan</option>
                                        <option>Jangka Panjang / Retainer</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Message Body --}}
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-800">
                                    <span x-show="$store.lang.current === 'id'">Deskripsi Proyek &amp; Tujuan Utama *</span>
                                    <span x-show="$store.lang.current === 'en'" x-cloak>Project Details &amp; Goals *</span>
                                </label>
                                <textarea
                                    name="message"
                                    required
                                    rows="4"
                                    :placeholder="$store.lang.current === 'id' ? 'Ceritakan tentang produk yang ingin dibangun, target pengguna, fitur utama, atau kendala performa yang dialami saat ini...' : 'Tell me about the product you want to build, user goals, key milestones...'"
                                    class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-xs text-slate-800 focus:bg-white focus:outline-indigo-500 resize-none transition-colors shadow-2xs"
                                >{{ old('message') }}</textarea>
                                @error('message') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <button id="contact-form-submit-btn" type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-indigo-600 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <x-icon name="send" class="w-4 h-4" />
                                <span x-show="$store.lang.current === 'id'">Kirim Brief &amp; Mulai Diskusi</span>
                                <span x-show="$store.lang.current === 'en'" x-cloak>Send Brief &amp; Start Discussion</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Social Media Integration Grid Section --}}
        <div data-reveal class="pt-8 border-t border-slate-200/80 space-y-6">
            <div class="text-center max-w-2xl mx-auto space-y-1.5">
                <h3 class="text-xl font-extrabold text-slate-900">
                    <span x-show="$store.lang.current === 'id'">Temukan &amp; Terhubung di Platform Lain</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Connect Across All Digital Channels</span>
                </h3>
                <p class="text-xs text-slate-500">
                    <span x-show="$store.lang.current === 'id'">Ikuti kabar terbaru seputar artikel teknologi, live code streaming, dan update repositori.</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Follow tech blogs, GitHub open source repos, and engineering updates.</span>
                </p>
            </div>

            @include('portfolio.partials.social-bar', ['variant' => 'cards'])
        </div>
    </div>

    {{-- Discovery Call Booking Modal --}}
    <div
        x-show="calendarOpen"
        x-transition
        @click="calendarOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div @click.stop class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2 text-indigo-600">
                    <x-icon name="calendar" class="w-5 h-5" />
                    <h4 class="font-bold text-slate-900 text-base">Schedule 15-min Call</h4>
                </div>
                <button @click="calendarOpen = false" class="text-slate-400 hover:text-slate-700 text-xs font-bold">&#10005;</button>
            </div>

            <template x-if="callBooked">
                <div class="text-center py-6 space-y-2">
                    <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                        <x-icon name="check" class="w-6 h-6" />
                    </div>
                    <h5 class="font-bold text-slate-900 text-sm">Jadwal Berhasil Dikonfirmasi!</h5>
                    <p class="text-xs text-slate-500">Undangan Google Meet telah dikirim ke kalender Anda untuk sesi <strong x-text="selectedSlot"></strong>.</p>
                </div>
            </template>

            <template x-if="!callBooked">
                <form @submit.prevent="bookCall()" class="space-y-4">
                    <p class="text-xs text-slate-600">Pilih slot waktu yang cocok untuk diskusi teknis via Google Meet:</p>

                    <div class="space-y-2">
                        <template x-for="slot in ['Senin, 10:00 - 10:15 WIB (GMT+7)', 'Selasa, 14:30 - 14:45 WIB (GMT+7)', 'Rabu, 16:00 - 16:15 WIB (GMT+7)', 'Kamis, 11:00 - 11:15 WIB (GMT+7)']" :key="slot">
                            <div
                                @click="selectedSlot = slot"
                                class="p-3 rounded-xl border text-xs font-medium cursor-pointer transition-all"
                                :class="selectedSlot === slot ? 'bg-indigo-50 border-indigo-600 text-indigo-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'"
                                x-text="slot"
                            ></div>
                        </template>
                    </div>

                    <button type="submit" :disabled="!selectedSlot" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-colors disabled:opacity-50 cursor-pointer">
                        Konfirmasi Slot Pertemuan
                    </button>
                </form>
            </template>
        </div>
    </div>
</section>
