@php
    $iconMap = [
        'Github' => 'github',
        'Linkedin' => 'linkedin',
        'Twitter' => 'twitter',
        'Youtube' => 'youtube',
        'Instagram' => 'instagram',
        'Mail' => 'mail',
    ];
@endphp

@if (($variant ?? 'horizontal') === 'cards')
    <div id="social-integration-grid" x-data="socialBar()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($socialLinks as $link)
            <a
                id="social-card-{{ strtolower($link['platform']) }}"
                href="{{ $link['url'] }}"
                target="_blank"
                rel="noopener noreferrer"
                class="group p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 hover:border-indigo-300 shadow-2xs hover:shadow-md transition-all duration-300 flex items-start gap-3.5 relative overflow-hidden"
            >
                <div class="w-11 h-11 rounded-xl bg-white/80 border border-white flex items-center justify-center text-slate-700 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-colors duration-200 shrink-0 shadow-2xs">
                    <x-icon :name="$iconMap[$link['icon']] ?? 'external-link'" class="w-4 h-4" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $link['name'] }}</h4>
                        <x-icon name="external-link" class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                    </div>
                    <p class="text-xs font-mono text-indigo-600 font-medium truncate mt-0.5">{{ $link['username'] }}</p>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $link['description'] }}</p>
                </div>
            </a>
        @endforeach

        {{-- Quick WhatsApp Action Card --}}
        <a
            id="social-card-whatsapp"
            href="https://wa.me/6281234567890?text=Halo%20Bagus,%20saya%20tertarik%20untuk%20bekerjasama%20proyek%20web"
            target="_blank"
            rel="noopener noreferrer"
            class="group p-4.5 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/80 hover:border-emerald-300 shadow-2xs hover:shadow-md transition-all duration-300 flex items-start gap-3.5"
        >
            <div class="w-11 h-11 rounded-xl bg-emerald-50/80 backdrop-blur-md border border-emerald-100/60 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-200 shrink-0 shadow-2xs">
                <x-icon name="message-circle" class="w-5 h-5" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">WhatsApp Direct</h4>
                    <x-icon name="external-link" class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-600 transition-transform" />
                </div>
                <p class="text-xs font-mono text-emerald-600 font-medium truncate mt-0.5">+62 812-3456-7890</p>
                <p class="text-xs text-slate-500 mt-1">
                    <span x-show="$store.lang.current === 'id'">Chat instan untuk diskusi cepat</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Instant chat for fast inquiries</span>
                </p>
            </div>
        </a>

        {{-- Copy Email Card --}}
        <button
            id="social-card-copy-email"
            type="button"
            @click="copyEmail('{{ $personalInfo['email'] }}')"
            class="group p-4.5 bg-gradient-to-br from-indigo-50/70 to-blue-50/70 backdrop-blur-lg rounded-2xl border border-indigo-100/90 hover:border-indigo-300 shadow-2xs hover:shadow-md transition-all duration-300 flex items-start gap-3.5 text-left cursor-pointer"
        >
            <div class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20">
                <x-icon name="check" class="w-5 h-5" x-show="copiedEmail" x-cloak />
                <x-icon name="copy" class="w-5 h-5" x-show="!copiedEmail" />
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-indigo-950">
                        <template x-if="copiedEmail"><span x-show="$store.lang.current === 'id'">Tersalin ke Clipboard!</span></template>
                        <template x-if="copiedEmail"><span x-show="$store.lang.current === 'en'" x-cloak>Copied to Clipboard!</span></template>
                        <template x-if="!copiedEmail"><span x-show="$store.lang.current === 'id'">Salin Alamat Email</span></template>
                        <template x-if="!copiedEmail"><span x-show="$store.lang.current === 'en'" x-cloak>Copy Email Address</span></template>
                    </h4>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 bg-indigo-100/80 backdrop-blur-xs text-indigo-700 rounded-md border border-indigo-200/50" x-text="copiedEmail ? 'Copied' : 'Click'"></span>
                </div>
                <p class="text-xs font-mono text-indigo-700 font-medium truncate mt-0.5">{{ $personalInfo['email'] }}</p>
                <p class="text-xs text-indigo-900/60 mt-1">
                    <span x-show="$store.lang.current === 'id'">Klik untuk menyalin alamat email langsung</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Click to copy direct email address</span>
                </p>
            </div>
        </button>
    </div>
@else
    <div id="social-links-bar" x-data="socialBar()" class="flex flex-wrap items-center gap-2">
        @foreach ($socialLinks as $link)
            <a
                id="social-link-{{ strtolower($link['platform']) }}"
                href="{{ $link['url'] }}"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 px-3 py-2 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-slate-700 hover:text-indigo-600 hover:border-indigo-200 hover:bg-white transition-all text-xs font-semibold shadow-2xs"
                title="{{ $link['name'] }}: {{ $link['username'] }}"
            >
                <x-icon :name="$iconMap[$link['icon']] ?? 'external-link'" class="w-4 h-4" />
                <span class="hidden sm:inline">{{ $link['name'] }}</span>
            </a>
        @endforeach

        <button
            id="quick-copy-email-btn"
            type="button"
            @click="copyEmail('{{ $personalInfo['email'] }}')"
            class="flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50/80 backdrop-blur-md border border-indigo-200/80 text-indigo-700 hover:bg-indigo-100/90 rounded-xl transition-all text-xs font-semibold cursor-pointer shadow-2xs"
            title="Klik untuk menyalin email"
        >
            <template x-if="copiedEmail">
                <span class="flex items-center gap-1.5">
                    <x-icon name="check" class="w-3.5 h-3.5 text-emerald-600" />
                    <span class="text-emerald-700">
                        <span x-show="$store.lang.current === 'id'">Email Tersalin!</span>
                        <span x-show="$store.lang.current === 'en'" x-cloak>Copied!</span>
                    </span>
                </span>
            </template>
            <template x-if="!copiedEmail">
                <span class="flex items-center gap-1.5">
                    <x-icon name="copy" class="w-3.5 h-3.5" />
                    <span x-show="$store.lang.current === 'id'">Salin Email</span>
                    <span x-show="$store.lang.current === 'en'" x-cloak>Copy Email</span>
                </span>
            </template>
        </button>
    </div>
@endif
