<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard') — Admin Bagus Batra</title>
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-50/80 text-slate-800 font-sans relative overflow-x-hidden selection:bg-indigo-500/15 selection:text-indigo-900">

    {{-- Ambient background, consistent with the public site --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="ambient-blob ambient-blob-1 absolute -top-40 -right-40 w-[650px] h-[650px] bg-gradient-to-br from-indigo-200/40 via-purple-100/30 to-transparent rounded-full blur-3xl"></div>
        <div class="ambient-blob ambient-blob-2 absolute top-[35%] -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-blue-200/35 via-cyan-100/25 to-transparent rounded-full blur-3xl"></div>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-xs lg:hidden"
        style="display: none;"
    ></div>

    @php
        $menu = [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout'],
            ['route' => 'admin.profile', 'label' => 'Profil & Hero', 'icon' => 'user'],
            ['route' => 'admin.social-links', 'label' => 'Social Links', 'icon' => 'globe'],
            ['route' => 'admin.about-skills', 'label' => 'About & Skills', 'icon' => 'cpu'],
            ['route' => 'admin.projects', 'label' => 'Projects', 'icon' => 'folder-git-2'],
            ['route' => 'admin.playground', 'label' => 'Playground', 'icon' => 'zap'],
            ['route' => 'admin.experience', 'label' => 'Experience', 'icon' => 'briefcase'],
            ['route' => 'admin.blog', 'label' => 'Blog', 'icon' => 'book-open'],
            ['route' => 'admin.testimonials', 'label' => 'Testimonials', 'icon' => 'quote'],
            ['route' => 'admin.messages', 'label' => 'Pesan Masuk', 'icon' => 'mail'],
            ['route' => 'admin.section-settings', 'label' => 'Pengaturan Section', 'icon' => 'sliders'],
        ];
    @endphp

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-72 bg-white/80 backdrop-blur-xl border-r border-white/80 shadow-xl shadow-slate-200/40 flex flex-col transition-transform duration-300 ease-out lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex items-center justify-between px-5 h-16 border-b border-slate-200/70 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0">
                    <x-icon name="shield-check" class="w-5 h-5" />
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-extrabold text-slate-900">Bagus Batra</div>
                    <div class="text-[10px] font-semibold text-indigo-600 uppercase tracking-wider">Admin Panel</div>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1">
            @foreach ($menu as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs($item['route']) ? 'bg-indigo-50/90 text-indigo-700 shadow-2xs border border-indigo-100' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 border border-transparent' }}"
                >
                    <x-icon :name="$item['icon']" class="w-4.5 h-4.5 shrink-0" />
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="p-3.5 border-t border-slate-200/70 shrink-0">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50/80 transition-colors cursor-pointer">
                    <x-icon name="log-out" class="w-4.5 h-4.5 shrink-0" />
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content area --}}
    <div class="relative z-10 lg:pl-72 flex flex-col min-h-screen">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 h-16 flex items-center gap-4 px-4 sm:px-6 bg-white/70 backdrop-blur-xl border-b border-white/80 shadow-2xs">
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-800 p-1.5 -ml-1.5">
                <x-icon name="menu" class="w-5.5 h-5.5" />
            </button>

            <h1 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight flex-1">
                @yield('title', 'Dashboard')
            </h1>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="leading-tight">
                        <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="text-[10px] text-slate-500">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50/80 rounded-lg transition-colors cursor-pointer">
                        <x-icon name="log-out" class="w-4.5 h-4.5" />
                    </button>
                </form>
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('success') || session('error'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="mx-4 sm:mx-6 mt-4"
            >
                @if (session('success'))
                    <div class="flex items-center justify-between gap-3 p-3.5 rounded-xl bg-emerald-50/90 backdrop-blur-md border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-2xs">
                        <span class="flex items-center gap-2"><x-icon name="check-circle-2" class="w-4 h-4 shrink-0" /> {{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-600 hover:text-emerald-900"><x-icon name="x" class="w-3.5 h-3.5" /></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="flex items-center justify-between gap-3 p-3.5 rounded-xl bg-rose-50/90 backdrop-blur-md border border-rose-200 text-rose-800 text-xs font-semibold shadow-2xs">
                        <span class="flex items-center gap-2"><x-icon name="x" class="w-4 h-4 shrink-0" /> {{ session('error') }}</span>
                        <button @click="show = false" class="text-rose-600 hover:text-rose-900"><x-icon name="x" class="w-3.5 h-3.5" /></button>
                    </div>
                @endif
            </div>
        @endif

        {{-- Page content — reveal-on-scroll active via the shared [data-reveal] mechanism --}}
        <main x-data="revealOnScroll" class="flex-1 p-4 sm:p-6 space-y-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
