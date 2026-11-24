@extends('layouts.app')

@section('title', 'Pos Keamanan Gerbang - E-Absensi Santri')

@push('styles')
    <style>
        #kamtib-qr-reader {
            border: none !important;
            background: transparent !important;
        }
        #kamtib-qr-reader video {
            border-radius: 1rem;
            object-fit: cover !important;
            width: 100% !important;
            height: 100% !important;
        }
        #kamtib-qr-reader__scan_region {
            background: transparent !important;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- ========================================================
         HERO BANNER POS KEAMANAN (Sesuai Desain Standar Halaman Lain)
    ======================================================== --}}
    <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-5 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-xl">
                    🛡️
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                            <span>Pos Keamanan Gerbang • Live Monitoring</span>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-300">PP. Miftahul Huda Kroya</span>
                    </div>
                    <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                        Pos Keamanan &amp; Gerbang Utama
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="bg-black/20 backdrop-blur-md border border-white/10 rounded-xl px-3 py-1.5 text-right">
                    <span class="inline-flex items-center text-[11px] font-semibold text-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-ping"></span>
                        Pos Berjaga
                    </span>
                    <span class="text-[11px] font-mono font-bold text-white ml-2">
                        {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================
         STATISTIK RINGKAS HARI INI
    ======================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Santri Keluar --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center text-xl">⬆️</div>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full">Check-Out</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri Keluar Hari Ini</p>
            <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['total_keluar_hari_ini'] ?? 0 }}</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Check-Out lewat gerbang</p>
        </div>

        {{-- Santri Kembali --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center text-xl">⬇️</div>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">Check-In</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri Kembali Hari Ini</p>
            <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['total_kembali_hari_ini'] ?? 0 }}</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Check-In masuk pondok</p>
        </div>

        {{-- Santri di Luar --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 flex items-center justify-center text-xl">🚪</div>
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-full">Di Luar</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri di Luar Saat Ini</p>
            <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['santri_diluar_sekarang'] ?? 0 }}</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Izin sedang berlangsung</p>
        </div>
    </div>

    {{-- ========================================================
         AREA SCAN & VERIFIKASI SURAT IZIN DIGITAL (2 KOLOM)
    ======================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- KOLOM KIRI: SCANNER KAMERA & FORM TOKEN (5 COLS) --}}
        <div class="lg:col-span-5 space-y-5">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span>📷</span>
                            <span>Scanner QR Surat Izin</span>
                        </h2>
                    </div>
                    <span id="kamtib-camera-status" class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 font-mono">Siap Pindai</span>
                </div>

                {{-- Video Viewfinder Kamera --}}
                <div class="relative w-full aspect-square bg-slate-950 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 mb-4 flex items-center justify-center shadow-inner">
                    <div id="kamtib-qr-reader" class="w-full h-full"></div>
                    <div id="kamtib-qr-placeholder" class="text-center p-6 text-slate-400">
                        <svg class="w-14 h-14 mx-auto mb-2 text-slate-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <p class="text-xs text-slate-400">Klik tombol di bawah untuk menyalakan kamera pemindai gerbang.</p>
                    </div>
                </div>

                {{-- Kontrol Tombol Kamera --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <button type="button" id="kamtib-camera-start" class="flex-1 py-2 px-3 rounded-xl bg-orange-600 hover:bg-orange-700 active:scale-95 text-white text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Mulai Kamera</span>
                    </button>
                    <button type="button" id="kamtib-camera-switch" disabled class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-40 text-slate-700 dark:text-slate-300 text-xs font-semibold transition border border-slate-200 dark:border-slate-700" title="Balik Kamera Depan/Belakang">
                        🔄 Balik
                    </button>
                    <button type="button" id="kamtib-camera-stop" disabled class="py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-40 text-rose-600 dark:text-rose-400 text-xs font-semibold transition border border-slate-200 dark:border-slate-700">
                        ⏹ Berhenti
                    </button>
                </div>

                {{-- Info Bukti Kepulangan --}}
                <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs mb-4 flex items-start space-x-2.5">
                    <span class="text-base shrink-0">🔄</span>
                    <div class="leading-relaxed">
                        <strong class="text-emerald-900 dark:text-white block font-semibold">Bukti Kepulangan Santri:</strong>
                        Santri yang kembali dari izin <span class="text-amber-700 dark:text-amber-300 font-bold">wajib scan QR lagi</span> (Surat Izin / Kartu Santri) sebagai bukti sah kepulangan.
                    </div>
                </div>

                {{-- Form Verifikasi Manual / Scanner Gun --}}
                <form id="form-kamtib-scan" method="POST" action="{{ route('kamtib.scan.process') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Nama Petugas Jaga (Diisi Manual) *
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                id="kamtib-nama-petugas"
                                name="nama_petugas"
                                required
                                value="{{ old('nama_petugas') }}"
                                placeholder="Ketik nama petugas jaga / personil gerbang..."
                                class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                            >
                            <span class="absolute left-3 top-2.5 text-slate-400 text-xs">👮</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Nama petugas diisi manual sesuai personil yang sedang bertugas di pos gerbang.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Kode / Token QR Surat Izin *
                        </label>
                        <input
                            type="text"
                            id="kamtib-qr-token"
                            name="qr_token"
                            required
                            autofocus
                            placeholder="Scan QR surat atau ketik token di sini..."
                            class="w-full px-3.5 py-2.5 text-xs font-mono rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                        >
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="action" value="auto" checked class="peer sr-only">
                            <div class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center text-xs font-bold text-slate-600 dark:text-slate-400 peer-checked:bg-orange-600 peer-checked:border-orange-500 peer-checked:text-white transition">
                                ⚡ Auto
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="action" value="checkout" class="peer sr-only">
                            <div class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center text-xs font-bold text-slate-600 dark:text-slate-400 peer-checked:bg-amber-600 peer-checked:border-amber-500 peer-checked:text-white transition">
                                ⬆ Keluar
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="action" value="checkin" class="peer sr-only">
                            <div class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-center text-xs font-bold text-slate-600 dark:text-slate-400 peer-checked:bg-emerald-600 peer-checked:border-emerald-500 peer-checked:text-white transition">
                                ⬇ Masuk
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Catatan Petugas (Opsional)
                        </label>
                        <input
                            type="text"
                            name="catatan"
                            placeholder="Contoh: Dijemput orang tua, kondisi sehat..."
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold text-xs shadow transition active:scale-95 flex items-center justify-center space-x-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Verifikasi & Catat Gerbang</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: SANTRI DI LUAR & RIWAYAT LOG (7 COLS) --}}
        <div class="lg:col-span-7 space-y-5">

            {{-- Daftar Santri Sedang Berada di Luar Kompleks --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Santri Sedang di Luar Pesantren ({{ $santriDiluar->count() }})</h2>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">Menunggu Check-In</span>
                </div>

                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    @forelse($santriDiluar as $izin)
                        @php
                            $isOverdue = $izin->tanggal_selesai && \Carbon\Carbon::now()->greaterThan($izin->tanggal_selesai);
                        @endphp
                        <div class="p-3 rounded-xl border {{ $isOverdue ? 'border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20' : 'border-slate-200/70 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/40' }} flex items-center justify-between text-xs transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl {{ $isOverdue ? 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-200' : 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' }} font-black text-xs flex items-center justify-center shrink-0">
                                    {{ substr($izin->santri->nama ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $izin->santri->nama ?? 'Santri' }} <span class="font-mono text-slate-400 text-[11px]">({{ $izin->santri->nis ?? '-' }})</span></p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $izin->santri->asrama->nama_asrama ?? '-' }} &bull; {{ ucfirst(str_replace('_', ' ', $izin->jenis_izin)) }}
                                        @if($izin->approvedBy)
                                            &bull; <span class="text-emerald-600 dark:text-emerald-400 font-medium">Izin: {{ $izin->approvedBy->name }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-right flex items-center gap-2">
                                <div>
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $isOverdue ? 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-300' : 'bg-blue-100 dark:bg-blue-900/60 text-blue-800 dark:text-blue-300' }}">
                                        {{ $isOverdue ? '⚠️ Melewati Batas' : 'Di Luar' }}
                                    </span>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        Batas: {{ $izin->tanggal_selesai ? \Carbon\Carbon::parse($izin->tanggal_selesai)->translatedFormat('d M H:i') : '-' }}
                                    </p>
                                </div>
                                <button type="button"
                                        onclick="quickCheckin('{{ $izin->qr_token }}', '{{ addslashes($izin->santri->nama ?? 'Santri') }}')"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-[11px] shadow transition flex items-center space-x-1"
                                        title="Konfirmasi Santri Sudah Kembali (Scan Check-in)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Scan Kembali</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 dark:text-slate-500 text-xs">
                            Tidak ada santri yang sedang berada di luar pesantren saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Log Riwayat Aktivitas Gerbang Terkini --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Aktivitas Gerbang Terbaru</h2>
                    </div>
                    <span class="text-xs text-slate-400 font-mono">15 Terakhir</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="py-2.5 px-3 font-semibold">Waktu</th>
                                <th class="py-2.5 px-3 font-semibold">Santri</th>
                                <th class="py-2.5 px-3 font-semibold text-center">Aksi Gerbang</th>
                                <th class="py-2.5 px-3 font-semibold">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($recentLogs as $log)
                                @php
                                    $isCheckout = $log->tipe_log === 'checkout';
                                    $isCheckin = $log->tipe_log === 'checkin';
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-3 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ $log->waktu_log ? $log->waktu_log->format('d/m H:i') : '-' }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <p class="font-bold text-slate-800 dark:text-slate-200">{{ $log->santri->nama ?? 'Santri' }}</p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">NIS: {{ $log->santri->nis ?? '-' }}</p>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $isCheckout ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : ($isCheckin ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                            {{ $isCheckout ? '⬆ Keluar Pondok' : ($isCheckin ? '⬇ Kembali (Bukti Sah)' : ucfirst($log->tipe_log)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-500 dark:text-slate-400 text-[11px]">
                                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ $log->nama_petugas ?: ($log->recordedBy->name ?? 'Petugas Gerbang') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400 dark:text-slate-500 text-xs">
                                        Belum ada riwayat aktivitas gerbang yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($recentLogs->hasPages())
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        {{ $recentLogs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>

<!-- Audio Feedback Element -->
<audio id="audio-warning" src="https://assets.mixkit.co/active_storage/sfx/2874/2874-preview.mp3" preload="auto"></audio>
<audio id="audio-success" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
@endsection

@push('scripts')
<!-- Library HTML5-QRCode untuk Kamera Gerbang -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const readerElement = document.getElementById('kamtib-qr-reader');
    const placeholder = document.getElementById('kamtib-qr-placeholder');
    const tokenInput = document.getElementById('kamtib-qr-token');
    const petugasInput = document.getElementById('kamtib-nama-petugas');
    const form = document.getElementById('form-kamtib-scan');
    const statusElement = document.getElementById('kamtib-camera-status');
    const startButton = document.getElementById('kamtib-camera-start');
    const stopButton = document.getElementById('kamtib-camera-stop');
    const switchButton = document.getElementById('kamtib-camera-switch');

    // Mengingat nama petugas jaga terakhir di browser (localStorage) agar praktis
    if (petugasInput) {
        const savedPetugas = localStorage.getItem('kamtib_nama_petugas_manual');
        if (savedPetugas && !petugasInput.value) {
            petugasInput.value = savedPetugas;
        }
        petugasInput.addEventListener('input', function() {
            localStorage.setItem('kamtib_nama_petugas_manual', this.value.trim());
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            if (petugasInput && !petugasInput.value.trim()) {
                e.preventDefault();
                window.appAlert({
                    title: 'Nama Petugas Wajib Diisi',
                    message: 'Silakan isi Nama Petugas Jaga secara manual terlebih dahulu sebelum memverifikasi gerbang.',
                    icon: 'warning'
                });
                petugasInput.focus();
                return false;
            }
        });
    }

    let html5QrCode = null;
    let isRunning = false;
    let facingMode = 'environment';
    let lastScanned = null;
    let lastScanTime = 0;

    function playBeep(type = 'success') {
        try {
            const audio = document.getElementById(type === 'warning' ? 'audio-warning' : 'audio-success');
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(() => {});
            }
        } catch (e) {}
    }

    function setStatus(msg, colorClass = 'text-amber-600 dark:text-amber-400') {
        if (!statusElement) return;
        statusElement.textContent = msg;
        statusElement.className = 'text-[11px] font-semibold font-mono ' + colorClass;
    }

    function updateControls() {
        if (!startButton || !stopButton || !switchButton) return;
        startButton.disabled = isRunning;
        stopButton.disabled = !isRunning;
        switchButton.disabled = !isRunning;
        startButton.classList.toggle('opacity-50', isRunning);
        stopButton.classList.toggle('opacity-50', !isRunning);
        switchButton.classList.toggle('opacity-50', !isRunning);
    }

    async function startCamera() {
        if (isRunning) return;
        if (!window.Html5Qrcode) {
            setStatus('Library QR tidak tersedia', 'text-rose-500');
            return;
        }

        setStatus('Menghubungkan kamera...', 'text-amber-600 dark:text-amber-400');
        if (placeholder) placeholder.classList.add('hidden');

        try {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode('kamtib-qr-reader');
            }

            await html5QrCode.start(
                { facingMode },
                {
                    fps: 10,
                    qrbox: { width: 220, height: 220 },
                    aspectRatio: 1
                },
                onScanSuccess,
                () => {}
            );

            isRunning = true;
            setStatus('Kamera Aktif — Arahkan QR', 'text-emerald-600 dark:text-emerald-400');
            updateControls();
        } catch (err) {
            console.error('Kamera gagal dijalankan:', err);
            setStatus('Kamera tidak dapat diakses', 'text-rose-500');
            if (placeholder) placeholder.classList.remove('hidden');
            updateControls();
        }
    }

    async function stopCamera() {
        if (!isRunning || !html5QrCode) return;
        try {
            await html5QrCode.stop();
            isRunning = false;
            setStatus('Kamera Dihentikan', 'text-slate-400');
            if (placeholder) placeholder.classList.remove('hidden');
            updateControls();
        } catch (err) {
            console.error('Gagal menghentikan kamera:', err);
        }
    }

    async function switchCamera() {
        if (!isRunning) return;
        await stopCamera();
        facingMode = facingMode === 'environment' ? 'user' : 'environment';
        await startCamera();
    }

    function onScanSuccess(decodedText) {
        if (petugasInput && !petugasInput.value.trim()) {
            setStatus('Isi nama petugas dulu!', 'text-rose-500');
            window.appAlert({
                title: 'Nama Petugas Belum Diisi',
                message: 'Silakan isi <strong>Nama Petugas Jaga</strong> terlebih dahulu pada form sebelum scan.',
                icon: 'warning'
            });
            petugasInput.focus();
            playBeep('warning');
            return;
        }

        const now = Date.now();
        if (decodedText === lastScanned && now - lastScanTime < 3000) {
            return;
        }

        lastScanned = decodedText;
        lastScanTime = now;

        playBeep('success');
        setStatus('QR Berhasil Dipindai!', 'text-emerald-600 dark:text-emerald-300');

        if (tokenInput) {
            tokenInput.value = decodedText;
        }

        if (form) {
            form.submit();
        }
    }

    if (startButton) startButton.addEventListener('click', startCamera);
    if (stopButton) stopButton.addEventListener('click', stopCamera);
    if (switchButton) switchButton.addEventListener('click', switchCamera);

    window.quickCheckin = function(token, nama) {
        if (petugasInput && !petugasInput.value.trim()) {
            window.appAlert({
                title: 'Nama Petugas Belum Diisi',
                message: 'Silakan ketik <strong>Nama Petugas Jaga</strong> terlebih dahulu pada form di sebelah kiri.',
                icon: 'warning'
            });
            petugasInput.focus();
            return;
        }

        window.appConfirm({
            title: 'Konfirmasi Kepulangan Santri',
            message: `Konfirmasi kepulangan untuk santri <strong>${nama}</strong>?`,
            subtext: `
                <div class="space-y-1 font-medium">
                    <p class="text-emerald-700 dark:text-emerald-400 font-bold">✅ Verifikasi Pos Keamanan:</p>
                    <p class="text-slate-600 dark:text-slate-300">Tindakan ini akan memverifikasi bahwa santri telah tiba di pondok dan mencatat bukti kepulangan sah ke sistem.</p>
                </div>
            `,
            icon: 'success',
            confirmText: 'Ya, Verifikasi Kembali',
            cancelText: 'Batal'
        }).then(confirmed => {
            if (confirmed) {
                if (tokenInput) tokenInput.value = token;
                const checkinRadio = document.querySelector('input[name="action"][value="checkin"]');
                if (checkinRadio) checkinRadio.checked = true;
                if (form) form.submit();
            }
        });
    };

    updateControls();
});
</script>
@endpush
