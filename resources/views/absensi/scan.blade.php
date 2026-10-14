<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemindai Presensi QR Code Santri | E-Absensi Santri</title>
    <meta name="description" content="Sistem Pemindai Presensi Cepat Santri berbasis QR Code Pesantren dengan validasi perizinan otomatis dan performa tinggi.">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS (Vite with CDN fallback) -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'sans-serif'],
                            mono: ['JetBrains Mono', 'monospace'],
                        },
                        colors: {
                            emerald: {
                                950: '#022c22',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <!-- HTML5-QRCode Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom Laser Scan Animation */
        @keyframes scanLaser {
            0% { top: 6%; opacity: 0.2; }
            50% { opacity: 0.9; }
            100% { top: 92%; opacity: 0.2; }
        }
        .scan-laser-line {
            animation: scanLaser 2.2s ease-in-out infinite alternate;
        }
        /* Glow Pulse Effect */
        @keyframes subtleGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(16, 185, 129, 0.2); }
            50% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.45); }
        }
        .glow-active {
            animation: subtleGlow 3s infinite;
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(51, 65, 85, 0.8);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.6);
        }
        /* html5-qrcode video styling */
        #qr-reader {
            border: none !important;
            background: transparent !important;
        }
        #qr-reader video {
            border-radius: 1rem;
            object-fit: cover !important;
            width: 100% !important;
            height: 100% !important;
        }
        #qr-reader__scan_region {
            background: transparent !important;
        }
        #qr-reader__dashboard_section_csr button {
            background-color: #059669;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex flex-col antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 text-white font-bold text-lg">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-emerald-400 bg-clip-text text-transparent">
                            E-Absensi Santri
                        </span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full">
                            Live Scanner v2.0
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 hidden sm:block">Sistem Presensi Digital & Kedisiplinan Pesantren</p>
                </div>
            </div>

            <!-- Jam Digital Realtime & Status Sesi -->
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <div id="live-digital-clock" class="text-sm font-mono font-bold text-emerald-400 tracking-wider">00:00:00</div>
                    <div id="live-digital-date" class="text-xs text-slate-400">Loading...</div>
                </div>

                <!-- Session Switcher / Dropdown Button -->
                @if ($daftarSesi->isNotEmpty())
                    <div class="relative">
                        <select id="session-select" onchange="switchSession(this.value)" class="bg-slate-800/90 text-xs sm:text-sm text-slate-200 font-medium py-2 px-3 pr-8 rounded-xl border border-slate-700 hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition appearance-none cursor-pointer">
                            @foreach ($daftarSesi as $itemSesi)
                                <option value="{{ $itemSesi->id }}" {{ ($sesiAktif && $sesiAktif->id === $itemSesi->id) ? 'selected' : '' }}>
                                    ● {{ $itemSesi->nama_sesi }} ({{ \Carbon\Carbon::parse($itemSesi->tanggal)->format('d M') }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-3 text-xs text-slate-400 pointer-events-none"></i>
                    </div>
                @endif

                <!-- Audio Toggle Button -->
                <button id="audio-toggle-btn" onclick="toggleAudio()" title="Aktifkan/Nonaktifkan Suara Beep" class="h-9 w-9 rounded-xl bg-slate-800 border border-slate-700 hover:border-emerald-500/50 flex items-center justify-center text-slate-300 hover:text-emerald-400 transition">
                    <i id="audio-icon" class="fa-solid fa-volume-high text-sm"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-6">

        <!-- Active Session Banner -->
        @if ($sesiAktif)
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-950/40 via-slate-900/80 to-slate-900 border border-emerald-500/30 p-5 shadow-2xl backdrop-blur-sm glow-active">
                <div class="absolute -right-8 -top-8 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl shrink-0">
                            <i class="fa-solid fa-mosque"></i>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                                    {{ $sesiAktif->nama_sesi }}
                                </h1>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                    Sesi Dibuka
                                </span>
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-mono">
                                    Kode: {{ $sesiAktif->kode_sesi }}
                                </span>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-300">
                                <span><i class="fa-regular fa-calendar mr-1.5 text-emerald-400"></i>{{ \Carbon\Carbon::parse($sesiAktif->tanggal)->translatedFormat('l, d F Y') }}</span>
                                <span><i class="fa-regular fa-clock mr-1.5 text-emerald-400"></i>Waktu: {{ substr($sesiAktif->jam_mulai, 0, 5) }} - {{ substr($sesiAktif->jam_selesai, 0, 5) }} WIB</span>
                                @if ($sesiAktif->batas_terlambat)
                                    <span class="text-amber-300 font-medium">
                                        <i class="fa-solid fa-triangle-exclamation mr-1.5 text-amber-400"></i>Batas Tepat Waktu: {{ substr($sesiAktif->batas_terlambat, 0, 5) }} WIB
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Session Stat Pills -->
                    <div class="grid grid-cols-4 gap-2 text-center shrink-0">
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl px-3 py-2">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Hadir</div>
                            <div id="stat-hadir" class="text-lg font-bold text-emerald-400 font-mono">{{ $stats['hadir'] }}</div>
                        </div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl px-3 py-2">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Terlambat</div>
                            <div id="stat-terlambat" class="text-lg font-bold text-amber-400 font-mono">{{ $stats['terlambat'] }}</div>
                        </div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl px-3 py-2">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Izin</div>
                            <div id="stat-izin" class="text-lg font-bold text-sky-400 font-mono">{{ $stats['izin'] }}</div>
                        </div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl px-3 py-2">
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Sakit</div>
                            <div id="stat-sakit" class="text-lg font-bold text-purple-400 font-mono">{{ $stats['sakit'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Warning jika tidak ada sesi aktif -->
            <div class="rounded-2xl bg-amber-500/10 border border-amber-500/30 p-6 text-center">
                <i class="fa-solid fa-triangle-exclamation text-3xl text-amber-400 mb-2"></i>
                <h2 class="text-lg font-bold text-amber-200">Tidak Ada Sesi Absensi yang Sedang Dibuka</h2>
                <p class="text-sm text-amber-300/80 mt-1 max-w-md mx-auto">
                    Saat ini belum ada sesi presensi yang berstatus 'dibuka'. Silakan buka sesi absensi melalui panel administrasi atau jalankan database seeder.
                </p>
            </div>
        @endif

        <!-- Main Scanner Layout (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Camera Scanner & Manual Input (7 Cols) -->
            <div class="lg:col-span-7 flex flex-col gap-6">

                <!-- Scanner Viewfinder Card -->
                <section class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-xl relative backdrop-blur-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h2 class="font-bold text-base text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-camera text-emerald-400"></i> Kamera Pemindai QR
                            </h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Switch Camera Button -->
                            <button id="switch-camera-btn" onclick="toggleCameraFacing()" class="px-2.5 py-1.5 text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-700 flex items-center gap-1.5 transition">
                                <i class="fa-solid fa-arrows-rotate"></i>
                                <span class="hidden sm:inline">Balik Kamera</span>
                            </button>
                            <!-- Start / Stop Button -->
                            <button id="toggle-scanner-btn" onclick="toggleScanner()" class="px-3 py-1.5 text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg shadow-sm flex items-center gap-1.5 transition">
                                <i id="toggle-scanner-icon" class="fa-solid fa-stop"></i>
                                <span id="toggle-scanner-text">Jeda Kamera</span>
                            </button>
                        </div>
                    </div>

                    <!-- Viewfinder Frame -->
                    <div class="relative w-full aspect-[4/3] sm:aspect-[16/10] bg-black/90 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center">

                        <!-- HTML5 QR Video Area -->
                        <div id="qr-reader" class="w-full h-full"></div>

                        <!-- Reticle / Target Overlay -->
                        <div id="scanner-reticle" class="absolute inset-0 pointer-events-none flex items-center justify-center">
                            <!-- Center target square -->
                            <div class="w-64 h-64 sm:w-72 sm:h-72 border-2 border-emerald-500/50 rounded-2xl relative">
                                <!-- Corner Highlights -->
                                <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-emerald-400 rounded-tl-lg"></div>
                                <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-emerald-400 rounded-tr-lg"></div>
                                <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-emerald-400 rounded-bl-lg"></div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-emerald-400 rounded-br-lg"></div>

                                <!-- Animated Laser Line -->
                                <div id="scan-laser" class="scan-laser-line absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_12px_#34d399]"></div>
                            </div>
                        </div>

                        <!-- Processing Indicator Overlay -->
                        <div id="scanner-loading" class="hidden absolute inset-0 bg-slate-950/80 backdrop-blur-sm flex flex-col items-center justify-center gap-3 z-30">
                            <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
                            <span class="text-sm font-semibold text-emerald-400 tracking-wide">Memproses Presensi Santri...</span>
                        </div>
                    </div>

                    <!-- Scan Mode Guidance -->
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-emerald-400"></i>
                            Arahkan QR Code Kartu Santri ke kotak target
                        </span>
                        <span id="scanner-status-text" class="text-emerald-400 font-mono">Status: Aktif</span>
                    </div>
                </section>

                <!-- Manual Input / Barcode Scanner Gun Fallback Card -->
                <section class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-xl backdrop-blur-md">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-teal-400"></i>
                            Input Manual / Gun Barcode Scanner USB
                        </h2>
                        <span class="text-[11px] text-slate-400">Tekan Enter untuk scan</span>
                    </div>

                    <form id="form-manual-scan" onsubmit="handleManualSubmit(event)" class="flex gap-2">
                        <div class="relative flex-1">
                            <input
                                type="text"
                                id="nis-input"
                                name="nis"
                                placeholder="Ketik atau scan NIS santri di sini (contoh: STR2026001)..."
                                autocomplete="off"
                                class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 font-mono outline-none transition"
                            >
                            <button
                                type="button"
                                onclick="clearNisInput()"
                                class="absolute right-3 top-3 text-slate-400 hover:text-slate-200 text-sm hidden"
                                id="clear-nis-btn"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <button
                            type="submit"
                            id="submit-manual-btn"
                            class="px-5 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 active:scale-95 text-white font-semibold text-sm rounded-xl shadow-lg shadow-emerald-500/20 flex items-center gap-2 transition shrink-0"
                        >
                            <i class="fa-solid fa-check"></i>
                            <span>Absen</span>
                        </button>
                    </form>
                    <p class="text-[11px] text-slate-400 mt-2">
                        * Input ini kompatibel langsung dengan Barcode/QR Scanner eksternal (HID Keyboard Emulation) tanpa perlu mengklik tombol.
                    </p>
                </section>
            </div>

            <!-- RIGHT COLUMN: Scan Result Card & Recent Scans Feed (5 Cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6">

                <!-- Real-time Scan Result Card -->
                <section id="result-container" class="rounded-2xl bg-slate-900/80 border border-slate-800 p-6 shadow-xl backdrop-blur-md relative overflow-hidden transition-all duration-300">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                        <h2 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-address-card text-emerald-400"></i> Hasil Pemindaian Terakhir
                        </h2>
                        <span id="scan-perf-badge" class="hidden text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                            ⚡ 0ms
                        </span>
                    </div>

                    <!-- Placeholder Saat Belum Ada Scan -->
                    <div id="result-placeholder" class="py-12 flex flex-col items-center justify-center text-center text-slate-500">
                        <div class="w-16 h-16 rounded-2xl bg-slate-800/60 border border-slate-700/50 flex items-center justify-center text-2xl mb-3 text-slate-400">
                            <i class="fa-solid fa-id-badge"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-300">Menunggu Pemindaian QR</h3>
                        <p class="text-xs text-slate-400 max-w-xs mt-1">
                            Arahkan kamera ke QR santri atau masukkan NIS untuk memproses kehadiran.
                        </p>
                    </div>

                    <!-- Active Result Details (Initially Hidden) -->
                    <div id="result-details" class="hidden flex flex-col gap-4">
                        <!-- Header Santri Card -->
                        <div class="flex items-start gap-4">
                            <!-- Avatar / Foto Placeholder -->
                            <div id="result-avatar" class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                                S
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 id="result-nama" class="text-lg font-bold text-white truncate">Nama Santri</h3>
                                    <span id="result-jk" class="text-xs font-semibold px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">L</span>
                                </div>
                                <div class="text-xs font-mono text-emerald-400 mt-0.5" id="result-nis">NIS: -</div>
                                <div class="text-xs text-slate-300 mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                                    <span><i class="fa-solid fa-graduation-cap text-slate-400 mr-1"></i><span id="result-kelas">-</span></span>
                                    <span><i class="fa-solid fa-bed text-slate-400 mr-1"></i><span id="result-asrama">-</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge & Description Banner -->
                        <div id="result-status-banner" class="p-4 rounded-xl border flex items-center gap-3 bg-emerald-500/10 border-emerald-500/30 text-emerald-300">
                            <div id="result-status-icon" class="text-2xl">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs uppercase font-extrabold tracking-wider" id="result-status-title">PRESENSI BERHASIL</div>
                                <div class="text-xs mt-0.5 opacity-90" id="result-status-desc">Hadir tepat waktu</div>
                            </div>
                        </div>

                        <!-- Additional Metadata Grid -->
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Waktu Presensi</span>
                                <span id="result-waktu" class="font-mono text-slate-200 font-bold">-</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Metode Scan</span>
                                <span id="result-metode" class="text-slate-200 font-medium">QR Code</span>
                            </div>
                        </div>

                        <!-- Auto Perizinan Notice (jika ada izin aktif) -->
                        <div id="result-izin-box" class="hidden p-3 rounded-xl bg-sky-950/40 border border-sky-500/30 text-sky-200 text-xs">
                            <div class="font-bold flex items-center gap-1.5 text-sky-300">
                                <i class="fa-solid fa-file-signature"></i> Perizinan Aktif Terdeteksi
                            </div>
                            <p id="result-izin-desc" class="mt-1 text-[11px] text-sky-200/90"></p>
                        </div>
                    </div>
                </section>

                <!-- Live Feed: 10 Recent Scans in this Session -->
                <section class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-xl backdrop-blur-md flex flex-col flex-1">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
                        <h2 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-emerald-400"></i> Riwayat Presensi Sesi Ini
                        </h2>
                        <span id="feed-count-badge" class="text-[11px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-mono">
                            {{ $recentAbsensis->count() }} santri
                        </span>
                    </div>

                    <div id="recent-feed-list" class="space-y-2 overflow-y-auto max-h-[380px] pr-1">
                        @forelse ($recentAbsensis as $item)
                            <article class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 flex items-center justify-between gap-3 transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-slate-300 shrink-0">
                                        {{ substr($item->santri?->nama ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold text-slate-200 truncate">
                                            {{ $item->santri?->nama ?? 'Santri' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $item->santri?->nis }} • {{ $item->santri?->kelas?->nama_kelas ?? 'Kelas' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    @php
                                        $badgeClass = match ($item->status) {
                                            'hadir' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                            'terlambat' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                            'izin' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                            'sakit' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                            default => 'bg-slate-800 text-slate-400 border-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded border {{ $badgeClass }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                        {{ $item->waktu_absen ? $item->waktu_absen->format('H:i:s') : '-' }}
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div id="feed-empty-state" class="py-8 text-center text-xs text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-600 mb-2"></i>
                                <p>Belum ada santri yang melakukan presensi pada sesi ini.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-900/40 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            Sistem Informasi Manajemen Presensi Santri Terintegrasi &copy; {{ date('Y') }} Pesantren Modern.
        </div>
    </footer>

    <!-- Audio Beep Generator via Web Audio API -->
    <script>
        // Web Audio Context for synthesized feedback sounds (no external audio files required)
        let audioCtx = null;
        let isAudioEnabled = true;

        function initAudio() {
            if (!audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (AudioContext) {
                    audioCtx = new AudioContext();
                }
            }
        }

        function playBeep(type = 'success') {
            if (!isAudioEnabled) return;
            try {
                initAudio();
                if (!audioCtx) return;
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }

                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);

                const now = audioCtx.currentTime;

                if (type === 'success') {
                    // Two quick pleasant high tones
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, now); // A5
                    osc.frequency.setValueAtTime(1320, now + 0.08); // E6
                    gain.gain.setValueAtTime(0.3, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
                    osc.start(now);
                    osc.stop(now + 0.25);
                } else if (type === 'warning') {
                    // Medium warning tone
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(587.33, now); // D5
                    osc.frequency.setValueAtTime(440, now + 0.1); // A4
                    gain.gain.setValueAtTime(0.3, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                    osc.start(now);
                    osc.stop(now + 0.3);
                } else {
                    // Low buzz error tone
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(220, now); // A3
                    osc.frequency.setValueAtTime(164.81, now + 0.12); // E3
                    gain.gain.setValueAtTime(0.4, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                    osc.start(now);
                    osc.stop(now + 0.35);
                }
            } catch (e) {
                console.warn('Audio feedback error:', e);
            }
        }

        function toggleAudio() {
            isAudioEnabled = !isAudioEnabled;
            const icon = document.getElementById('audio-icon');
            if (isAudioEnabled) {
                icon.className = 'fa-solid fa-volume-high text-sm';
                playBeep('success');
            } else {
                icon.className = 'fa-solid fa-volume-xmark text-sm text-slate-500';
            }
        }
    </script>

    <!-- Digital Clock & Session Handler -->
    <script>
        function updateDigitalClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });

            const clockEl = document.getElementById('live-digital-clock');
            const dateEl = document.getElementById('live-digital-date');
            if (clockEl) clockEl.textContent = timeStr.replace(/\./g, ':');
            if (dateEl) dateEl.textContent = dateStr;
        }
        setInterval(updateDigitalClock, 1000);
        updateDigitalClock();

        function switchSession(sesiId) {
            if (!sesiId) return;
            const url = new URL(window.location.href);
            url.searchParams.set('sesi_id', sesiId);
            window.location.href = url.toString();
        }

        function clearNisInput() {
            const input = document.getElementById('nis-input');
            input.value = '';
            input.focus();
            document.getElementById('clear-nis-btn').classList.add('hidden');
        }

        document.getElementById('nis-input')?.addEventListener('input', function() {
            const clearBtn = document.getElementById('clear-nis-btn');
            if (this.value.trim().length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        });
    </script>

    <!-- QR Scanner Logic & API Communication -->
    <script>
        const ACTIVE_SESI_ID = {{ $sesiAktif ? $sesiAktif->id : 'null' }};
        let html5QrCode = null;
        let isScannerRunning = false;
        let currentFacingMode = "environment"; // default kamera belakang
        let isProcessingScan = false;
        let lastScannedCode = null;
        let lastScannedTime = 0;
        const SCAN_DEBOUNCE_MS = 2500; // Mencegah scan ganda dalam 2.5 detik

        // Inisialisasi scanner saat halaman siap
        document.addEventListener('DOMContentLoaded', function() {
            if (ACTIVE_SESI_ID) {
                startScanner();
            }

            // Auto focus pada input manual untuk kenyamanan scanner barcode USB
            const nisInput = document.getElementById('nis-input');
            if (nisInput) {
                setTimeout(() => nisInput.focus(), 600);
            }
        });

        async function startScanner() {
            const qrRegion = document.getElementById('qr-reader');
            if (!qrRegion) return;

            try {
                html5QrCode = new Html5Qrcode("qr-reader");
                const config = {
                    fps: 15,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                };

                await html5QrCode.start(
                    { facingMode: currentFacingMode },
                    config,
                    onQrCodeScanned,
                    onQrScanError
                );

                isScannerRunning = true;
                updateScannerUiState(true);
            } catch (err) {
                console.warn("Kamera tidak dapat diakses atau diblokir:", err);
                document.getElementById('scanner-status-text').textContent = "Kamera tidak aktif / butuh izin";
                updateScannerUiState(false);
            }
        }

        async function stopScanner() {
            if (html5QrCode && isScannerRunning) {
                try {
                    await html5QrCode.stop();
                    isScannerRunning = false;
                    updateScannerUiState(false);
                } catch (e) {
                    console.error("Gagal menghentikan kamera:", e);
                }
            }
        }

        function toggleScanner() {
            if (isScannerRunning) {
                stopScanner();
            } else {
                startScanner();
            }
        }

        async function toggleCameraFacing() {
            currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
            if (isScannerRunning) {
                await stopScanner();
                await startScanner();
            }
        }

        function updateScannerUiState(active) {
            const btnText = document.getElementById('toggle-scanner-text');
            const btnIcon = document.getElementById('toggle-scanner-icon');
            const laser = document.getElementById('scan-laser');
            const statusText = document.getElementById('scanner-status-text');

            if (active) {
                btnText.textContent = 'Jeda Kamera';
                btnIcon.className = 'fa-solid fa-stop';
                laser.classList.remove('hidden');
                statusText.textContent = 'Status: Aktif';
                statusText.className = 'text-emerald-400 font-mono';
            } else {
                btnText.textContent = 'Mulai Kamera';
                btnIcon.className = 'fa-solid fa-play';
                laser.classList.add('hidden');
                statusText.textContent = 'Status: Dijeda';
                statusText.className = 'text-amber-400 font-mono';
            }
        }

        function onQrCodeScanned(decodedText, decodedResult) {
            const now = Date.now();
            // Mencegah debounce scan berulang untuk kode yang sama
            if (decodedText === lastScannedCode && (now - lastScannedTime < SCAN_DEBOUNCE_MS)) {
                return;
            }

            if (isProcessingScan) return;

            lastScannedCode = decodedText;
            lastScannedTime = now;

            console.log("QR Code Terdeteksi:", decodedText);
            processAttendanceScan(decodedText.trim());
        }

        function onQrScanError(errorMessage) {
            // Abaikan error umum scanning frame
        }

        function handleManualSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('nis-input');
            const nis = input.value.trim();

            if (!nis) {
                input.focus();
                return;
            }

            processAttendanceScan(nis);
        }

        // Fungsi Inti Memanggil API Presensi
        async function processAttendanceScan(nisValue) {
            if (!ACTIVE_SESI_ID) {
                alert("Tidak ada sesi absensi yang aktif. Silakan pilih atau buka sesi terlebih dahulu.");
                return;
            }

            isProcessingScan = true;
            document.getElementById('scanner-loading').classList.remove('hidden');

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const response = await fetch('/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                    },
                    body: JSON.stringify({
                        nis: nisValue,
                        sesi_id: ACTIVE_SESI_ID,
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    renderSuccessResult(data);
                    playBeep(data.data?.status === 'terlambat' ? 'warning' : 'success');
                    updateStatsCounter(data.data?.status);
                    prependRecentFeed(data.data);
                } else {
                    renderErrorResult(data);
                    playBeep('error');
                }
            } catch (err) {
                console.error("Gagal melakukan request absensi:", err);
                renderNetworkErrorResult(err);
                playBeep('error');
            } finally {
                isProcessingScan = false;
                document.getElementById('scanner-loading').classList.add('hidden');

                // Reset dan fokus kembali ke input manual
                const nisInput = document.getElementById('nis-input');
                if (nisInput) {
                    nisInput.value = '';
                    document.getElementById('clear-nis-btn')?.classList.add('hidden');
                    nisInput.focus();
                }
            }
        }

        // Tampilan Hasil Sukses
        function renderSuccessResult(payload) {
            const data = payload.data;
            const meta = payload.meta || {};

            document.getElementById('result-placeholder').classList.add('hidden');
            const detailsEl = document.getElementById('result-details');
            detailsEl.classList.remove('hidden');

            // Set Avatar Inisial
            const initial = (data.santri?.nama || 'S').charAt(0).toUpperCase();
            document.getElementById('result-avatar').textContent = initial;

            // Set Data Santri
            document.getElementById('result-nama').textContent = data.santri?.nama || 'Nama Santri';
            document.getElementById('result-jk').textContent = data.santri?.jenis_kelamin || 'L';
            document.getElementById('result-nis').textContent = `NIS: ${data.santri?.nis || '-'}`;
            document.getElementById('result-kelas').textContent = data.santri?.kelas || '-';
            document.getElementById('result-asrama').textContent = data.santri?.asrama || '-';
            document.getElementById('result-waktu').textContent = data.waktu_absen || '-';
            document.getElementById('result-metode').textContent = data.metode === 'qr_code' ? 'QR Code Scanner' : 'Input NIS Manual';

            // Latency badge
            const perfBadge = document.getElementById('scan-perf-badge');
            if (meta.waktu_eksekusi_ms) {
                perfBadge.textContent = `⚡ ${meta.waktu_eksekusi_ms}ms`;
                perfBadge.classList.remove('hidden');
            }

            // Banner Status Styling
            const banner = document.getElementById('result-status-banner');
            const bannerIcon = document.getElementById('result-status-icon');
            const bannerTitle = document.getElementById('result-status-title');
            const bannerDesc = document.getElementById('result-status-desc');

            const status = data.status;
            if (status === 'hadir') {
                banner.className = 'p-4 rounded-xl border flex items-center gap-3 bg-emerald-500/10 border-emerald-500/30 text-emerald-300';
                bannerIcon.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400"></i>';
                bannerTitle.textContent = 'PRESENSI BERHASIL (HADIR)';
                bannerDesc.textContent = data.keterangan || 'Santri hadir tepat waktu.';
            } else if (status === 'terlambat') {
                banner.className = 'p-4 rounded-xl border flex items-center gap-3 bg-amber-500/10 border-amber-500/30 text-amber-300';
                bannerIcon.innerHTML = '<i class="fa-solid fa-clock text-amber-400"></i>';
                bannerTitle.textContent = 'PRESENSI TERLAMBAT';
                bannerDesc.textContent = data.keterangan || 'Presensi tercatat melewati batas waktu.';
            } else if (status === 'izin' || status === 'sakit') {
                banner.className = 'p-4 rounded-xl border flex items-center gap-3 bg-sky-500/10 border-sky-500/30 text-sky-300';
                bannerIcon.innerHTML = '<i class="fa-solid fa-file-medical text-sky-400"></i>';
                bannerTitle.textContent = `PRESENSI OTOMATIS: ${status.toUpperCase()}`;
                bannerDesc.textContent = data.keterangan || 'Santri memiliki surat izin resmi.';
            }

            // Info Perizinan Aktif
            const izinBox = document.getElementById('result-izin-box');
            if (meta.status_otomatis_izin && meta.perizinan_aktif) {
                izinBox.classList.remove('hidden');
                document.getElementById('result-izin-desc').textContent =
                    `Surat ${meta.perizinan_aktif.nomor_surat} (${meta.perizinan_aktif.jenis_izin}): ${meta.perizinan_aktif.keperluan}`;
            } else {
                izinBox.classList.add('hidden');
            }
        }

        // Tampilan Hasil Gagal / Error
        function renderErrorResult(payload) {
            document.getElementById('result-placeholder').classList.add('hidden');
            const detailsEl = document.getElementById('result-details');
            detailsEl.classList.remove('hidden');

            const perfBadge = document.getElementById('scan-perf-badge');
            if (payload.meta?.waktu_eksekusi_ms) {
                perfBadge.textContent = `⚡ ${payload.meta.waktu_eksekusi_ms}ms`;
                perfBadge.classList.remove('hidden');
            }

            // Atur status banner ke Merah
            const banner = document.getElementById('result-status-banner');
            const bannerIcon = document.getElementById('result-status-icon');
            const bannerTitle = document.getElementById('result-status-title');
            const bannerDesc = document.getElementById('result-status-desc');

            banner.className = 'p-4 rounded-xl border flex items-center gap-3 bg-rose-500/10 border-rose-500/30 text-rose-300';
            bannerIcon.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-400"></i>';
            bannerTitle.textContent = 'PEMINDAIAN DITOLAK';
            bannerDesc.textContent = payload.message || 'Terjadi kesalahan saat memproses presensi.';

            document.getElementById('result-izin-box').classList.add('hidden');
        }

        function renderNetworkErrorResult(err) {
            const banner = document.getElementById('result-status-banner');
            banner.className = 'p-4 rounded-xl border flex items-center gap-3 bg-rose-500/10 border-rose-500/30 text-rose-300';
            document.getElementById('result-status-icon').innerHTML = '<i class="fa-solid fa-triangle-exclamation text-rose-400"></i>';
            document.getElementById('result-status-title').textContent = 'KONEKSI TERPUTUS';
            document.getElementById('result-status-desc').textContent = 'Gagal menghubungi server absensi. Periksa koneksi lokal Anda.';
        }

        // Update Stat Counter Realtime
        function updateStatsCounter(status) {
            if (!status) return;
            const targetId = `stat-${status.toLowerCase()}`;
            const el = document.getElementById(targetId);
            if (el) {
                const current = parseInt(el.textContent) || 0;
                el.textContent = current + 1;
                // Animate count bump
                el.classList.add('scale-125', 'text-white');
                setTimeout(() => el.classList.remove('scale-125', 'text-white'), 300);
            }
        }

        // Tambahkan santri ke baris teratas feed
        function prependRecentFeed(data) {
            const feedList = document.getElementById('recent-feed-list');
            const emptyState = document.getElementById('feed-empty-state');
            if (emptyState) emptyState.remove();

            const status = data.status || 'hadir';
            let badgeClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
            if (status === 'terlambat') badgeClass = 'bg-amber-500/10 text-amber-400 border-amber-500/30';
            else if (status === 'izin') badgeClass = 'bg-sky-500/10 text-sky-400 border-sky-500/30';
            else if (status === 'sakit') badgeClass = 'bg-purple-500/10 text-purple-400 border-purple-500/30';

            const waktuFormatted = data.waktu_absen ? data.waktu_absen.split(' ')[1] : new Date().toLocaleTimeString('id-ID');

            const itemHtml = `
                <article class="p-3 rounded-xl bg-slate-950/60 border border-emerald-500/40 hover:border-slate-700 flex items-center justify-between gap-3 transition animate-fadeIn">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center font-bold text-xs text-emerald-300 shrink-0">
                            ${(data.santri?.nama || 'S').charAt(0).toUpperCase()}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-semibold text-slate-200 truncate">
                                ${data.santri?.nama || 'Santri'}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                ${data.santri?.nis || '-'} • ${data.santri?.kelas || '-'}
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded border ${badgeClass}">
                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                        </span>
                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                            ${waktuFormatted}
                        </div>
                    </div>
                </article>
            `;

            feedList.insertAdjacentHTML('afterbegin', itemHtml);

            // Perbarui badge jumlah di header feed
            const badge = document.getElementById('feed-count-badge');
            if (badge) {
                const count = feedList.querySelectorAll('article').length;
                badge.textContent = `${count} santri`;
            }
        }
    </script>
</body>
</html>
