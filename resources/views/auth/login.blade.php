<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Sistem - E-Absensi Santri PP. Miftahul Huda Kroya</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-pesantren.png') }}">

    <!-- Theme initialization script (anti-flash) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        arabic: ['"Amiri"', 'serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body
    x-data="{
        darkMode: false,
        init() {
            this.darkMode = document.documentElement.classList.contains('dark');
        },
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        }
    }"
    class="min-h-full flex items-center justify-center p-4 text-slate-800 dark:text-slate-100 antialiased bg-gradient-to-br from-slate-100 via-emerald-50 to-slate-200 dark:from-slate-950 dark:via-emerald-950 dark:to-slate-900 transition-colors duration-200"
>

    <!-- Dark Mode Switcher di Pojok Kanan Atas -->
    <div class="fixed top-4 right-4 z-50">
        <button
            type="button"
            @click="toggleDarkMode()"
            class="p-2.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-slate-200 dark:border-slate-700 text-amber-500 dark:text-amber-400 shadow-md transition hover:scale-105 active:scale-95"
            :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
            aria-label="Toggle Theme"
        >
            <template x-if="darkMode">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </template>
            <template x-if="!darkMode">
                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </template>
        </button>
    </div>

    <div class="max-w-md w-full space-y-5 my-8">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="inline-flex w-20 h-20 rounded-full bg-white dark:bg-slate-800 items-center justify-center shadow-xl shadow-emerald-950/20 mb-3 ring-4 ring-emerald-500/30 p-1 overflow-hidden">
                <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo Pondok Pesantren Miftahul Huda Kroya" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">E-Absensi Santri</h1>
            <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mt-0.5 uppercase tracking-wide">Pondok Pesantren Miftahul Huda Kroya</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Sistem Presensi & Manajemen Santri Modern</p>
        </div>

        <!-- Alerts -->
        @if (session('error'))
            <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-300 text-xs font-semibold text-center">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-semibold text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 md:p-8 shadow-2xl border border-slate-200/80 dark:border-slate-800 transition-colors duration-200">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-1">Masuk ke Akun Anda</h2>
            <p class="text-xs text-slate-400 dark:text-slate-400 mb-5">Gunakan email terdaftar untuk mengakses hak akses Anda</p>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Email *</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="nama@pesantren.test"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition"
                    >
                    @error('email')
                        <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi *</label>
                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition"
                    >
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 cursor-pointer text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-sm tracking-wide shadow-md shadow-emerald-700/20 transition-all"
                >
                    Masuk Sekarang
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6 text-center">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200 dark:border-slate-800"></div></div>
                <span class="relative px-3 bg-white dark:bg-slate-900 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Akses Cepat Pengujian (4 Role PRD)</span>
            </div>

            <!-- Quick 1-Click Role Switcher Grid -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <!-- Super Admin -->
                <a href="{{ route('quick.login', 'super_admin') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition-all flex flex-col items-center text-center group">
                    <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400">👑 Super Admin</span>
                    <span class="text-[10px] text-slate-400">Pengurus Pusat</span>
                </a>

                <!-- Musyrif -->
                <a href="{{ route('quick.login', 'musyrif') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition-all flex flex-col items-center text-center group">
                    <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400">📋 Musyrif</span>
                    <span class="text-[10px] text-slate-400">Ustadz / Pengasuhan</span>
                </a>

                <!-- Keamanan -->
                <a href="{{ route('quick.login', 'keamanan') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-amber-500 dark:hover:border-amber-500 hover:bg-amber-50/50 dark:hover:bg-amber-950/30 transition-all flex flex-col items-center text-center group">
                    <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400">🛡️ Keamanan</span>
                    <span class="text-[10px] text-slate-400">Pos Gerbang</span>
                </a>

                <!-- Wali Santri -->
                <a href="{{ route('quick.login', 'wali_santri') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-blue-500 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-950/30 transition-all flex flex-col items-center text-center group">
                    <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">👨‍👩‍👧 Wali Santri</span>
                    <span class="text-[10px] text-slate-400">Portal Orang Tua</span>
                </a>

                <!-- Bendahara -->
                <a href="{{ route('quick.login', 'bendahara') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-teal-500 dark:hover:border-teal-500 hover:bg-teal-50/50 dark:hover:bg-teal-950/30 transition-all flex flex-col items-center text-center group col-span-2">
                    <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400">💰 Bendahara Pondok</span>
                    <span class="text-[10px] text-slate-400">Manajemen Keuangan, Tagihan & Rekap Pembayaran</span>
                </a>
            </div>

            <!-- Box Pendaftaran Akun Wali Santri Baru -->
            <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Wali santri baru belum punya akun?
                </p>
                <a href="{{ route('register.wali') }}" class="mt-2 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 hover:bg-blue-100 font-bold text-xs border border-blue-200 dark:border-blue-800 transition">
                    <span>Daftar Akun Wali Santri Baru</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 dark:text-slate-400">
            &copy; {{ date('Y') }} E-Absensi Santri &bull; Pondok Pesantren Miftahul Huda Kroya
        </p>
    </div>

</body>
</html>
