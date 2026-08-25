<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {{--
        Iterasi 11 (Fase 2): title/description/og:* jadi dinamis per halaman lewat
        @section('meta_title'|'meta_description'|'meta_image') — dipakai oleh halaman
        detail Project (resources/views/projects/show.blade.php) untuk SEO. Halaman
        lain yang tidak set section ini otomatis jatuh ke default lama (tidak ada
        regresi pada `/` atau halaman lain).
    --}}
    <title>@hasSection('meta_title')@yield('meta_title')@else Bagus Batra — Senior Web Developer & Technical Writer @endif</title>
    <meta name="description" content="@hasSection('meta_description')@yield('meta_description')@else Portfolio modern, clean, dan elegan untuk Senior Web Developer & Frontend Specialist. Menghadirkan solusi web berkinerja tinggi, UI intuitif, dan artikel teknologi terkini. @endif" />
    <meta property="og:title" content="@hasSection('meta_title')@yield('meta_title')@else Bagus Batra — Senior Web Developer Portfolio @endif" />
    <meta property="og:description" content="@hasSection('meta_description')@yield('meta_description')@else Modern clean portfolio & tech blog for web development professional. @endif" />
    <meta property="og:type" content="website" />
    @hasSection('meta_image')
        <meta property="og:image" content="@yield('meta_image')" />
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/public.js'])

    {{--
        Preset warna aksen — Iterasi 19 (Fase 4). Nilai --accent-* di-set
        LEWAT INLINE <style> SERVER-RENDERED di sini (bukan lewat JS
        runtime) supaya tidak ada flash-of-wrong-color saat halaman
        pertama kali dimuat. $accentPreset dibagikan ke SEMUA view lewat
        App\Http\Middleware\HandleAppearancePreview (preview-aware: draft
        utk admin+preview aktif, live utk visitor biasa) — lihat
        App\Support\AccentPreset utk daftar 4 preset & nilai hex-nya.
        Diletakkan SETELAH @vite supaya urutan cascade CSS memenangkan nilai
        preset aktif di sini dibanding default statis di resources/css/app
        .css (keduanya sama-sama men-target :root, jadi yang terakhir di
        <head> yang menang).
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
<body
    x-data="appRoot()"
    data-reveal-enabled="{{ ($animationsEnabled ?? true) ? '1' : '0' }}"
    class="min-h-screen bg-slate-50/80 text-slate-800 flex flex-col selection:bg-indigo-500/15 selection:text-indigo-900 font-sans relative overflow-x-hidden"
>
    {{--
        Banner mode Draft/Preview — Iterasi 18 (Fase 4). $appearancePreview
        dibagikan ke SEMUA view lewat App\Http\Middleware\HandleAppearancePreview
        (didaftarkan di grup middleware "web", lihat bootstrap/app.php), jadi
        tersedia di layout manapun tanpa perlu di-pass eksplisit dari tiap
        controller. HANYA tampil kalau mode preview benar-benar aktif (admin
        login + flag session preview menyala) — visitor biasa tidak pernah
        melihat banner ini. Sticky di atas scroll-progress-bar (z lebih
        tinggi), warna amber supaya jelas beda dari progress bar indigo.
    --}}
    @if ($appearancePreview ?? false)
        <div class="sticky top-0 inset-x-0 z-[60] bg-amber-500 text-amber-950 text-xs sm:text-sm font-bold px-4 py-2 flex items-center justify-center gap-3 flex-wrap shadow-sm">
            <span class="flex items-center gap-1.5">
                <x-icon name="eye" class="w-4 h-4 shrink-0" />
                Anda sedang melihat mode Draft/Preview — pengunjung biasa TIDAK melihat perubahan ini sampai di-publish.
            </span>
            <a href="{{ request()->fullUrlWithQuery(['preview' => 0]) }}" class="underline hover:no-underline">
                Keluar dari Mode Preview
            </a>
        </div>
    @endif

    {{--
        Top Scroll Progress Indicator — diubah dari `fixed` jadi `sticky`
        (Iterasi 18, Fase 4) supaya SELALU berada tepat di bawah banner
        Draft/Preview di atas ini kalau banner sedang tampil, alih-alih
        tumpang tindih di posisi yang sama (dua elemen `fixed top-0` akan
        saling menutupi). `sticky` pada sibling normal-flow tetap
        menghasilkan efek "selalu terlihat saat scroll" yang sama seperti
        `fixed` untuk elemen setipis ini, karena elemen sebelumnya (banner)
        sudah menempati ruang di atasnya duluan dalam alur dokumen.
    --}}
    <div
        class="sticky top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 via-purple-500 to-blue-500 origin-left z-50 shadow-sm shadow-indigo-500/30"
        :style="'transform: scaleX(' + scrollProgress + '); transition: transform 120ms linear;'"
    ></div>

    {{-- Ambient background animated light gradients for living frosted glass diffusion --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="ambient-blob ambient-blob-1 absolute -top-40 -right-40 w-[650px] h-[650px] bg-gradient-to-br from-indigo-200/40 via-purple-100/30 to-transparent rounded-full blur-3xl"></div>
        <div class="ambient-blob ambient-blob-2 absolute top-[35%] -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-blue-200/35 via-cyan-100/25 to-transparent rounded-full blur-3xl"></div>
        <div class="ambient-blob ambient-blob-3 absolute top-[70%] -right-32 w-[550px] h-[550px] bg-gradient-to-tl from-indigo-100/40 via-violet-100/30 to-transparent rounded-full blur-3xl"></div>
    </div>

    @include('portfolio.partials.navbar')

    <main class="flex-1 relative z-10">
        @yield('content')
    </main>

    {{--
        Floating Quick Action Widget (Back to Top & Quick Contact) —
        Iterasi 21 (Fase 4, Bagian A): toggle `floatingWidgetVisible`.
        Dibungkus @if di level SERVER (bukan cuma Alpine x-show yang murni
        client-side scroll-based) supaya benar2 tidak ada di DOM sama sekali
        saat setting dimatikan (draft-aware lewat
        App\Http\Middleware\HandleAppearancePreview, pola sama persis
        $animationsEnabled/$navbarCtaVisible).
    --}}
    @if ($floatingWidgetVisible ?? true)
        <div
            x-show="showFloatingWidget"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-75 translate-y-5"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-75 translate-y-5"
            class="fixed bottom-6 right-6 z-40 flex flex-col items-center gap-2.5"
            style="display: none;"
        >
            <button
                @click="scrollTo('contact')"
                class="p-3.5 rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 hover:scale-108 active:scale-92 transition-all cursor-pointer border border-indigo-400/40 backdrop-blur-md flex items-center justify-center group"
                :title="$store.lang.current === 'id' ? 'Kirim Pesan / Kontak' : 'Contact Me'"
            >
                <x-icon name="message-square" class="w-5 h-5 group-hover:scale-110 transition-transform" />
            </button>

            <button
                @click="scrollToTop()"
                class="p-3.5 rounded-2xl bg-white/80 hover:bg-white hover:scale-108 active:scale-92 text-slate-700 shadow-lg shadow-slate-300/40 border border-white/90 backdrop-blur-md transition-all cursor-pointer flex items-center justify-center group"
                :title="$store.lang.current === 'id' ? 'Kembali ke atas' : 'Back to top'"
            >
                <x-icon name="arrow-up" class="w-5 h-5 text-indigo-600 group-hover:-translate-y-0.5 transition-transform" />
            </button>
        </div>
    @endif

    @include('portfolio.partials.footer')

    {{-- project-modal.blade.php dicabut di Iterasi 11 (Fase 2) — case-study
         project sekarang halaman sungguhan (/projects/{project_key}), bukan
         modal. article-modal (Blog) TIDAK disentuh, tetap di luar scope Fase 2. --}}
    @include('portfolio.partials.cv-modal')
    @include('portfolio.partials.article-modal')
</body>
</html>
