@extends('layouts.app')

@section('title', 'Pemindai Presensi QR Code Santri - E-Absensi Santri')

@push('styles')
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom Laser Scan Animation */
        @keyframes scanLaser {
            0% { top: 6%; opacity: 0.2; }
            50% { opacity: 0.9; }
            100% { top: 92%; opacity: 0.2; }
        }
        .scan-laser-line {
            animation: scanLaser 2.2s ease-in-out infinite alternate;
        }

        /* Subtle Pulse Effect */
        @keyframes subtleGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(16, 185, 129, 0.15); }
            50% { box-shadow: 0 0 25px rgba(16, 185, 129, 0.35); }
        }
        .glow-active {
            animation: subtleGlow 3s infinite;
        }

        /* html5-qrcode video overrides */
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
@endpush

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- ========================================================
         HERO BANNER PRESENSI (Sesuai Desain Standar Halaman Lain)
    ======================================================== --}}
    {{-- ========================================================
         HERO BANNER PEMINDAI PRESENSI QR (Ringkas & Menyatu)
    ======================================================== --}}
    <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-4 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-xl">
                    📷
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                            <span>Live Scanner v2.0 • Realtime</span>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-200">PP. Miftahul Huda Kroya</span>
                    </div>
                    <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                        Pemindai Presensi QR Code Santri
                    </h1>
                </div>
            </div>

            {{-- Widget Kontrol Sesi & Jam Digital --}}
            <div class="flex items-center gap-2.5 flex-wrap shrink-0">
                {{-- Jam Realtime --}}
                <div class="bg-black/20 backdrop-blur-md border border-white/10 rounded-xl px-3 py-1.5 text-right flex items-center gap-2">
                    <span id="live-digital-clock" class="text-xs font-mono font-bold text-emerald-200 tracking-wider">00:00:00</span>
                    <span id="live-digital-date" class="text-[10px] text-emerald-100/80"></span>
                </div>

                {{-- Action Switcher Sesi & Tombol Rekap --}}
                @if ($daftarSesi->isNotEmpty())
                    <div class="relative">
                        <select id="session-select" onchange="switchSession(this.value)"
                                class="bg-white/10 hover:bg-white/20 border border-white/20 text-xs text-white font-semibold py-1.5 px-3 pr-7 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 transition appearance-none cursor-pointer">
                            @foreach ($daftarSesi as $itemSesi)
                                <option value="{{ $itemSesi->id }}" {{ ($sesiAktif && $sesiAktif->id === $itemSesi->id) ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $itemSesi->nama_sesi }} ({{ \Carbon\Carbon::parse($itemSesi->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($itemSesi->jam_selesai)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-2.5 top-2.5 text-[9px] text-emerald-200 pointer-events-none"></i>
                    </div>
                @endif

                <a href="{{ route('rekap.absensi') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-xs font-semibold text-white shadow-sm transition active:scale-95">
                    <i class="fa-solid fa-chart-pie text-emerald-200"></i>
                    <span>Rekap</span>
                </a>

                <button id="audio-toggle-btn" onclick="toggleAudio()" title="Suara Beep Scan"
                        class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center text-white transition active:scale-95">
                    <i id="audio-icon" class="fa-solid fa-volume-high text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================
         BANNER STATUS SESI & METRIK CEPAT
    ======================================================== --}}
    @if ($sesiAktif)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-lg shrink-0">
                        🕌
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                                {{ $sesiAktif->nama_sesi }}
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                Sesi Aktif
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono">
                                {{ $sesiAktif->kode_sesi }}
                            </span>
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                            <span>📅 {{ \Carbon\Carbon::parse($sesiAktif->tanggal)->translatedFormat('l, d F Y') }}</span>
                            <span>⏰ Jam: <strong>{{ substr($sesiAktif->jam_mulai, 0, 5) }} - {{ substr($sesiAktif->jam_selesai, 0, 5) }} WIB</strong></span>
                            @if ($sesiAktif->batas_terlambat)
                                <span class="text-amber-600 dark:text-amber-400 font-medium">
                                    ⚠️ Batas Tepat Waktu: {{ substr($sesiAktif->batas_terlambat, 0, 5) }} WIB
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stat Counter Cards --}}
                <div class="grid grid-cols-5 gap-1 sm:gap-2 text-center shrink-0">
                    <div class="bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40 rounded-xl px-1 sm:px-2.5 py-1.5 sm:py-2">
                        <div class="text-[9px] sm:text-[10px] text-emerald-700 dark:text-emerald-400 uppercase tracking-tight sm:tracking-wider font-bold">Hadir</div>
                        <div id="stat-hadir" class="text-sm sm:text-lg font-extrabold text-emerald-700 dark:text-emerald-300 font-mono">{{ $stats['hadir'] }}</div>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40 rounded-xl px-1 sm:px-2.5 py-1.5 sm:py-2">
                        <div class="text-[9px] sm:text-[10px] text-amber-700 dark:text-amber-400 uppercase tracking-tight sm:tracking-wider font-bold">
                            <span class="sm:hidden">Telat</span>
                            <span class="hidden sm:inline">Terlambat</span>
                        </div>
                        <div id="stat-terlambat" class="text-sm sm:text-lg font-extrabold text-amber-700 dark:text-amber-300 font-mono">{{ $stats['terlambat'] }}</div>
                    </div>
                    <div class="bg-sky-50 dark:bg-sky-950/30 border border-sky-200/60 dark:border-sky-800/40 rounded-xl px-1 sm:px-2.5 py-1.5 sm:py-2">
                        <div class="text-[9px] sm:text-[10px] text-sky-700 dark:text-sky-400 uppercase tracking-tight sm:tracking-wider font-bold">Izin</div>
                        <div id="stat-izin" class="text-sm sm:text-lg font-extrabold text-sky-700 dark:text-sky-300 font-mono">{{ $stats['izin'] }}</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/40 rounded-xl px-1 sm:px-2.5 py-1.5 sm:py-2">
                        <div class="text-[9px] sm:text-[10px] text-purple-700 dark:text-purple-400 uppercase tracking-tight sm:tracking-wider font-bold">Sakit</div>
                        <div id="stat-sakit" class="text-sm sm:text-lg font-extrabold text-purple-700 dark:text-purple-300 font-mono">{{ $stats['sakit'] }}</div>
                    </div>
                    <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200/60 dark:border-rose-800/40 rounded-xl px-1 sm:px-2.5 py-1.5 sm:py-2">
                        <div class="text-[9px] sm:text-[10px] text-rose-700 dark:text-rose-400 uppercase tracking-tight sm:tracking-wider font-bold">
                            <span class="sm:hidden">Belum</span>
                            <span class="hidden sm:inline">Belum Absen</span>
                        </div>
                        <div id="stat-alpa" class="text-sm sm:text-lg font-extrabold text-rose-700 dark:text-rose-300 font-mono">{{ $stats['alpa'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-2xl p-6 text-center">
            <span class="text-3xl block mb-2">⚠️</span>
            <h2 class="text-base font-bold text-amber-900 dark:text-amber-200">Tidak Ada Sesi Absensi yang Aktif</h2>
            <p class="text-xs text-amber-800 dark:text-amber-300/80 mt-1 max-w-md mx-auto">
                Saat ini belum ada sesi presensi yang sedang berlangsung. Silakan pilih sesi dari jadwal harian atau buka sesi melalui menu jadwal sholat.
            </p>
        </div>
    @endif

    {{-- ========================================================
         MAIN SCANNER SECTION (2 KOLOM: KAMERA & HASIL SCAN)
    ======================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- KOLOM KIRI: KAMERA SCANNER & INPUT MANUAL (7 COLS) --}}
        <div class="lg:col-span-7 space-y-5">

            {{-- Kartu Viewfinder Kamera --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 relative">
                <!-- Mode Switcher Presensi: Deteksi Wajah AI vs QR Code -->
                <div class="flex items-center p-1 bg-slate-100 dark:bg-slate-800 rounded-xl mb-4">
                    <button type="button" id="tab-mode-face" onclick="switchScanMode('face')" class="flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm">
                        <span>👤</span>
                        <span>Deteksi Wajah (Face AI)</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-semibold hidden sm:inline">AI Realtime</span>
                    </button>
                    <button type="button" id="tab-mode-qr" onclick="switchScanMode('qr')" class="flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        <span>📷</span>
                        <span>Pemindai QR Code</span>
                    </button>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span id="scanner-indicator-dot" class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></span>
                        <h2 id="scanner-header-title" class="font-bold text-sm text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>👤</span>
                            <span>Kamera Presensi Wajah AI</span>
                        </h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="switch-camera-btn" onclick="toggleCameraFacing()"
                                class="px-2.5 py-1.5 text-xs bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 transition active:scale-95">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            <span class="hidden sm:inline">Balik Kamera</span>
                        </button>
                        <button id="toggle-scanner-btn" onclick="toggleScanner()"
                                class="px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition active:scale-95 flex items-center gap-1.5">
                            <i id="toggle-scanner-icon" class="fa-solid fa-stop"></i>
                            <span id="toggle-scanner-text">Jeda Kamera</span>
                        </button>
                    </div>
                </div>

                {{-- Viewfinder Frame --}}
                <div class="relative w-full aspect-[4/3] sm:aspect-[16/10] bg-slate-950 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 flex items-center justify-center shadow-inner">
                    <!-- QR Reader Container -->
                    <div id="qr-reader" class="w-full h-full hidden"></div>

                    <!-- Face AI Camera Stream & Canvas Overlay -->
                    <video id="face-stream-video" autoplay muted playsinline class="w-full h-full object-cover transform -scale-x-100"></video>
                    <canvas id="face-stream-canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                    <!-- Face Guide Oval Reticle (Hanya muncul saat mode wajah) -->
                    <div id="face-reticle-guide" class="absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="w-52 h-64 border-2 border-dashed border-blue-400/50 rounded-[45%] flex items-center justify-center relative">
                            <span class="text-[10px] font-semibold text-blue-200/90 bg-slate-950/70 px-2.5 py-0.5 rounded-full backdrop-blur-sm">Arahkan Wajah ke Sini</span>
                        </div>
                    </div>

                    {{-- QR Reticle Guide (Hanya muncul saat mode QR) --}}
                    <div id="scanner-reticle" class="hidden absolute inset-0 pointer-events-none flex items-center justify-center">
                        <div class="w-60 h-60 sm:w-68 sm:h-68 border-2 border-emerald-500/50 rounded-2xl relative">
                            <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-emerald-400 rounded-tl-lg"></div>
                            <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-emerald-400 rounded-tr-lg"></div>
                            <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-emerald-400 rounded-bl-lg"></div>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-emerald-400 rounded-br-lg"></div>
                            <div id="scan-laser" class="scan-laser-line absolute left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_12px_#34d399]"></div>
                        </div>
                    </div>

                    {{-- Loading Spinner Overlay --}}
                    <div id="scanner-loading" class="hidden absolute inset-0 bg-slate-950/80 backdrop-blur-sm flex flex-col items-center justify-center gap-3 z-30">
                        <div class="w-10 h-10 border-4 border-blue-500/20 border-t-blue-500 rounded-full animate-spin"></div>
                        <span id="scanner-loading-text" class="text-xs font-semibold text-blue-400 tracking-wide">Memproses Presensi Santri...</span>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span id="scanner-help-text" class="flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                        Posisikan wajah santri di depan kamera untuk verifikasi otomatis
                    </span>
                    <span id="scanner-status-text" class="text-blue-600 dark:text-blue-400 font-mono font-semibold">Status: Menghubungkan...</span>
                </div>
            </div>

            {{-- Kartu Input Manual / USB Scanner Gun --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span>⌨️</span>
                        <span>Input Manual / Scanner Barcode USB Gun</span>
                    </h2>
                    <span class="text-[11px] text-slate-400">Tekan Enter untuk scan</span>
                </div>

                <form id="form-manual-scan" onsubmit="handleManualSubmit(event)" class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text"
                               id="nis-input"
                               name="nis"
                               placeholder="Ketik NIS santri atau scan barcode USB..."
                               autocomplete="off"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 font-mono outline-none transition">
                        <button type="button"
                                onclick="clearNisInput()"
                                class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs hidden"
                                id="clear-nis-btn">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <button type="submit"
                            id="submit-manual-btn"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition active:scale-95 flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-check"></i>
                        <span>Proses Absen</span>
                    </button>
                </form>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">
                    * Kompatibel otomatis dengan USB barcode scanner tanpa perlu mengklik tombol mouse.
                </p>
            </div>

        </div>

        {{-- KOLOM KANAN: HASIL SCAN & FEED TERBARU (5 COLS) --}}
        <div class="lg:col-span-5 space-y-5">

            {{-- Kartu Hasil Scan Terakhir --}}
            <div id="result-container" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 relative overflow-hidden transition-all duration-300">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <h2 class="font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span>🪪</span>
                        <span>Hasil Pemindaian Terakhir</span>
                    </h2>
                    <span id="scan-perf-badge" class="hidden text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                        ⚡ 0ms
                    </span>
                </div>

                {{-- Placeholder Menunggu Scan --}}
                <div id="result-placeholder" class="py-10 flex flex-col items-center justify-center text-center text-slate-400">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-2xl mb-3 text-slate-400">
                        📭
                    </div>
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300">Menunggu Pemindaian QR</h3>
                    <p class="text-[11px] text-slate-400 max-w-xs mt-1">
                        Arahkan kamera ke QR santri atau ketik NIS untuk melihat hasil verifikasi kehadiran di sini.
                    </p>
                </div>

                {{-- Detail Hasil Scan --}}
                <div id="result-details" class="hidden flex flex-col gap-4">
                    <div class="flex items-start gap-3.5">
                        <div id="result-avatar" class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-extrabold text-xl flex items-center justify-center shadow shrink-0">
                            S
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 id="result-nama" class="text-base font-bold text-slate-900 dark:text-white truncate">Nama Santri</h3>
                                <span id="result-jk" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">L</span>
                            </div>
                            <div class="text-xs font-mono text-emerald-600 dark:text-emerald-400 mt-0.5 font-semibold" id="result-nis">NIS: -</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                                <span>📚 <span id="result-kelas">-</span></span>
                                <span>🏠 <span id="result-asrama">-</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Banner Status --}}
                    <div id="result-status-banner" class="p-3.5 rounded-xl border flex items-center gap-3 bg-emerald-50 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300">
                        <div id="result-status-icon" class="text-2xl">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-xs uppercase font-extrabold tracking-wider" id="result-status-title">PRESENSI BERHASIL</div>
                            <div class="text-xs mt-0.5 opacity-90" id="result-status-desc">Hadir tepat waktu</div>
                        </div>
                    </div>

                    {{-- Metadata Grid --}}
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Waktu Presensi</span>
                            <span id="result-waktu" class="font-mono text-slate-800 dark:text-slate-200 font-bold">-</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Metode Scan</span>
                            <span id="result-metode" class="text-slate-800 dark:text-slate-200 font-medium">QR Code</span>
                        </div>
                    </div>

                    {{-- Alert Perizinan Otomatis --}}
                    <div id="result-izin-box" class="hidden p-3 rounded-xl bg-sky-50 border border-sky-200 dark:bg-sky-950/40 dark:border-sky-800 text-sky-800 dark:text-sky-200 text-xs">
                        <div class="font-bold flex items-center gap-1.5 text-sky-700 dark:text-sky-300">
                            <span>📄</span>
                            <span>Surat Perizinan Aktif Terdeteksi</span>
                        </div>
                        <p id="result-izin-desc" class="mt-1 text-[11px] text-sky-600 dark:text-sky-300/90"></p>
                    </div>
                </div>
            </div>

            {{-- Kartu Riwayat Presensi Sesi Ini --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 flex flex-col">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <h2 class="font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <span>📋</span>
                        <span>Riwayat Presensi Sesi Ini</span>
                    </h2>
                    <span id="feed-count-badge" class="text-[11px] px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-mono font-semibold">
                        {{ $recentAbsensis->count() }} santri
                    </span>
                </div>

                <div id="recent-feed-list" class="space-y-2 overflow-y-auto max-h-[380px] pr-1">
                    @forelse ($recentAbsensis as $item)
                        <article class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 flex items-center justify-between gap-3 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ substr($item->santri?->nama ?? 'S', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
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
                                        'hadir' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
                                        'terlambat' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
                                        'izin' => 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300',
                                        'sakit' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300',
                                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                    };
                                @endphp
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full {{ $badgeClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                                <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                    {{ $item->waktu_absen ? $item->waktu_absen->format('H:i:s') : '-' }}
                                </div>
                            </div>
                        </article>
                    @empty
                        <div id="feed-empty-state" class="py-8 text-center text-xs text-slate-400">
                            <span class="text-2xl block mb-2">⏳</span>
                            <p>Belum ada santri yang melakukan presensi pada sesi ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    {{-- ========================================================
         REKAPITULASI KEHADIRAN SESI INI
    ======================================================== --}}
    @if ($sesiAktif)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-lg shrink-0">
                        📊
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Rekapitulasi Kehadiran Sesi {{ $sesiAktif->nama_sesi }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                {{ $stats['persentase'] }}% Kehadiran
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Ringkasan langsung kehadiran santri per asrama pada sesi yang sedang berjalan.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('rekap.absensi.harian') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition active:scale-95" title="Lihat Rekap Harian Hari Ini">
                        <span>📅</span>
                        <span>Rekap Harian</span>
                    </a>
                    <a href="{{ route('rekap.absensi.excel', ['tipe' => 'harian', 'tanggal' => today()->toDateString()]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition active:scale-95 shadow" title="Unduh Excel Rekap Hari Ini">
                        <span>📗</span>
                        <span>Excel Hari Ini</span>
                    </a>
                    <a href="{{ route('rekap.absensi.pdf', ['tipe' => 'harian', 'tanggal' => today()->toDateString()]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition active:scale-95 shadow" title="Cetak PDF Rekap Hari Ini">
                        <span>📕</span>
                        <span>PDF Hari Ini</span>
                    </a>
                    <a href="{{ route('rekap.absensi', ['tipe' => 'bulanan']) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition active:scale-95 shadow" title="Lihat Rekap Bulanan">
                        <span>📊</span>
                        <span>Rekap Bulanan</span>
                    </a>
                </div>
            </div>

            {{-- Rekap Per Asrama --}}
            @if (isset($rekapPerAsrama) && $rekapPerAsrama->isNotEmpty())
                <div class="pt-4">
                    <h3 class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>🏢</span>
                        <span>Kehadiran Per Asrama</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                        @foreach ($rekapPerAsrama as $ra)
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $ra['nama_asrama'] }}</p>
                                    <p class="text-[11px] text-slate-400">{{ ucfirst($ra['gender'] ?? '-') }} • {{ $ra['hadir'] }} / {{ $ra['total'] }} Santri</p>
                                </div>
                                <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-lg {{ $ra['persen'] >= 75 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : ($ra['persen'] >= 50 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300') }}">
                                    {{ $ra['persen'] }}%
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <!-- HTML5-QRCode Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <!-- AI Face Recognition Library -->
    <script src="{{ asset('js/face-api.js') }}"></script>

    <!-- Audio Beep Generator via Web Audio API -->
    <script>
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
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, now);
                    osc.frequency.setValueAtTime(1320, now + 0.08);
                    gain.gain.setValueAtTime(0.3, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
                    osc.start(now);
                    osc.stop(now + 0.25);
                } else if (type === 'warning') {
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(587.33, now);
                    osc.frequency.setValueAtTime(440, now + 0.1);
                    gain.gain.setValueAtTime(0.3, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                    osc.start(now);
                    osc.stop(now + 0.3);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(220, now);
                    osc.frequency.setValueAtTime(164.81, now + 0.12);
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
                icon.className = 'fa-solid fa-volume-high text-xs';
                playBeep('success');
            } else {
                icon.className = 'fa-solid fa-volume-xmark text-xs opacity-50';
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

    <!-- Dual Scanner Engine: AI Face Recognition & QR Code Logic -->
    <script>
        const ACTIVE_SESI_ID = {{ $sesiAktif ? $sesiAktif->id : 'null' }};
        let currentScanMode = localStorage.getItem('absensi_scan_mode') || 'face'; // 'face' atau 'qr'
        let html5QrCode = null;
        let isScannerRunning = false;
        let currentFacingMode = "user"; // default user camera untuk deteksi wajah
        let isProcessingScan = false;
        let lastScannedCode = null;
        let lastScannedTime = 0;
        const SCAN_DEBOUNCE_MS = 2500;

        // Face AI Engine State
        let faceStream = null;
        let faceDetectionLoop = null;
        let faceApiModelsLoaded = false;
        let faceMatcher = null;
        let registeredSantris = [];
        let recentFaceScans = new Map(); // NIS -> timestamp untuk debouncing

        document.addEventListener('DOMContentLoaded', function() {
            switchScanMode(currentScanMode);

            const nisInput = document.getElementById('nis-input');
            if (nisInput) {
                setTimeout(() => nisInput.focus(), 600);
            }
        });

        async function switchScanMode(mode) {
            currentScanMode = mode;
            localStorage.setItem('absensi_scan_mode', mode);

            const tabFace = document.getElementById('tab-mode-face');
            const tabQr = document.getElementById('tab-mode-qr');
            const headerTitle = document.getElementById('scanner-header-title');
            const indicatorDot = document.getElementById('scanner-indicator-dot');
            const helpText = document.getElementById('scanner-help-text');
            const faceReticle = document.getElementById('face-reticle-guide');
            const qrReticle = document.getElementById('scanner-reticle');
            const qrReader = document.getElementById('qr-reader');
            const faceVideo = document.getElementById('face-stream-video');
            const faceCanvas = document.getElementById('face-stream-canvas');
            const toggleBtn = document.getElementById('toggle-scanner-btn');

            if (mode === 'face') {
                tabFace.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm';
                tabQr.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
                headerTitle.innerHTML = `<span>👤</span><span>Kamera Presensi Wajah AI</span>`;
                indicatorDot.className = 'w-3 h-3 rounded-full bg-blue-500 animate-pulse';
                helpText.innerHTML = `<i class="fa-solid fa-circle-info text-blue-500"></i> Posisikan wajah santri di depan kamera untuk verifikasi otomatis`;
                toggleBtn.className = 'px-3 py-1.5 text-xs bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow transition active:scale-95 flex items-center gap-1.5';

                await stopQrScanner();
                qrReader.classList.add('hidden');
                qrReticle.classList.add('hidden');
                faceVideo.classList.remove('hidden');
                faceCanvas.classList.remove('hidden');
                faceReticle.classList.remove('hidden');

                if (ACTIVE_SESI_ID) {
                    await startFaceScanner();
                }
            } else {
                tabQr.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm';
                tabFace.className = 'flex-1 py-1.5 px-3 rounded-lg text-xs font-bold transition flex items-center justify-center gap-2 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
                headerTitle.innerHTML = `<span>📷</span><span>Kamera Pemindai QR Code</span>`;
                indicatorDot.className = 'w-3 h-3 rounded-full bg-emerald-500 animate-pulse';
                helpText.innerHTML = `<i class="fa-solid fa-circle-info text-emerald-500"></i> Arahkan QR Code Kartu Santri ke kotak target`;
                toggleBtn.className = 'px-3 py-1.5 text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow transition active:scale-95 flex items-center gap-1.5';

                stopFaceScanner();
                faceVideo.classList.add('hidden');
                faceCanvas.classList.add('hidden');
                faceReticle.classList.add('hidden');
                qrReader.classList.remove('hidden');
                qrReticle.classList.remove('hidden');

                if (ACTIVE_SESI_ID) {
                    await startQrScanner();
                }
            }
        }

        // ==========================================
        // FACE AI ENGINE LOGIC
        // ==========================================
        async function ensureFaceModels() {
            if (faceApiModelsLoaded) return true;
            try {
                const LOCAL_MODELS = '{{ asset("models/face-api") }}';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(LOCAL_MODELS),
                    faceapi.nets.faceLandmark68Net.loadFromUri(LOCAL_MODELS),
                    faceapi.nets.faceRecognitionNet.loadFromUri(LOCAL_MODELS),
                ]);
                faceApiModelsLoaded = true;
                return true;
            } catch (e) {
                console.warn('Fallback CDN face models:', e);
                try {
                    const CDN_MODELS = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(CDN_MODELS),
                        faceapi.nets.faceLandmark68Net.loadFromUri(CDN_MODELS),
                        faceapi.nets.faceRecognitionNet.loadFromUri(CDN_MODELS),
                    ]);
                    faceApiModelsLoaded = true;
                    return true;
                } catch (err) {
                    console.error('Fatal load face models:', err);
                    return false;
                }
            }
        }

        async function loadFaceDatabase() {
            try {
                const resp = await fetch('{{ route("absensi.face-descriptors") }}');
                const json = await resp.json();
                if (json.success && Array.isArray(json.data) && json.data.length > 0) {
                    registeredSantris = json.data;
                    const labeledDescriptors = json.data.map(item => {
                        const descriptorArray = new Float32Array(item.descriptor);
                        return new faceapi.LabeledFaceDescriptors(item.nis, [descriptorArray]);
                    });
                    faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.48);
                    return true;
                } else {
                    registeredSantris = [];
                    faceMatcher = null;
                    return false;
                }
            } catch (err) {
                console.error('[Face AI] Gagal memuat database wajah:', err);
                return false;
            }
        }

        async function startFaceScanner() {
            const video = document.getElementById('face-stream-video');
            const canvas = document.getElementById('face-stream-canvas');
            const statusText = document.getElementById('scanner-status-text');

            if (!video || !canvas) return;

            statusText.textContent = 'Status: Memuat AI & Database Wajah...';

            await Promise.all([
                ensureFaceModels(),
                loadFaceDatabase()
            ]);

            try {
                stopFaceScanner();
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: currentFacingMode }
                });
                faceStream = stream;
                video.srcObject = stream;
                if (currentFacingMode === 'user') {
                    video.classList.add('-scale-x-100');
                } else {
                    video.classList.remove('-scale-x-100');
                }
                await video.play();

                isScannerRunning = true;
                updateScannerUiState(true);

                if (registeredSantris.length === 0) {
                    statusText.textContent = 'Status: AI Siap (0 Wajah Terdaftar di Master)';
                } else {
                    statusText.textContent = `Status: AI Siap (${registeredSantris.length} Wajah Santri)`;
                }

                const displaySize = { width: video.videoWidth || 640, height: video.videoHeight || 480 };
                faceapi.matchDimensions(canvas, displaySize);

                faceDetectionLoop = setInterval(async () => {
                    if (!video || video.paused || video.ended || !faceApiModelsLoaded) return;

                    const detections = await faceapi.detectAllFaces(
                        video,
                        new faceapi.TinyFaceDetectorOptions({ inputSize: 256, scoreThreshold: 0.5 })
                    ).withFaceLandmarks().withFaceDescriptors();

                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    if (!detections || detections.length === 0) return;

                    const resized = faceapi.resizeResults(detections, displaySize);
                    const isMirrored = (currentFacingMode === 'user');

                    resized.forEach(d => {
                        const box = d.detection.box;
                        const drawX = isMirrored ? (displaySize.width - (box.x + box.width)) : box.x;
                        let label = registeredSantris.length === 0 ? 'Wajah Belum Terdaftar' : 'Wajah Tidak Dikenali';
                        let boxColor = '#f59e0b'; // amber
                        let matchedSantri = null;

                        if (faceMatcher) {
                            const bestMatch = faceMatcher.findBestMatch(d.descriptor);
                            if (bestMatch.label !== 'unknown') {
                                matchedSantri = registeredSantris.find(s => s.nis === bestMatch.label);
                                const persen = Math.round((1 - bestMatch.distance) * 100);
                                label = matchedSantri ? `${matchedSantri.nama} (${persen}%)` : `${bestMatch.label} (${persen}%)`;
                                boxColor = '#10b981'; // emerald green

                                // Eksekusi presensi otomatis jika lolos cooldown
                                const now = Date.now();
                                const lastScan = recentFaceScans.get(bestMatch.label) || 0;
                                if (now - lastScan > 3500 && !isProcessingScan) {
                                    recentFaceScans.set(bestMatch.label, now);
                                    processAttendanceScan(bestMatch.label);
                                }
                            }
                        }

                        // Gambar Bounding Box Wajah dengan Rounded Corner
                        ctx.strokeStyle = boxColor;
                        ctx.lineWidth = 3;
                        ctx.beginPath();
                        if (typeof ctx.roundRect === 'function') {
                            ctx.roundRect(drawX, box.y, box.width, box.height, 10);
                        } else {
                            ctx.rect(drawX, box.y, box.width, box.height);
                        }
                        ctx.stroke();

                        // Label Background
                        ctx.fillStyle = boxColor;
                        ctx.font = 'bold 12px Plus Jakarta Sans, sans-serif';
                        const textWidth = ctx.measureText(label).width;
                        const labelWidth = Math.max(textWidth + 20, 140);
                        ctx.fillRect(drawX, Math.max(0, box.y - 24), labelWidth, 22);

                        // Label Text
                        ctx.fillStyle = '#ffffff';
                        ctx.fillText(label, drawX + 10, Math.max(15, box.y - 8));
                    });
                }, 220);
            } catch (err) {
                console.warn('Akses kamera face scanner gagal:', err);
                statusText.textContent = 'Kamera tidak aktif / butuh izin browser';
                updateScannerUiState(false);
            }
        }

        function stopFaceScanner() {
            if (faceDetectionLoop) {
                clearInterval(faceDetectionLoop);
                faceDetectionLoop = null;
            }
            if (faceStream) {
                faceStream.getTracks().forEach(t => t.stop());
                faceStream = null;
            }
            const video = document.getElementById('face-stream-video');
            if (video) video.srcObject = null;
            const canvas = document.getElementById('face-stream-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
            isScannerRunning = false;
            updateScannerUiState(false);
        }

        // ==========================================
        // QR CODE SCANNER LOGIC
        // ==========================================
        async function startQrScanner() {
            const qrRegion = document.getElementById('qr-reader');
            if (!qrRegion) return;

            try {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("qr-reader");
                }
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
                const statusEl = document.getElementById('scanner-status-text');
                if (statusEl) statusEl.textContent = "Kamera tidak aktif / butuh izin";
                updateScannerUiState(false);
            }
        }

        async function stopQrScanner() {
            if (html5QrCode && isScannerRunning) {
                try {
                    await html5QrCode.stop();
                } catch (e) {
                    console.error("Gagal menghentikan kamera QR:", e);
                }
            }
            isScannerRunning = false;
            updateScannerUiState(false);
        }

        function toggleScanner() {
            if (currentScanMode === 'face') {
                if (isScannerRunning) {
                    stopFaceScanner();
                } else {
                    startFaceScanner();
                }
            } else {
                if (isScannerRunning) {
                    stopQrScanner();
                } else {
                    startQrScanner();
                }
            }
        }

        async function toggleCameraFacing() {
            currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
            if (currentScanMode === 'face') {
                if (isScannerRunning) {
                    stopFaceScanner();
                    await startFaceScanner();
                }
            } else {
                if (isScannerRunning) {
                    await stopQrScanner();
                    await startQrScanner();
                }
            }
        }

        function updateScannerUiState(active) {
            const btnText = document.getElementById('toggle-scanner-text');
            const btnIcon = document.getElementById('toggle-scanner-icon');
            const laser = document.getElementById('scan-laser');
            const statusText = document.getElementById('scanner-status-text');

            if (!btnText || !btnIcon || !laser || !statusText) return;

            if (active) {
                btnText.textContent = 'Jeda Kamera';
                btnIcon.className = 'fa-solid fa-stop';
                laser.classList.remove('hidden');
                statusText.textContent = 'Status: Aktif';
                statusText.className = 'text-emerald-600 dark:text-emerald-400 font-mono font-semibold';
            } else {
                btnText.textContent = 'Mulai Kamera';
                btnIcon.className = 'fa-solid fa-play';
                laser.classList.add('hidden');
                statusText.textContent = 'Status: Dijeda';
                statusText.className = 'text-amber-600 dark:text-amber-400 font-mono font-semibold';
            }
        }

        function onQrCodeScanned(decodedText, decodedResult) {
            const now = Date.now();
            if (decodedText === lastScannedCode && (now - lastScannedTime < SCAN_DEBOUNCE_MS)) {
                return;
            }

            if (isProcessingScan) return;

            lastScannedCode = decodedText;
            lastScannedTime = now;

            processAttendanceScan(decodedText.trim());
        }

        function onQrScanError(errorMessage) {}

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

        async function processAttendanceScan(nisValue) {
            if (!ACTIVE_SESI_ID) {
                window.appAlert({
                    title: 'Sesi Belum Dipilih',
                    message: 'Tidak ada sesi absensi yang aktif saat ini. Silakan pilih sesi presensi terlebih dahulu di panel atas.',
                    icon: 'warning'
                });
                return;
            }

            let cleanNis = String(nisValue || '').trim();
            if (cleanNis.startsWith('{') && cleanNis.endsWith('}')) {
                try {
                    const parsed = JSON.parse(cleanNis);
                    if (parsed.nis) cleanNis = String(parsed.nis).trim();
                } catch (e) {}
            }
            const matchNis = cleanNis.match(/^nis\s*[:=]\s*(.+)$/i);
            if (matchNis) {
                cleanNis = matchNis[1].trim();
            }

            if (!cleanNis) return;

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
                        nis: cleanNis,
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

                const nisInput = document.getElementById('nis-input');
                if (nisInput) {
                    nisInput.value = '';
                    document.getElementById('clear-nis-btn')?.classList.add('hidden');
                    nisInput.focus();
                }
            }
        }

        function renderSuccessResult(payload) {
            const data = payload.data;
            const meta = payload.meta || {};

            document.getElementById('result-placeholder').classList.add('hidden');
            const detailsEl = document.getElementById('result-details');
            detailsEl.classList.remove('hidden');

            const initial = (data.santri?.nama || 'S').charAt(0).toUpperCase();
            document.getElementById('result-avatar').textContent = initial;

            document.getElementById('result-nama').textContent = data.santri?.nama || 'Nama Santri';
            document.getElementById('result-jk').textContent = data.santri?.jenis_kelamin || 'L';
            document.getElementById('result-nis').textContent = `NIS: ${data.santri?.nis || '-'}`;
            document.getElementById('result-kelas').textContent = data.santri?.kelas || '-';
            document.getElementById('result-asrama').textContent = data.santri?.asrama || '-';
            document.getElementById('result-waktu').textContent = data.waktu_absen || '-';
            document.getElementById('result-metode').textContent = data.metode === 'qr_code' ? 'QR Code Scanner' : 'Input NIS Manual';

            const perfBadge = document.getElementById('scan-perf-badge');
            if (meta.waktu_eksekusi_ms) {
                perfBadge.textContent = `⚡ ${meta.waktu_eksekusi_ms}ms`;
                perfBadge.classList.remove('hidden');
            }

            const banner = document.getElementById('result-status-banner');
            const bannerIcon = document.getElementById('result-status-icon');
            const bannerTitle = document.getElementById('result-status-title');
            const bannerDesc = document.getElementById('result-status-desc');

            const status = data.status;
            if (status === 'hadir') {
                banner.className = 'p-3.5 rounded-xl border flex items-center gap-3 bg-emerald-50 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300';
                bannerIcon.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>';
                bannerTitle.textContent = 'PRESENSI BERHASIL (HADIR)';
                bannerDesc.textContent = data.keterangan || 'Santri hadir tepat waktu.';
            } else if (status === 'terlambat') {
                banner.className = 'p-3.5 rounded-xl border flex items-center gap-3 bg-amber-50 border-amber-200 dark:bg-amber-950/40 dark:border-amber-800 text-amber-800 dark:text-amber-300';
                bannerIcon.innerHTML = '<i class="fa-solid fa-clock text-amber-600 dark:text-amber-400"></i>';
                bannerTitle.textContent = 'PRESENSI TERLAMBAT';
                bannerDesc.textContent = data.keterangan || 'Presensi tercatat melewati batas waktu.';
            } else if (status === 'izin' || status === 'sakit') {
                banner.className = 'p-3.5 rounded-xl border flex items-center gap-3 bg-sky-50 border-sky-200 dark:bg-sky-950/40 dark:border-sky-800 text-sky-800 dark:text-sky-300';
                bannerIcon.innerHTML = '<i class="fa-solid fa-file-medical text-sky-600 dark:text-sky-400"></i>';
                bannerTitle.textContent = `PRESENSI OTOMATIS: ${status.toUpperCase()}`;
                bannerDesc.textContent = data.keterangan || 'Santri memiliki surat perizinan resmi.';
            } else {
                banner.className = 'p-3.5 rounded-xl border flex items-center gap-3 bg-emerald-50 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300';
                bannerIcon.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>';
                bannerTitle.textContent = `PRESENSI BERHASIL (${(status || 'HADIR').toUpperCase()})`;
                bannerDesc.textContent = data.keterangan || 'Presensi berhasil dicatat.';
            }

            const izinBox = document.getElementById('result-izin-box');
            if (meta.status_otomatis_izin && meta.perizinan_aktif) {
                izinBox.classList.remove('hidden');
                document.getElementById('result-izin-desc').textContent =
                    `Surat ${meta.perizinan_aktif.nomor_surat} (${meta.perizinan_aktif.jenis_izin}): ${meta.perizinan_aktif.keperluan}`;
            } else {
                izinBox.classList.add('hidden');
            }
        }

        function renderErrorResult(payload) {
            document.getElementById('result-placeholder').classList.add('hidden');
            const detailsEl = document.getElementById('result-details');
            detailsEl.classList.remove('hidden');

            const perfBadge = document.getElementById('scan-perf-badge');
            if (payload.meta?.waktu_eksekusi_ms) {
                perfBadge.textContent = `⚡ ${payload.meta.waktu_eksekusi_ms}ms`;
                perfBadge.classList.remove('hidden');
            }

            const banner = document.getElementById('result-status-banner');
            const bannerIcon = document.getElementById('result-status-icon');
            const bannerTitle = document.getElementById('result-status-title');
            const bannerDesc = document.getElementById('result-status-desc');

            banner.className = 'p-3.5 rounded-xl border flex items-center gap-3 bg-rose-50 border-rose-200 dark:bg-rose-950/40 dark:border-rose-800 text-rose-800 dark:text-rose-300';
            bannerIcon.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-600 dark:text-rose-400"></i>';
            bannerTitle.textContent = 'PEMINDAIAN DITOLAK';
            bannerDesc.textContent = payload.message || 'Terjadi kesalahan saat memproses presensi.';

            document.getElementById('result-izin-box').classList.add('hidden');
        }

        function renderNetworkErrorResult(err) {
            const banner = document.getElementById('result-status-banner');
            banner.className = 'p-3.5 rounded-xl border flex items-center gap-3 bg-rose-50 border-rose-200 dark:bg-rose-950/40 dark:border-rose-800 text-rose-800 dark:text-rose-300';
            document.getElementById('result-status-icon').innerHTML = '<i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400"></i>';
            document.getElementById('result-status-title').textContent = 'KONEKSI TERPUTUS';
            document.getElementById('result-status-desc').textContent = 'Gagal menghubungi server absensi. Periksa koneksi lokal Anda.';
        }

        function updateStatsCounter(status) {
            if (!status) return;
            const targetId = `stat-${status.toLowerCase()}`;
            const el = document.getElementById(targetId);
            if (el) {
                const current = parseInt(el.textContent) || 0;
                el.textContent = current + 1;
                el.classList.add('scale-125');
                setTimeout(() => el.classList.remove('scale-125'), 300);
            }
        }

        function prependRecentFeed(data) {
            const feedList = document.getElementById('recent-feed-list');
            const emptyState = document.getElementById('feed-empty-state');
            if (emptyState) emptyState.remove();

            const status = data.status || 'hadir';
            let badgeClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300';
            if (status === 'terlambat') badgeClass = 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300';
            else if (status === 'izin') badgeClass = 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300';
            else if (status === 'sakit') badgeClass = 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300';

            const waktuFormatted = data.waktu_absen ? (data.waktu_absen.split(' ')[1] || data.waktu_absen) : new Date().toLocaleTimeString('id-ID');

            const itemHtml = `
                <article class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-emerald-500/40 flex items-center justify-between gap-3 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 flex items-center justify-center font-bold text-xs shrink-0">
                            ${(data.santri?.nama || 'S').charAt(0).toUpperCase()}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                                ${data.santri?.nama || 'Santri'}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                ${data.santri?.nis || '-'} • ${data.santri?.kelas || '-'}
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full ${badgeClass}">
                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                        </span>
                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                            ${waktuFormatted}
                        </div>
                    </div>
                </article>
            `;

            feedList.insertAdjacentHTML('afterbegin', itemHtml);

            const badge = document.getElementById('feed-count-badge');
            if (badge) {
                const count = feedList.querySelectorAll('article').length;
                badge.textContent = `${count} santri`;
            }
        }
    </script>
@endpush
