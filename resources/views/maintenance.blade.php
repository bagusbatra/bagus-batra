<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Segera Hadir — Bagus Batra</title>
    {{-- Halaman maintenance sengaja noindex — bukan konten permanen. --}}
    <meta name="robots" content="noindex, nofollow" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/public.js'])

    {{--
        Preset warna aksen — sama pola dgn resources/views/layouts/app.blade.php
        (Iterasi 19). $accentPreset dibagikan ke SEMUA view lewat
        App\Http\Middleware\HandleAppearancePreview, TERMASUK view ini (yang
        dirender langsung oleh App\Http\Middleware\CheckMaintenanceMode, di
        pipeline request yang sama, jadi view()->share() sebelumnya sudah
        berlaku).
    --}}
    @php $accentVars = \App\Support\AccentPreset::get($accentPreset ?? \App\Support\AccentPreset::DEFAULT); @endphp
    <style>
        :root {
            --accent-50: {{ $accentVars['50'] }};
            --accent-100: {{ $accentVars['100'] }};
            --accent-300: {{ $accentVars['300'] }};
            --accent-500: {{ $accentVars['500'] }};
            --accent-600: {{ $accentVars['600'] }};
            --accent-700: {{ $accentVars['700'] }};
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50/80 text-slate-800 flex flex-col items-center justify-center font-sans relative overflow-hidden px-4 selection:bg-indigo-500/15 selection:text-indigo-900">
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="ambient-blob ambient-blob-1 absolute -top-40 -right-40 w-[650px] h-[650px] bg-gradient-to-br from-indigo-200/40 via-purple-100/30 to-transparent rounded-full blur-3xl"></div>
        <div class="ambient-blob ambient-blob-2 absolute top-[35%] -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-blue-200/35 via-cyan-100/25 to-transparent rounded-full blur-3xl"></div>
        <div class="ambient-blob ambient-blob-3 absolute top-[70%] -right-32 w-[550px] h-[550px] bg-gradient-to-tl from-indigo-100/40 via-violet-100/30 to-transparent rounded-full blur-3xl"></div>
    </div>

    <button
        id="maintenance-lang-toggle-btn"
        x-data
        @click="$store.lang.toggle()"
        class="fixed top-5 right-5 z-10 flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-indigo-600 hover:bg-white/80 rounded-lg transition-colors border border-white/80 bg-white/50 backdrop-blur-md shadow-2xs cursor-pointer hover:scale-105 active:scale-95"
        :title="$store.lang.current === 'id' ? 'Switch to English' : 'Ganti ke Bahasa Indonesia'"
    >
        <x-icon name="globe" class="w-3.5 h-3.5 text-slate-500" />
        <span class="uppercase" x-text="$store.lang.current"></span>
    </button>

    <main x-data class="relative z-10 max-w-lg w-full bg-white/60 backdrop-blur-xl rounded-3xl border border-white/80 shadow-2xl p-8 sm:p-10 text-center space-y-5">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-[var(--accent-50)] text-[var(--accent-600)] flex items-center justify-center border border-[var(--accent-100)]">
            <x-icon name="wrench" class="w-8 h-8" />
        </div>

        <div class="space-y-2">
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                <span x-show="$store.lang.current === 'id'">Segera Hadir</span>
                <span x-show="$store.lang.current === 'en'" x-cloak>Coming Right Back</span>
            </h1>

            <p class="text-sm text-slate-600 leading-relaxed" x-show="$store.lang.current === 'id'">
                {{ $messageId ?: 'Situs sedang dalam pemeliharaan singkat untuk peningkatan. Mohon coba lagi dalam beberapa saat.' }}
            </p>
            <p class="text-sm text-slate-600 leading-relaxed" x-show="$store.lang.current === 'en'" x-cloak>
                {{ $messageEn ?: 'The site is undergoing brief maintenance for improvements. Please check back again shortly.' }}
            </p>
        </div>

        <div class="pt-4 border-t border-slate-200/70 text-[11px] text-slate-400">
            Bagus Batra &copy; {{ date('Y') }}
        </div>
    </main>
</body>
</html>
