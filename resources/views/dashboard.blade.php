@extends('layouts.app')

@section('title', Auth::user()->isSuperAdmin() ? 'Dashboard Super Admin - E-Absensi Santri' : '📋 Dashboard Musyrif - E-Absensi Santri')

@section('content')
<div class="space-y-6">

    {{-- =============================================
         HERO BANNER — Konten berbeda per Role
    ============================================= --}}
    @if(Auth::user()->isSuperAdmin())
        {{-- Super Admin: Command Center Banner --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-950 via-emerald-950 to-slate-950 text-white p-5 sm:p-6 shadow-xl border border-emerald-800/40">
            <!-- Background Decorative Glow -->
            <div class="absolute -right-16 -top-16 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-16 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Info Section -->
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-400/30 text-amber-300 shadow-md flex items-center justify-center font-bold text-2xl shrink-0">
                        👑
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-400/15 border border-amber-400/30 text-amber-300 text-[11px] font-bold tracking-wide">
                                <span>Super Admin</span>
                                <span class="text-amber-400/60">•</span>
                                <span>Command Center</span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-[11px] font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Sistem Online</span>
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                            Dashboard Pengelolaan E-Absensi
                        </h1>
                        <p class="text-xs text-emerald-200/80 mt-0.5 flex items-center gap-1.5">
                            <span>📅 {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                            <span class="text-slate-400">•</span>
                            <span>PP. Miftahul Huda Kroya</span>
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-2 lg:shrink-0 pt-2 lg:pt-0 border-t border-white/10 lg:border-t-0">
                    <a href="{{ route('santri.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-brand-950 font-bold text-xs shadow-md transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Santri</span>
                    </a>
                    <a href="{{ route('absensi.scan') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs shadow-sm transition-all active:scale-95">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Scan Presensi</span>
                    </a>
                    <a href="{{ route('rekap.absensi', ['tipe' => 'harian']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600/60 hover:bg-emerald-600/80 border border-emerald-500/40 text-emerald-100 font-semibold text-xs shadow-sm transition-all active:scale-95">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Rekap Harian</span>
                    </a>
                    <a href="{{ route('jadwal.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-slate-200 hover:text-white font-semibold text-xs shadow-sm transition-all active:scale-95">
                        <span>⚙️ Jam Sholat</span>
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Musyrif: Banner Fokus Presensi --}}
        <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-5 shadow-xl"
             style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
            <div class="absolute -right-12 -top-12 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center font-bold text-xl">
                        📋
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                <span>Musyrif — {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-300">PP. Miftahul Huda Kroya</span>
                        </div>
                        <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                            Dashboard Presensi Santri
                        </h1>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 md:shrink-0">
                    <a href="{{ route('absensi.scan') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-bold text-xs uppercase tracking-wider shadow active:scale-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Scanner QR</span>
                    </a>
                    <a href="{{ route('absensi.batch') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold text-xs tracking-wide active:scale-95 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span>Checklist Batch</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- =============================================
         SUPER ADMIN: Panel Statistik Manajemen
    ============================================= --}}
    @if(Auth::user()->isSuperAdmin())
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Santri Aktif --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">Aktif</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Santri Aktif</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $stats['total_santri'] }}</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ $stats['total_putra'] }} Putra · {{ $stats['total_putri'] }} Putri</p>
            </div>

            {{-- Kehadiran Hari Ini --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-950/60 flex items-center justify-center text-teal-600 dark:text-teal-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/40 px-2 py-0.5 rounded-full">Hari Ini</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kehadiran Hari Ini</p>
                <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['persentase_hadir'] }}%</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ $stats['hadir'] }} dari {{ $stats['total_presensi'] }} presensi</p>
            </div>

            {{-- Total Pengguna Sistem --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full">Sistem</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pengguna Sistem</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $totalUsers ?? 0 }}</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ $totalMusyrif ?? 0 }} Musyrif · {{ $totalKamtib ?? 0 }} Kamtib</p>
            </div>

            {{-- Santri Di Luar Pondok --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950/60 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/40 px-2 py-0.5 rounded-full">Di Luar</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri Di Luar Pondok</p>
                <p class="text-2xl font-extrabold text-orange-600 dark:text-orange-400 mt-1">{{ $stats['santri_diluar'] }}</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Izin aktif via Surat Digital</p>
            </div>
        </div>

        {{-- Super Admin: Panel Aksi Cepat --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <a href="{{ route('absensi.scan') }}" class="group bg-white dark:bg-slate-900 border border-emerald-200/70 dark:border-emerald-900/40 hover:border-emerald-400 hover:bg-emerald-50/50 dark:hover:bg-slate-800 rounded-2xl p-5 flex flex-col items-center text-center transition-all shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-white">Scan Presensi</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Shalat, KBM & Asrama</p>
            </a>
            <a href="{{ route('santri.index') }}" class="group bg-white dark:bg-slate-900 border border-blue-200/70 dark:border-blue-900/40 hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-slate-800 rounded-2xl p-5 flex flex-col items-center text-center transition-all shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-white">Data Santri & Kamar</p>
                <p class="text-[10px] text-slate-400 mt-0.5">CRUD & ID Card QR</p>
            </a>
            <a href="{{ route('perizinan.index') }}" class="group bg-white dark:bg-slate-900 border border-purple-200/70 dark:border-purple-900/40 hover:border-purple-400 hover:bg-purple-50/50 dark:hover:bg-slate-800 rounded-2xl p-5 flex flex-col items-center text-center transition-all shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-white">Kelola Perizinan</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Approve Izin Santri</p>
            </a>
            <a href="{{ route('kamtib.index') }}" class="group bg-white dark:bg-slate-900 border border-orange-200/70 dark:border-orange-900/40 hover:border-orange-400 hover:bg-orange-50/50 dark:hover:bg-slate-800 rounded-2xl p-5 flex flex-col items-center text-center transition-all shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-white">Pos Kamtib</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Gerbang Keluar/Masuk</p>
            </a>
            <a href="{{ route('jadwal.index') }}" class="group bg-white dark:bg-slate-900 border border-amber-200/70 dark:border-amber-900/40 hover:border-amber-400 hover:bg-amber-50/50 dark:hover:bg-slate-800 rounded-2xl p-5 flex flex-col items-center text-center transition-all shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-bold text-slate-800 dark:text-white">Jadwal Sholat</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Setting Jam & Toleransi</p>
            </a>
        </div>
    @else
        {{-- Musyrif: 4 Metrik Presensi --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">Hadir</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kehadiran Hari Ini</p>
                <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['persentase_hadir'] }}%</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ $stats['hadir'] }} dari {{ $stats['total_presensi'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 flex items-center justify-center text-rose-600 dark:text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded-full">Alpa</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Alpa</p>
                <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ $stats['alpa'] }}</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Tanpa keterangan valid</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-full">Izin</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Izin &amp; Sakit</p>
                <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['izin'] + $stats['sakit'] }}</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">{{ $stats['izin'] }} Izin · {{ $stats['sakit'] }} Sakit</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full">Di Luar</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri Di Luar</p>
                <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['santri_diluar'] }}</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Izin aktif pos kamtib</p>
            </div>
        </div>
    @endif

    {{-- =============================================
         GRID SESI + KOMPOSISI (Shared — semua role)
    ============================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sesi Absensi Terjadwal --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Sesi Absensi Terjadwal</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Jadwal shalat, KBM, pengajian & cek malam yang memerlukan presensi</p>
                </div>
                <div class="flex items-center space-x-2">
                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('jadwal.index') }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition-all flex items-center space-x-1">
                            <span>⚙️ Atur Jam</span>
                        </a>
                    @endif
                    <a href="{{ route('absensi.batch') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700">Checklist Cepat &rarr;</a>
                </div>
            </div>
            <div class="space-y-3">
                @forelse($sesiHariIni as $sesi)
                    <div class="p-4 rounded-2xl border border-slate-200/70 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all bg-slate-50/50 dark:bg-slate-800/40 hover:bg-emerald-50/30 dark:hover:bg-slate-800/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl {{ $sesi->status === 'dibuka' ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                {{ substr($sesi->jam_mulai, 0, 5) }}
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $sesi->nama_sesi }}</h3>
                                    @if($sesi->status === 'dibuka')
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 uppercase">Aktif</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 uppercase">{{ $sesi->status }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ ucfirst(str_replace('_', ' ', $sesi->jenis)) }} &bull; Batas Telat: {{ $sesi->batas_terlambat ? substr($sesi->batas_terlambat, 0, 5) : '-' }} WIB
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            <a href="{{ route('absensi.scan', ['sesi_id' => $sesi->id]) }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm active:scale-95 transition-all flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>Scan QR</span>
                            </a>
                            <a href="{{ route('absensi.batch', ['sesi_id' => $sesi->id]) }}" class="px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-all">
                                Batch
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-slate-400 dark:text-slate-500 text-xs">Belum ada sesi absensi yang aktif hari ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Komposisi Kehadiran --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white mb-1">Rasio Status Presensi</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 mb-4">Statistik akumulasi kehadiran santri hari ini</p>
                <div class="space-y-3.5">
                    @foreach([
                        ['label' => 'Hadir Tepat Waktu', 'key' => 'hadir', 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-400'],
                        ['label' => 'Terlambat', 'key' => 'terlambat', 'color' => 'bg-amber-500', 'text' => 'text-amber-700 dark:text-amber-400'],
                        ['label' => 'Izin (Surat Digital)', 'key' => 'izin', 'color' => 'bg-blue-500', 'text' => 'text-blue-700 dark:text-blue-400'],
                        ['label' => 'Sakit', 'key' => 'sakit', 'color' => 'bg-purple-500', 'text' => 'text-purple-700 dark:text-purple-400'],
                        ['label' => 'Alpa / Tanpa Keterangan', 'key' => 'alpa', 'color' => 'bg-rose-500', 'text' => 'text-rose-700 dark:text-rose-400'],
                    ] as $item)
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="{{ $item['text'] }} flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $item['color'] }}"></span>
                                    {{ $item['label'] }}
                                </span>
                                <span class="text-slate-800 dark:text-slate-200">{{ $stats[$item['key']] }}</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div class="{{ $item['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $stats['total_presensi'] > 0 ? ($stats[$item['key']] / $stats['total_presensi']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 dark:text-slate-500">
                <span>PWA Sync & Uptime: 99.5%</span>
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Online</span>
            </div>
        </div>
    </div>

    {{-- =============================================
         EWS + ACTIVITY FEED (Shared)
    ============================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- EWS Widget --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-rose-200/80 dark:border-rose-900/50 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-rose-100 dark:border-rose-900/40 mb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-300 flex items-center justify-center font-bold">⚠️</div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Early Warning System (EWS)</h2>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Santri dengan akumulasi ketidakhadiran (Alpa &ge; 2x)</p>
                    </div>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-extrabold uppercase tracking-wide">Peringatan</span>
            </div>
            <div class="space-y-3">
                @forelse($santriEws as $item)
                    <div class="p-3.5 rounded-xl border border-rose-100 dark:border-rose-900/40 bg-rose-50/40 dark:bg-rose-950/20 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-xs text-slate-900 dark:text-white">{{ $item->nama }}</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $item->asrama->nama_asrama ?? '-' }} &bull; {{ $item->kelas->nama_kelas ?? '-' }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded-md bg-rose-200 dark:bg-rose-900/60 text-rose-900 dark:text-rose-200 font-bold text-xs">
                                {{ $item->alpa_count }}x Alpa
                            </span>
                            @if($item->no_wa_wali)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->no_wa_wali)) }}" target="_blank" class="p-1.5 rounded-lg bg-emerald-600 text-white text-xs hover:bg-emerald-700" title="Hubungi Wali via WhatsApp">WA</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="font-medium text-emerald-700 dark:text-emerald-400 text-xs">Tidak ada santri berisiko saat ini.</p>
                        <p class="mt-0.5 text-slate-400 dark:text-slate-500 text-xs">Tingkat kedisiplinan santri dalam ambang aman.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Aktivitas Presensi Terkini --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 flex items-center justify-center font-bold">🕒</div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Aktivitas Presensi Terkini</h2>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Riwayat scan QR Code terbaru di pondok</p>
                    </div>
                </div>
                <a href="{{ route('absensi.scan') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">Scanner &rarr;</a>
            </div>
            <div class="space-y-2.5">
                @forelse($recentAbsensi as $log)
                    <div class="p-3 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <div class="flex items-center space-x-3">
                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 font-bold text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px]">
                                {{ substr($log->santri->nama ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 dark:text-slate-200">{{ $log->santri->nama ?? 'Santri' }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ $log->sesiAbsensi->nama_sesi ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $log->status === 'hadir' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300' : ($log->status === 'terlambat' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $log->waktu_absen ? $log->waktu_absen->format('H:i:s') : '-' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-slate-400 dark:text-slate-500 text-xs">Belum ada data presensi yang masuk hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- =============================================
         SUPER ADMIN ONLY: Log Aktivitas Kamtib Terbaru
    ============================================= --}}
    @if(Auth::user()->isSuperAdmin() && isset($recentKamtib) && $recentKamtib->isNotEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-orange-200/60 dark:border-orange-900/40 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-orange-100 dark:border-orange-900/40 mb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-300 flex items-center justify-center font-bold">🛡️</div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Log Pos Keamanan (Kamtib) Terbaru</h2>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Aktivitas keluar-masuk santri di gerbang pesantren</p>
                    </div>
                </div>
                <a href="{{ route('kamtib.index') }}" class="text-xs font-semibold text-orange-600 dark:text-orange-400 hover:underline">Buka Pos Kamtib &rarr;</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($recentKamtib as $log)
                    @php
                        $isCheckout = $log->tipe_log === 'checkout';
                        $isCheckin = $log->tipe_log === 'checkin';
                        $isLate = $log->perizinan && $log->perizinan->tanggal_selesai && $log->waktu_log && $log->waktu_log->greaterThan($log->perizinan->tanggal_selesai);
                    @endphp
                    <div class="p-3.5 rounded-xl border border-orange-100 dark:border-orange-900/40 bg-orange-50/30 dark:bg-orange-950/20 flex items-center justify-between text-xs">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">{{ $log->santri->nama ?? 'Santri' }}</p>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                                @if($isCheckout)
                                    <span class="text-amber-700 dark:text-amber-400 font-semibold">⬆ Keluar:</span> {{ $log->waktu_log ? $log->waktu_log->format('H:i') : '-' }} WIB
                                @elseif($isCheckin)
                                    <span class="text-emerald-700 dark:text-emerald-400 font-semibold">⬇ Kembali:</span> {{ $log->waktu_log ? $log->waktu_log->format('H:i') : '-' }} WIB
                                @else
                                    <span class="text-slate-700 dark:text-slate-300 font-semibold">⚠️ Pelanggaran</span>
                                @endif
                            </p>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $isLate ? 'bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-300' : ($isCheckout ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300' : 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300') }}">
                            {{ $isLate ? 'Terlambat' : ($isCheckout ? 'Di Luar' : 'Sesuai') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
