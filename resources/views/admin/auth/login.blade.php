<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login — Bagus Batra Portfolio</title>
    <meta name="robots" content="noindex, nofollow" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50/80 text-slate-800 flex items-center justify-center font-sans relative overflow-hidden px-4 py-12">
    {{-- Ambient background, consistent with the public site --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="ambient-blob ambient-blob-1 absolute -top-40 -right-40 w-[650px] h-[650px] bg-gradient-to-br from-indigo-200/40 via-purple-100/30 to-transparent rounded-full blur-3xl"></div>
        <div class="ambient-blob ambient-blob-2 absolute top-[35%] -left-40 w-[600px] h-[600px] bg-gradient-to-tr from-blue-200/35 via-cyan-100/25 to-transparent rounded-full blur-3xl"></div>
        <div class="ambient-blob ambient-blob-3 absolute top-[70%] -right-32 w-[550px] h-[550px] bg-gradient-to-tl from-indigo-100/40 via-violet-100/30 to-transparent rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md space-y-6">
        {{-- Brand mark --}}
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-900/20 mb-1">
                <x-icon name="shield-check" class="w-6 h-6" />
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Bagus Batra — Admin Panel</h1>
            <p class="text-xs text-slate-500">Masuk untuk mengelola konten portfolio</p>
        </div>

        <div class="bg-white/60 backdrop-blur-xl rounded-3xl p-6 sm:p-8 border border-white/80 shadow-2xl shadow-slate-200/50 space-y-6">
            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50/90 border border-rose-200 text-rose-700 text-xs font-semibold flex items-start gap-2">
                    <x-icon name="x" class="w-4 h-4 shrink-0 mt-0.5" />
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50/90 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-start gap-2">
                    <x-icon name="check-circle-2" class="w-4 h-4 shrink-0 mt-0.5" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-slate-800">Alamat Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="username"
                        value="{{ old('email') }}"
                        placeholder="admin@bagusbatra.dev"
                        class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors"
                    />
                    @error('email') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-800">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••••"
                        class="w-full px-4 py-2.5 bg-white/70 backdrop-blur-md rounded-xl border border-white/90 text-sm text-slate-800 focus:bg-white focus:outline-indigo-500 shadow-2xs transition-colors"
                    />
                    @error('password') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                </div>

                <label class="flex items-center gap-2 text-xs text-slate-600 font-medium select-none cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                    Ingat saya di perangkat ini
                </label>

                <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="log-out" class="w-4 h-4 rotate-180" />
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400">
            <a href="{{ url('/') }}" class="hover:text-indigo-600 transition-colors">&larr; Kembali ke halaman publik</a>
        </p>
    </div>
</body>
</html>
