@extends('layouts.app')

@section('title', 'Modul Perizinan Santri - E-Absensi Santri')

@section('content')
<div class="space-y-6">
    {{-- Role-Aware Header --}}
    @auth
        @if(Auth::user()->isWali())
            {{-- Wali Santri: Portal Pengajuan Izin --}}
            <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-4 shadow-xl"
                 style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
                <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-xl">
                            👨‍👩‍👧
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold">
                                    <span>👨‍👩‍👧</span> Portal Wali Santri
                                </div>
                                <span class="text-[10px] font-bold text-emerald-200">PP. Miftahul Huda Kroya</span>
                            </div>
                            <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">Pengajuan &amp; Riwayat Izin Ananda</h1>
                        </div>
                    </div>
                    <button type="button" onclick="openModal('modal-ajukan-izin')"
                        class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-900 font-bold text-xs uppercase tracking-wider shadow active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>Ajukan Izin Baru</span>
                    </button>
                </div>
            </div>
        @else
            {{-- Musyrif / Super Admin: Approval Center --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-200 dark:border-blue-800 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            <span>Persetujuan & Manajemen Perizinan</span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">PP. Miftahul Huda Kroya</span>
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola permohonan surat izin santri, persetujuan pengasuhan/musyrif, dan pantau status santri.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openModal('modal-ajukan-izin')" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-all shadow-md shadow-blue-700/20 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>Buat Izin Baru</span>
                    </button>
                </div>
            </div>
        @endif
    @endauth


    <!-- Alert Success / Error Session -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium flex items-center justify-between shadow-sm">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm font-medium flex items-center justify-between shadow-sm">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:text-rose-900">&times;</button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Menunggu Approval</p>
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $stats['pending'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Izin Disetujui</p>
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ $stats['disetujui'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Sedang Berlangsung</p>
                <p class="text-xl font-bold text-purple-600 dark:text-purple-400 mt-0.5">{{ $stats['sedang_berlangsung'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Riwayat</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $stats['total'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="space-y-3">
        @if(!Auth::user()->isWali() && !isset($lockedGender))
            <div class="flex items-center space-x-2">
                <a href="{{ route('perizinan.index', request()->except('gender')) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('gender') ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                    Semua Santri
                </a>
                <a href="{{ route('perizinan.index', array_merge(request()->except('gender'), ['gender' => 'L'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('gender') === 'L' ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                    Santri Putra
                </a>
                <a href="{{ route('perizinan.index', array_merge(request()->except('gender'), ['gender' => 'P'])) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('gender') === 'P' ? 'bg-purple-600 text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                    Santri Putri
                </a>
            </div>
        @elseif(isset($lockedGender))
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300">
                <span>🔒 Database Terisolasi: Khusus Santri {{ $lockedGender === 'L' ? 'Putra' : 'Putri' }}</span>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('perizinan.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @if(request('gender'))
                    <input type="hidden" name="gender" value="{{ request('gender') }}">
                @endif
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Surat atau Nama Santri..." class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">-- Semua Status Izin --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending (Menunggu Persetujuan)</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="sedang_berlangsung" {{ request('status') == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang Berlangsung (Di Luar Pondok)</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai (Sudah Kembali)</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <select name="jenis_izin" onchange="this.form.submit()" class="w-full px-3.5 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">-- Semua Jenis Izin --</option>
                        <option value="pulang" {{ request('jenis_izin') == 'pulang' ? 'selected' : '' }}>Izin Pulang ke Rumah</option>
                        <option value="keluar_komplek" {{ request('jenis_izin') == 'keluar_komplek' ? 'selected' : '' }}>Keluar Komplek Sementara</option>
                        <option value="sakit" {{ request('jenis_izin') == 'sakit' ? 'selected' : '' }}>Izin Sakit</option>
                        <option value="sambangan_keluarga" {{ request('jenis_izin') == 'sambangan_keluarga' ? 'selected' : '' }}>Sambangan Keluarga</option>
                        <option value="tugas_pondok" {{ request('jenis_izin') == 'tugas_pondok' ? 'selected' : '' }}>Tugas Pondok</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 font-semibold">Surat & Santri</th>
                        <th class="py-3.5 px-4 font-semibold">Jenis & Keperluan</th>
                        <th class="py-3.5 px-4 font-semibold">Rentang Waktu Izin</th>
                        <th class="py-3.5 px-4 font-semibold">Penjemput</th>
                        <th class="py-3.5 px-4 font-semibold">Disetujui Oleh</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($perizinans as $izin)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $izin->santri->nama ?? 'Santri Dihapus' }}</p>
                                <div class="flex items-center space-x-2 text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    <span class="font-mono">{{ $izin->santri->nis ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $izin->santri->asrama->nama_asrama ?? '-' }}</span>
                                </div>
                                <span class="inline-block mt-1 text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    {{ $izin->nomor_surat }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 mb-1 border border-blue-200 dark:border-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $izin->jenis_izin)) }}
                                </span>
                                <p class="text-xs text-slate-700 dark:text-slate-300 line-clamp-2">{{ $izin->keperluan }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <p class="text-slate-800 dark:text-slate-200 font-medium">Mulai: {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->translatedFormat('d M Y, H:i') }}</p>
                                <p class="text-slate-500 dark:text-slate-400 mt-0.5">Batas: {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->translatedFormat('d M Y, H:i') }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600 dark:text-slate-400">
                                @if($izin->penjemput_nama)
                                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ $izin->penjemput_nama }}</p>
                                    <p class="text-slate-400 dark:text-slate-500 text-[11px]">{{ $izin->penjemput_hubungan ?? 'Keluarga' }}</p>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                @if($izin->nama_pemberi_izin || $izin->approvedBy)
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center shrink-0">
                                            <svg class="w-3 h-3 text-emerald-700 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 dark:text-slate-200 text-xs">{{ $izin->nama_pemberi_izin ?? $izin->approvedBy->name }}</p>
                                            @if($izin->nama_pemberi_izin && $izin->approvedBy && $izin->nama_pemberi_izin !== $izin->approvedBy->name)
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500">(Akun: {{ $izin->approvedBy->name }})</p>
                                            @endif
                                            @if($izin->updated_at && !in_array($izin->status, ['pending']))
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">
                                                    {{ \Carbon\Carbon::parse($izin->updated_at)->translatedFormat('d M Y, H:i') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @elseif($izin->status === 'pending')
                                    <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                        Belum diproses
                                    </span>
                                @elseif($izin->status === 'ditolak')
                                    <span class="text-rose-500 dark:text-rose-400">Ditolak</span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($izin->status === 'pending')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                        Menunggu Persetujuan
                                    </span>
                                @elseif($izin->status === 'disetujui')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        Disetujui
                                    </span>
                                @elseif($izin->status === 'sedang_berlangsung')
                                    <div class="space-y-1">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            Di Luar Komplek
                                        </span>
                                        <p class="text-[10px] font-semibold text-rose-600 dark:text-rose-400 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                            Wajib Scan Kembali
                                        </p>
                                    </div>
                                @elseif($izin->status === 'selesai')
                                    <div class="space-y-0.5">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            ✓ Kembali (Bukti Sah)
                                        </span>
                                        @if($izin->tanggal_kembali_aktual)
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                                                Scan: {{ \Carbon\Carbon::parse($izin->tanggal_kembali_aktual)->translatedFormat('d M H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                @elseif($izin->status === 'terlambat_kembali')
                                    <div class="space-y-0.5">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            ⚠️ Terlambat Kembali
                                        </span>
                                        @if($izin->tanggal_kembali_aktual)
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                                                Scan: {{ \Carbon\Carbon::parse($izin->tanggal_kembali_aktual)->translatedFormat('d M H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                @elseif($izin->status === 'ditolak')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Jika Pending: Tombol Approve & Reject (Hanya Super Admin & Musyrif) -->
                                    @if($izin->status === 'pending')
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isMusyrif())
                                            <button type="button" onclick="openApproveModal({{ $izin->id }}, '{{ addslashes($izin->santri->nama ?? '') }}')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-lg text-xs font-bold shadow-sm" title="Setujui Izin">
                                                Setujui
                                            </button>
                                            <button type="button" onclick="openRejectModal({{ $izin->id }}, '{{ addslashes($izin->santri->nama ?? '') }}')" class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 rounded-lg text-xs font-bold border border-rose-200 dark:border-rose-800" title="Tolak Izin">
                                                Tolak
                                            </button>
                                        @else
                                            <span class="px-2.5 py-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 rounded-lg border border-amber-200 dark:border-amber-800">
                                                Menunggu Approval
                                            </span>
                                        @endif
                                    @endif

                                    <!-- Tombol Surat Digital -->
                                    @if(in_array($izin->status, ['disetujui', 'sedang_berlangsung', 'selesai']))
                                        <a href="{{ route('perizinan.surat', $izin->id) }}" class="p-1.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 hover:bg-blue-100 rounded-lg text-xs font-bold flex items-center gap-1 border border-blue-200 dark:border-blue-800" title="Cetak Surat Izin Digital">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Surat QR</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                Belum ada riwayat perizinan santri yang terdata.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($perizinans->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                {{ $perizinans->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL AJUKAN IZIN SANTRI -->
<div id="modal-ajukan-izin" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 transition-colors duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Form Pengajuan Izin Santri</h3>
            <button type="button" onclick="closeModal('modal-ajukan-izin')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg font-bold">&times;</button>
        </div>

        <form method="POST" action="{{ route('perizinan.store') }}" class="mt-4 space-y-4">
            @csrf

            {{-- Auto-select / read-only santri untuk Wali Santri --}}
            @if(Auth::user()->isWali())
                @if($santris->count() === 1)
                    @php $myChild = $santris->first(); @endphp
                    <input type="hidden" name="santri_id" value="{{ $myChild->id }}">
                    <div class="p-3.5 bg-blue-50 dark:bg-blue-950/50 rounded-xl border border-blue-200 dark:border-blue-800/80">
                        <label class="block text-xs font-semibold text-blue-700 dark:text-blue-300 mb-1">Nama Ananda (Otomatis Sesuai Akun Wali Santri)</label>
                        <p class="text-base font-bold text-slate-900 dark:text-white">{{ $myChild->nama }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                            NIS: {{ $myChild->nis }} &bull; {{ $myChild->asrama->nama_asrama ?? '-' }} &bull; {{ $myChild->kelas->nama_kelas ?? '-' }}
                        </p>
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Ananda *</label>
                        <select name="santri_id" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            @foreach($santris as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} (NIS: {{ $s->nis }}) {{ isset($s->kelas->nama_kelas) ? '- ' . $s->kelas->nama_kelas : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @else
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Santri *</label>
                    <select name="santri_id" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">-- Pilih Santri --</option>
                        @foreach($santris as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }} (NIS: {{ $s->nis }}) {{ isset($s->kelas->nama_kelas) ? '- ' . $s->kelas->nama_kelas : '' }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Izin *</label>
                <select name="jenis_izin" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="pulang">Izin Pulang ke Rumah</option>
                    <option value="keluar_komplek">Keluar Komplek Pesantren Sementara</option>
                    <option value="sakit">Izin Sakit / Pengobatan</option>
                    <option value="sambangan_keluarga">Sambangan Keluarga</option>
                    <option value="tugas_pondok">Tugas Pondok / Organisasi</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Waktu Mulai Izin *</label>
                    <input type="datetime-local" name="tanggal_mulai" required value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Batas Waktu Kembali *</label>
                    <input type="datetime-local" name="tanggal_selesai" required value="{{ now()->addDays(2)->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keperluan / Alasan Izin *</label>
                <textarea name="keperluan" rows="3" required placeholder="Jelaskan secara rinci alasan perizinan..." class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Penjemput (Jika Ada)</label>
                    <input type="text" name="penjemput_nama" placeholder="Contoh: Bapak Ahmad" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Hubungan Penjemput</label>
                    <input type="text" name="penjemput_hubungan" placeholder="Ayah / Ibu / Paman" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modal-ajukan-izin')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md shadow-blue-700/20">Kirim Pengajuan Izin</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PERSETUJUAN IZIN SANTRI (INPUT MANUAL PEMBERI IZIN) -->
<div id="modal-approve" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 transition-colors duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Persetujuan Surat Izin</h3>
                <p id="approve-santri-text" class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modal-approve')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg font-bold">&times;</button>
        </div>

        <form id="form-approve" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Ustadz / Pengasuh yang Mengizinkan *</label>
                <input type="text" name="nama_pemberi_izin" id="approve-nama-pemberi" required value="{{ Auth::user()->name ?? 'Ustadz Pengasuhan' }}" placeholder="Ketik nama lengkap ustadz pemberi izin..." class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Nama ini dicantumkan secara resmi pada lembar Surat Izin Digital & Surat QR.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Catatan atau nasihat bagi santri saat izin..." class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modal-approve')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-emerald-700/20">Setujui & Terbitkan Izin</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TOLAK IZIN -->
<div id="modal-reject" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 transition-colors duration-200">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Tolak Pengajuan Izin</h3>
        <p id="reject-santri-text" class="text-xs text-slate-500 dark:text-slate-400 mb-4"></p>

        <form id="form-reject" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan *</label>
                <textarea name="catatan" rows="3" required placeholder="Sebutkan alasan penolakan izin santri ini..." class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modal-reject')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl">Tolak Izin</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openApproveModal(id, santriNama) {
        const approveRoute = "{{ route('perizinan.approve', ':id') }}";
        document.getElementById('form-approve').action = approveRoute.replace(':id', id);
        document.getElementById('approve-santri-text').innerText = 'Santri: ' + santriNama;
        openModal('modal-approve');
    }

    function openRejectModal(id, santriNama) {
        const rejectRoute = "{{ route('perizinan.reject', ':id') }}";
        document.getElementById('form-reject').action = rejectRoute.replace(':id', id);
        document.getElementById('reject-santri-text').innerText = 'Santri: ' + santriNama;
        openModal('modal-reject');
    }
</script>
@endpush
@endsection