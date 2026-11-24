<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Akun Wali Santri - E-Absensi Santri</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-pesantren.png') }}">

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

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
    class="min-h-full flex items-center justify-center p-4 text-slate-800 dark:text-slate-100 antialiased bg-gradient-to-br from-slate-100 via-blue-50 to-slate-200 dark:from-slate-950 dark:via-blue-950 dark:to-slate-900 transition-colors duration-200"
>
    <!-- Dark Mode Switcher -->
    <div class="fixed top-4 right-4 z-50">
        <button
            type="button"
            @click="toggleDarkMode()"
            class="p-2.5 rounded-2xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-slate-200 dark:border-slate-700 text-amber-500 dark:text-amber-400 shadow-md transition hover:scale-105 active:scale-95"
            :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
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

    <div class="max-w-lg w-full space-y-5 my-8">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="inline-flex w-20 h-20 rounded-full bg-white dark:bg-slate-800 items-center justify-center shadow-xl shadow-blue-950/20 mb-3 ring-4 ring-blue-500/30 p-1 overflow-hidden">
                <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo Pondok Pesantren Miftahul Huda Kroya" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Pendaftaran Akun Wali Santri</h1>
            <p class="text-xs font-bold text-blue-700 dark:text-blue-400 mt-0.5 uppercase tracking-wide">Pondok Pesantren Miftahul Huda Kroya</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Daftarkan akun untuk memantau presensi dan mengajukan izin ananda secara online</p>
        </div>

        <!-- Card Pendaftaran -->
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-2xl">
            <!-- Alert Error -->
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                    <strong class="font-bold block">Mohon periksa kembali formulir Anda:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.wali.process') }}" class="space-y-4">
                @csrf

                <!-- Nama Lengkap Wali -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Nama Lengkap Wali Santri *
                    </label>
                    <input
                        type="text"
                        name="name"
                        required
                        value="{{ old('name') }}"
                        placeholder="Contoh: H. Ahmad Subarkah"
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                </div>

                <!-- Nomor WhatsApp / HP Aktif -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Nomor WhatsApp / HP Aktif *
                    </label>
                    <input
                        type="tel"
                        name="no_hp"
                        required
                        value="{{ old('no_hp') }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                    <p class="text-[10px] text-slate-400 mt-1">Nomor ini akan otomatis dihubungkan dengan santri ananda untuk pengiriman notifikasi izin & presensi.</p>
                </div>

                <!-- Hubungan dengan Santri -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Hubungan dengan Santri *
                    </label>
                    <select
                        name="hubungan"
                        required
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                        <option value="Ayah Kandung" {{ old('hubungan') === 'Ayah Kandung' ? 'selected' : '' }}>Ayah Kandung</option>
                        <option value="Ibu Kandung" {{ old('hubungan') === 'Ibu Kandung' ? 'selected' : '' }}>Ibu Kandung</option>
                        <option value="Wali / Saudara" {{ old('hubungan') === 'Wali / Saudara' ? 'selected' : '' }}>Wali / Saudara</option>
                    </select>
                </div>

                <!-- Input Nomor Induk Santri (NIS Ananda) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Nomor Induk Santri (NIS Ananda) *
                    </label>
                    <input
                        type="text"
                        name="nis_santri"
                        required
                        value="{{ old('nis_santri') }}"
                        placeholder="Contoh: SAN-2026-0001"
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono transition uppercase"
                        style="text-transform: uppercase;"
                    >
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                        <span>🔒</span>
                        <span>Masukkan NIS resmi yang tertera pada Kartu Santri ananda atau lembar pendaftaran.</span>
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Alamat Email *
                    </label>
                    <input
                        type="email"
                        name="email"
                        required
                        value="{{ old('email') }}"
                        placeholder="wali@example.com"
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                </div>

                <!-- Kata Sandi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Kata Sandi *
                        </label>
                        <input
                            type="password"
                            name="password"
                            required
                            placeholder="Min. 6 karakter"
                            class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                            Konfirmasi Sandi *
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            placeholder="Ulangi sandi"
                            class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                        >
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-98 text-white font-bold text-sm shadow-lg shadow-blue-700/20 transition flex items-center justify-center space-x-2"
                    >
                        <span>Daftarkan Akun Wali Santri</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Sudah memiliki akun wali santri?
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Masuk di sini &rarr;
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
