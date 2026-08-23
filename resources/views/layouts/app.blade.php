<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bagus Batra — Senior Web Developer & Technical Writer</title>
    <meta name="description" content="Portfolio modern, clean, dan elegan untuk Senior Web Developer & Frontend Specialist. Menghadirkan solusi web berkinerja tinggi, UI intuitif, dan artikel teknologi terkini." />
    <meta property="og:title" content="Bagus Batra — Senior Web Developer Portfolio" />
    <meta property="og:description" content="Modern clean portfolio & tech blog for web development professional." />
    <meta property="og:type" content="website" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="appRoot()"
    class="min-h-screen bg-slate-50/80 text-slate-800 flex flex-col selection:bg-indigo-500/15 selection:text-indigo-900 font-sans relative overflow-x-hidden"
>
    {{-- Top Scroll Progress Indicator --}}
    <div
        class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 via-purple-500 to-blue-500 origin-left z-50 shadow-sm shadow-indigo-500/30"
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

    {{-- Floating Quick Action Widget (Back to Top & Quick Contact) --}}
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

    @include('portfolio.partials.footer')

    @include('portfolio.partials.cv-modal')
    @include('portfolio.partials.project-modal')
    @include('portfolio.partials.article-modal')
</body>
</html>
