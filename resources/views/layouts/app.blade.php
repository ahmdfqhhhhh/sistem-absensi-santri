<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Absensi Santri - PP. Miftahul Huda Kroya')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-pesantren.png') }}">

    <!-- Theme initialization script (mencegah flash saat reload halaman) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts: Plus Jakarta Sans & Amiri -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN Fallback & Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        arabic: ['"Amiri"', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js untuk Switch Dark/Light Mode & Interaktivitas UI -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .arabic-text {
            font-family: 'Amiri', serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
        }
    </style>
    @stack('styles')
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
    class="h-full flex flex-col text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 antialiased pb-20 md:pb-0 transition-colors duration-200"
>

    <!-- Top Navigation Bar (Floating Pill matching exact width of page content) -->
    <div class="no-print sticky top-0 z-40 w-full bg-slate-50/80 dark:bg-slate-950/80 backdrop-blur-sm transition-colors duration-200">
        <div class="w-full max-w-7xl mx-auto px-2.5 sm:px-6 lg:px-8 py-2 sm:py-3">
            <header class="bg-brand-900/95 dark:bg-slate-900/95 backdrop-blur-md text-white border border-brand-800/80 dark:border-slate-800 shadow-xl rounded-2xl md:rounded-3xl transition-colors duration-200">
                <div class="px-2.5 sm:px-4 lg:px-5">
                    <div class="flex items-center justify-between h-13 sm:h-16 gap-1 sm:gap-2">
                        <!-- Brand / Logo -->
                        <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                            <a href="{{ Auth::check() && Auth::user()->isWali() ? route('wali.portal') : (Auth::check() && Auth::user()->isKeamanan() ? route('kamtib.index') : (Auth::check() && Auth::user()->isBendahara() ? route('bendahara.index') : url('/'))) }}" class="flex items-center space-x-2 sm:space-x-3 group">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/10 dark:bg-slate-800 p-0.5 flex items-center justify-center shadow-lg shadow-emerald-950/30 ring-2 ring-emerald-400/40 group-hover:scale-105 transition-transform overflow-hidden shrink-0">
                                    <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo PP. Miftahul Huda Kroya" class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <div class="flex items-center space-x-1 sm:space-x-1.5">
                                        <span class="font-extrabold text-sm sm:text-base lg:text-lg tracking-tight text-white">E-Absensi</span>
                                        <span class="px-1.5 py-0.5 text-[9px] sm:text-[10px] font-bold bg-amber-400 text-brand-950 rounded uppercase tracking-wider">Santri</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Desktop Navigation Links (Role-Based Access Control) -->
                        <nav class="hidden md:flex items-center gap-0.5 xl:gap-1 shrink-0">
                    @auth
                        @php
                            $user = Auth::user();
                        @endphp

                        {{-- Akses Super Admin & Musyrif --}}
                        @if ($user->isSuperAdmin() || $user->isMusyrif())
                            <a href="{{ url('/') }}" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->is('/') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('absensi.scan') }}" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('absensi.scan*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Scan Presensi
                            </a>
                            <a href="{{ route('absensi.batch') }}" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('absensi.batch*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Presensi Cepat
                            </a>
                            <a href="{{ route('santri.index') }}" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('santri.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Data Santri
                            </a>
                            @if ($user->isSuperAdmin())
                                <a href="{{ route('jadwal.index') }}" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('jadwal.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                    Jadwal Sholat
                                </a>
                            @endif
                        @endif

                        {{-- Akses Perizinan (Super Admin, Musyrif, & Keamanan) --}}
                        @if ($user->isSuperAdmin() || $user->isMusyrif() || $user->isKeamanan())
                            <a href="{{ route('perizinan.index') }}" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('perizinan.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Perizinan
                            </a>
                        @endif

                        {{-- Akses Pos Keamanan (Super Admin & Keamanan) --}}
                        @if ($user->isSuperAdmin() || $user->isKeamanan())
                            <a href="{{ route('kamtib.index') }}" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('kamtib.*') || request()->routeIs('keamanan.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Pos Keamanan
                            </a>
                        @endif

                        {{-- Akses Rekapitulasi (Super Admin, Musyrif, & Keamanan) --}}
                        @if ($user->isSuperAdmin() || $user->isMusyrif() || $user->isKeamanan())
                            <div class="relative" x-data="{ openRekap: false }">
                                <button @click="openRekap = !openRekap" @click.away="openRekap = false" type="button" class="px-1.5 lg:px-2 xl:px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center space-x-1 {{ request()->routeIs('rekap.*') || request()->routeIs('keuangan.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                    <span>Rekapitulasi</span>
                                    <svg class="w-3.5 h-3.5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="openRekap" x-cloak class="absolute left-0 mt-2 w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl py-2 z-50 transition-all divide-y divide-slate-100 dark:divide-slate-800">
                                    <div class="py-1">
                                        <div class="px-4 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Presensi Santri</div>
                                        <a href="{{ route('rekap.absensi.harian') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-800 transition">
                                            <span>📅</span>
                                            <span>Rekap Absensi Harian</span>
                                        </a>
                                        <a href="{{ route('rekap.absensi', ['tipe' => 'bulanan']) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-800 transition">
                                            <span>📊</span>
                                            <span>Rekap Absensi Bulanan</span>
                                        </a>
                                    </div>
                                    <div class="py-1">
                                        <div class="px-4 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Perizinan Santri</div>
                                        <a href="{{ route('rekap.perizinan.harian') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-800 transition">
                                            <span>📋</span>
                                            <span>Rekap Izin Harian</span>
                                        </a>
                                        <a href="{{ route('rekap.perizinan', ['tipe' => 'bulanan']) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-800 transition">
                                            <span>📝</span>
                                            <span>Rekap Izin Bulanan</span>
                                        </a>
                                    </div>
                                    @if ($user->isSuperAdmin())
                                        <div class="py-1">
                                            <a href="{{ route('keuangan.tunggakan') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-slate-800 transition">
                                                <span>🚨</span>
                                                <span>Tunggakan Santri (Admin)</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Akses Khusus Bendahara Pondok --}}
                        @if ($user->isBendahara())
                            <a href="{{ route('bendahara.index') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('bendahara.index') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('bendahara.rekap') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('bendahara.rekap*') || request()->routeIs('bendahara.santri.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Rekap Pembayaran
                            </a>
                            <a href="{{ route('bendahara.tagihan.index') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('bendahara.tagihan.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Kelola Tagihan
                            </a>
                            <a href="{{ route('bendahara.verifikasi') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('bendahara.verifikasi*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Verifikasi Transfer
                            </a>
                        @endif

                        {{-- Akses Khusus Wali Santri (Portal, Pembayaran & Izin Ananda) --}}
                        @if ($user->isWali())
                            <a href="{{ route('wali.portal') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('wali.portal') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Portal Ananda
                            </a>
                            <a href="{{ route('wali.pembayaran') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('wali.pembayaran*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Pembayaran Tagihan
                            </a>
                            <a href="{{ route('perizinan.index') }}" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('perizinan.*') ? 'bg-brand-800 dark:bg-slate-800 text-white shadow-inner' : 'text-emerald-100 hover:bg-brand-800/60 dark:hover:bg-slate-800/60 hover:text-white' }}">
                                Pengajuan Izin
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- User Profile, Dark Mode Switcher & Auth Button -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">

                    <!-- Switch Dark/Light Mode Button (Alpine.js & localStorage) -->
                    <button
                        type="button"
                        @click="toggleDarkMode()"
                        class="p-1.5 sm:p-2 rounded-xl bg-brand-800/80 hover:bg-brand-700/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-amber-300 dark:text-amber-400 border border-brand-700/60 dark:border-slate-700 transition flex items-center justify-center shadow-sm shrink-0"
                        :title="darkMode ? 'Beralih ke Mode Terang (Light Mode)' : 'Beralih ke Mode Gelap (Dark Mode)'"
                        aria-label="Toggle Theme Mode"
                    >
                        <template x-if="darkMode">
                            <!-- Icon Matahari (Sun) untuk Light Mode -->
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                        <template x-if="!darkMode">
                            <!-- Icon Bulan (Moon) untuk Dark Mode -->
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </template>
                    </button>

                    @auth
                        @php
                            $user = Auth::user();
                            $roleLabel = match($user->role) {
                                'super_admin' => '👑 Super Admin',
                                'musyrif' => '📋 Musyrif',
                                'keamanan' => '🛡️ Keamanan',
                                'wali_santri' => '👨‍👩‍👧 Wali Santri',
                                'bendahara' => '💰 Bendahara',
                                default => ucfirst($user->role),
                            };
                            $roleShort = match($user->role) {
                                'super_admin' => '👑 Admin',
                                'musyrif' => '📋 Musyrif',
                                'keamanan' => '🛡️ Kamtib',
                                'wali_santri' => '👨‍👩‍👧 Wali',
                                'bendahara' => '💰 Bendahara',
                                default => ucfirst($user->role),
                            };
                            $roleBg = match($user->role) {
                                'super_admin' => 'bg-amber-400 text-brand-950 font-bold',
                                'musyrif' => 'bg-emerald-500 text-white',
                                'keamanan' => 'bg-orange-500 text-white',
                                'wali_santri' => 'bg-blue-500 text-white',
                                'bendahara' => 'bg-purple-600 text-white',
                                default => 'bg-slate-700 text-white',
                            };
                        @endphp
                        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                            <span class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg text-[10px] sm:text-xs font-bold {{ $roleBg }} shrink-0 whitespace-nowrap shadow-sm" title="{{ $user->name }} ({{ $roleLabel }})">
                                <span class="sm:hidden">{{ $roleShort }}</span>
                                <span class="hidden sm:inline">{{ $roleLabel }}</span>
                            </span>
                        </div>

                        <!-- Form Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline shrink-0">
                            @csrf
                            <button type="submit" class="p-1.5 sm:p-2 rounded-xl bg-brand-800 hover:bg-brand-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-emerald-200 hover:text-white text-xs font-bold border border-brand-700 dark:border-slate-700 transition active:scale-95 flex items-center justify-center shrink-0" title="Keluar / Logout">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-xs tracking-wide uppercase transition shadow-md">
                            <span>Masuk / Login</span>
                        </a>
                    @endauth
                </div>
            </div>
        </header>
    </div>
</div>

<!-- Main Content Area -->
<main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
        <!-- Toast Alerts -->
        @if ($errors->any())
            <div id="flash-errors" class="no-print mb-5 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 flex items-start justify-between shadow-sm animate-fade-in">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold">Terjadi Kesalahan Pengisian Formulir:</p>
                        <ul class="list-disc list-inside text-xs mt-1 space-y-0.5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('flash-errors').remove()" class="text-rose-600 dark:text-rose-400 hover:opacity-75 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('success'))
            <div id="flash-alert" class="no-print mb-5 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('flash-alert').remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div id="flash-alert" class="no-print mb-5 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('flash-alert').remove()" class="text-rose-600 dark:text-rose-400 hover:opacity-75 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (Mobile / Phone Only - Responsive & Role Consistent) -->
    <nav class="md:hidden no-print fixed bottom-0 left-0 right-0 z-40 bg-brand-950/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-brand-800/80 dark:border-slate-800 px-1 py-1.5 flex items-center justify-around text-[10px] font-semibold text-emerald-200 dark:text-slate-400 shadow-2xl">
        @auth
            @php $user = Auth::user(); @endphp

            {{-- 1. Super Admin Mobile Bottom Nav --}}
            @if ($user->isSuperAdmin())
                <a href="{{ url('/') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->is('/') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('absensi.scan') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('absensi.scan*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Scanner</span>
                </a>
                <a href="{{ route('santri.index') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('santri.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Santri</span>
                </a>
                <a href="{{ route('perizinan.index') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('perizinan.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Izin</span>
                </a>
                <a href="{{ route('rekap.absensi.harian') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('rekap.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Rekap</span>
                </a>

            {{-- 2. Musyrif Mobile Bottom Nav --}}
            @elseif ($user->isMusyrif())
                <a href="{{ url('/') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->is('/') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('absensi.scan') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('absensi.scan*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Scanner</span>
                </a>
                <a href="{{ route('absensi.batch') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('absensi.batch*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Presensi</span>
                </a>
                <a href="{{ route('perizinan.index') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('perizinan.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Izin</span>
                </a>
                <a href="{{ route('rekap.absensi.harian') }}" class="flex flex-col items-center py-1 px-2 rounded-xl transition {{ request()->routeIs('rekap.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Rekap</span>
                </a>

            {{-- 3. Keamanan / Kamtib Mobile Bottom Nav --}}
            @elseif ($user->isKeamanan())
                <a href="{{ route('kamtib.index') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('kamtib.*') || request()->routeIs('keamanan.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Pos Gerbang</span>
                </a>
                <a href="{{ route('perizinan.index') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('perizinan.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Perizinan</span>
                </a>
                <a href="{{ route('rekap.perizinan.harian') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('rekap.perizinan*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Rekap Izin</span>
                </a>
                <a href="{{ route('rekap.absensi.harian') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('rekap.absensi*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Rekap Absen</span>
                </a>

            {{-- 4. Bendahara Keuangan Mobile Bottom Nav --}}
            @elseif ($user->isBendahara())
                <a href="{{ route('bendahara.index') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('bendahara.index') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('bendahara.rekap') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('bendahara.rekap*') || request()->routeIs('bendahara.santri.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Rekap Bayar</span>
                </a>
                <a href="{{ route('bendahara.tagihan.index') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('bendahara.tagihan.*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Tagihan</span>
                </a>
                <a href="{{ route('bendahara.verifikasi') }}" class="flex flex-col items-center py-1 px-3 rounded-xl transition {{ request()->routeIs('bendahara.verifikasi*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Verifikasi</span>
                </a>

            {{-- 5. Wali Santri Mobile Bottom Nav --}}
            @elseif ($user->isWali())
                <a href="{{ route('wali.portal') }}" class="flex flex-col items-center py-1 px-4 rounded-xl transition {{ request()->routeIs('wali.portal*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Portal Ananda</span>
                </a>
                <a href="{{ route('wali.pembayaran') }}" class="flex flex-col items-center py-1 px-4 rounded-xl transition {{ request()->routeIs('wali.pembayaran*') || request()->routeIs('wali.kuitansi*') ? 'text-amber-400 font-bold bg-white/10' : 'hover:text-white' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Bayar &amp; SPP</span>
                </a>
            @endif
        @endauth
    </nav>

    <!-- Footer -->
    <footer class="no-print mt-auto py-6 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
            <div>
                <p class="font-semibold text-slate-700 dark:text-slate-300">Sistem Absensi Santri Digital</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">Pondok Pesantren Miftahul Huda Kroya &bull; Cilacap, Jawa Tengah</p>
            </div>
            <div class="text-[11px] text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} Hak Cipta Dilindungi Undang-Undang.
            </div>
        </div>
    </footer>

    {{-- ========================================================
         MODAL KONFIRMASI MODERN GLOBAL (SEMUA ROLE)
    ======================================================== --}}
    <div id="global-confirm-modal" class="fixed inset-0 z-[99999] hidden overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200 opacity-0">
        <div id="global-confirm-card" class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200/80 dark:border-slate-800 text-center relative overflow-hidden transform scale-95 transition-all duration-200">
            <!-- Glow Accent -->
            <div id="global-confirm-glow" class="absolute -top-12 -right-12 w-32 h-32 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Icon Wrapper -->
            <div id="global-confirm-icon-wrapper" class="w-14 h-14 rounded-2xl mx-auto mb-3.5 flex items-center justify-center shadow-md transition-transform duration-300">
                <div id="global-confirm-icon-svg" class="w-7 h-7 flex items-center justify-center"></div>
            </div>

            <!-- Title -->
            <h3 id="global-confirm-title" class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug">
                Konfirmasi
            </h3>

            <!-- Message -->
            <div id="global-confirm-message" class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed"></div>

            <!-- Subtext / Details Box -->
            <div id="global-confirm-subtext" class="hidden mt-3 p-3 rounded-2xl text-left text-xs leading-relaxed border border-slate-200/80 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50"></div>

            <!-- Action Buttons -->
            <div class="mt-5 flex items-center justify-center gap-2.5">
                <button type="button" id="global-confirm-btn-cancel" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition active:scale-95">
                    Batal
                </button>
                <button type="button" id="global-confirm-btn-ok" class="flex-1 px-4 py-2.5 rounded-xl text-white font-bold text-xs shadow-md active:scale-95 transition flex items-center justify-center gap-1.5">
                    <span id="global-confirm-btn-ok-text">Ya, Lanjutkan</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        let _appConfirmResolver = null;

        window.appConfirm = function(options = {}) {
            return new Promise((resolve) => {
                _appConfirmResolver = resolve;
                const modal = document.getElementById('global-confirm-modal');
                const card = document.getElementById('global-confirm-card');
                const glow = document.getElementById('global-confirm-glow');
                const iconWrapper = document.getElementById('global-confirm-icon-wrapper');
                const iconSvg = document.getElementById('global-confirm-icon-svg');
                const titleEl = document.getElementById('global-confirm-title');
                const msgEl = document.getElementById('global-confirm-message');
                const subtextEl = document.getElementById('global-confirm-subtext');
                const btnCancel = document.getElementById('global-confirm-btn-cancel');
                const btnOk = document.getElementById('global-confirm-btn-ok');
                const btnOkText = document.getElementById('global-confirm-btn-ok-text');

                const type = options.icon || (options.danger ? 'danger' : 'warning');
                titleEl.innerHTML = options.title || (type === 'danger' ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan');
                msgEl.innerHTML = options.message || options.text || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';

                if (options.subtext) {
                    subtextEl.innerHTML = options.subtext;
                    subtextEl.classList.remove('hidden');
                } else {
                    subtextEl.innerHTML = '';
                    subtextEl.classList.add('hidden');
                }

                btnCancel.classList.remove('hidden');
                btnCancel.innerText = options.cancelText || 'Batal';
                btnOkText.innerText = options.confirmText || (type === 'danger' ? 'Ya, Hapus' : 'Ya, Lanjutkan');

                // Apply theme classes
                iconWrapper.className = 'w-14 h-14 rounded-2xl mx-auto mb-3.5 flex items-center justify-center shadow-md transition-transform duration-300';
                btnOk.className = 'flex-1 px-4 py-2.5 rounded-xl text-white font-bold text-xs shadow-md active:scale-95 transition flex items-center justify-center gap-1.5';

                if (type === 'danger') {
                    glow.className = 'absolute -top-12 -right-12 w-32 h-32 rounded-full blur-2xl pointer-events-none bg-rose-500/15';
                    iconWrapper.classList.add('bg-rose-100', 'dark:bg-rose-950/70', 'border', 'border-rose-200', 'dark:border-rose-800', 'ring-4', 'ring-rose-500/10');
                    iconSvg.innerHTML = `<svg class="w-7 h-7 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>`;
                    btnOk.classList.add('bg-gradient-to-r', 'from-rose-600', 'to-red-600', 'hover:from-rose-700', 'hover:to-red-700', 'shadow-rose-600/30');
                } else if (type === 'success') {
                    glow.className = 'absolute -top-12 -right-12 w-32 h-32 rounded-full blur-2xl pointer-events-none bg-emerald-500/15';
                    iconWrapper.classList.add('bg-emerald-100', 'dark:bg-emerald-950/70', 'border', 'border-emerald-200', 'dark:border-emerald-800', 'ring-4', 'ring-emerald-500/10');
                    iconSvg.innerHTML = `<svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                    btnOk.classList.add('bg-gradient-to-r', 'from-emerald-600', 'to-teal-600', 'hover:from-emerald-700', 'hover:to-teal-700', 'shadow-emerald-600/30');
                } else if (type === 'info') {
                    glow.className = 'absolute -top-12 -right-12 w-32 h-32 rounded-full blur-2xl pointer-events-none bg-blue-500/15';
                    iconWrapper.classList.add('bg-blue-100', 'dark:bg-blue-950/70', 'border', 'border-blue-200', 'dark:border-blue-800', 'ring-4', 'ring-blue-500/10');
                    iconSvg.innerHTML = `<svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
                    btnOk.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700', 'shadow-blue-600/30');
                } else {
                    glow.className = 'absolute -top-12 -right-12 w-32 h-32 rounded-full blur-2xl pointer-events-none bg-amber-500/15';
                    iconWrapper.classList.add('bg-amber-100', 'dark:bg-amber-950/70', 'border', 'border-amber-200', 'dark:border-amber-800', 'ring-4', 'ring-amber-500/10');
                    iconSvg.innerHTML = `<svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
                    btnOk.classList.add('bg-gradient-to-r', 'from-amber-600', 'to-amber-700', 'hover:from-amber-700', 'hover:to-amber-800', 'shadow-amber-600/30');
                }

                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    modal.classList.remove('opacity-0');
                    card.classList.remove('scale-95');
                    card.classList.add('scale-100');
                });
            });
        };

        window.appAlert = function(options = {}) {
            return new Promise((resolve) => {
                window.appConfirm({
                    ...options,
                    confirmText: options.btnText || 'Mengerti',
                }).then(() => resolve());
                const btnCancel = document.getElementById('global-confirm-btn-cancel');
                if (btnCancel) btnCancel.classList.add('hidden');
            });
        };

        function _closeGlobalConfirm(result) {
            const modal = document.getElementById('global-confirm-modal');
            const card = document.getElementById('global-confirm-card');
            if (!modal) return;
            modal.classList.add('opacity-0');
            if (card) {
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                if (_appConfirmResolver) {
                    _appConfirmResolver(result);
                    _appConfirmResolver = null;
                }
            }, 180);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btnCancel = document.getElementById('global-confirm-btn-cancel');
            const btnOk = document.getElementById('global-confirm-btn-ok');
            const modal = document.getElementById('global-confirm-modal');

            if (btnCancel) btnCancel.addEventListener('click', () => _closeGlobalConfirm(false));
            if (btnOk) btnOk.addEventListener('click', () => _closeGlobalConfirm(true));
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) _closeGlobalConfirm(false);
                });
            }

            document.addEventListener('keydown', (e) => {
                if (!modal || modal.classList.contains('hidden')) return;
                if (e.key === 'Escape') _closeGlobalConfirm(false);
                if (e.key === 'Enter') _closeGlobalConfirm(true);
            });

            // Otomatis tangkap dan gantikan form onsubmit="return confirm(...)"
            document.querySelectorAll('form').forEach(form => {
                const onsubmit = form.getAttribute('onsubmit');
                if (onsubmit && onsubmit.includes('confirm(')) {
                    const match = onsubmit.match(/confirm\((?:'|")(.*?)(?:'|")\)/s);
                    if (match && match[1]) {
                        form.removeAttribute('onsubmit');
                        form.setAttribute('data-confirm', match[1].replace(/\\'/g, "'").replace(/\\"/g, '"'));
                    }
                }
            });

            // Otomatis tangkap button onclick="return confirm(...)"
            document.querySelectorAll('button, a').forEach(btn => {
                const onclick = btn.getAttribute('onclick');
                if (onclick && onclick.includes('confirm(')) {
                    const match = onclick.match(/confirm\((?:'|")(.*?)(?:'|")\)/s);
                    if (match && match[1]) {
                        btn.removeAttribute('onclick');
                        btn.setAttribute('data-confirm', match[1].replace(/\\'/g, "'").replace(/\\"/g, '"'));
                    }
                }
            });

            // Delegasi global form submit
            document.addEventListener('submit', function(e) {
                const form = e.target;
                const confirmMsg = form.getAttribute('data-confirm');
                if (confirmMsg && !form.dataset.confirmed) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') ||
                                     form.action.includes('destroy') ||
                                     form.action.includes('delete') ||
                                     confirmMsg.toLowerCase().includes('hapus');

                    window.appConfirm({
                        title: isDelete ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan',
                        message: confirmMsg,
                        icon: isDelete ? 'danger' : 'warning',
                        confirmText: isDelete ? 'Ya, Hapus' : 'Ya, Lanjutkan',
                        cancelText: 'Batal'
                    }).then(confirmed => {
                        if (confirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            }, true);

            // Delegasi global button click di dalam form
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('button[data-confirm]');
                if (btn) {
                    const form = btn.closest('form');
                    const confirmMsg = btn.getAttribute('data-confirm');
                    if (form && confirmMsg && !form.dataset.confirmed) {
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') ||
                                         confirmMsg.toLowerCase().includes('hapus');

                        window.appConfirm({
                            title: isDelete ? 'Konfirmasi Hapus Data' : 'Konfirmasi Tindakan',
                            message: confirmMsg,
                            icon: isDelete ? 'danger' : 'success',
                            confirmText: isDelete ? 'Ya, Hapus' : 'Ya, Konfirmasi',
                            cancelText: 'Batal'
                        }).then(confirmed => {
                            if (confirmed) {
                                form.dataset.confirmed = 'true';
                                if (btn.name) {
                                    const hidden = document.createElement('input');
                                    hidden.type = 'hidden';
                                    hidden.name = btn.name;
                                    hidden.value = btn.value;
                                    form.appendChild(hidden);
                                }
                                form.submit();
                            }
                        });
                    }
                }
            }, true);
        });
    </script>

    @stack('scripts')
</body>
</html>
