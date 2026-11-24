@extends('layouts.app')

@section('title', ($tipe === 'harian' ? 'Rekapitulasi Perizinan Harian' : 'Rekapitulasi Perizinan Bulanan') . ' - E-Absensi Santri')

@section('content')
<div class="space-y-6">
    <!-- Header Section & Tab Navigasi -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-200 dark:border-blue-800 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Rekapitulasi Perizinan Santri</h1>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $tipe === 'harian' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-300 dark:border-blue-700' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-400 border border-indigo-300 dark:border-indigo-700' }}">
                        {{ $tipe === 'harian' ? 'Harian' : 'Bulanan' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $tipe === 'harian' ? 'Daftar santri izin keluar/pulang pada tanggal ' . ($tanggalFormatted ?? $tanggal) : 'Daftar laporan izin santri periode ' . ($bulanNama ?? '') . ' ' . ($tahun ?? '') }} — PP. Miftahul Huda Kroya.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Tab Switcher Harian & Bulanan -->
            <div class="inline-flex p-1 rounded-xl bg-slate-200/80 dark:bg-slate-800 border border-slate-300/50 dark:border-slate-700">
                <a href="{{ route('rekap.perizinan', ['tipe' => 'harian', 'tanggal' => $tanggal ?? today()->toDateString(), 'gender' => $gender ?? 'semua', 'status' => $status ?? 'semua', 'jenis_izin' => $jenisIzin ?? 'semua']) }}" class="px-3.5 py-1.5 rounded-lg text-xs {{ $tipe === 'harian' ? 'font-bold bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 shadow-sm' : 'font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition flex items-center gap-1.5">
                    <span>📋</span>
                    <span>Laporan Harian</span>
                </a>
                <a href="{{ route('rekap.perizinan', ['tipe' => 'bulanan', 'bulan' => $bulan ?? now()->month, 'tahun' => $tahun ?? now()->year, 'gender' => $gender ?? 'semua', 'status' => $status ?? 'semua', 'jenis_izin' => $jenisIzin ?? 'semua']) }}" class="px-3.5 py-1.5 rounded-lg text-xs {{ $tipe === 'bulanan' ? 'font-bold bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 shadow-sm' : 'font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition flex items-center gap-1.5">
                    <span>📝</span>
                    <span>Laporan Bulanan</span>
                </a>
            </div>

            <!-- Ekspor Excel -->
            <a href="{{ route('rekap.perizinan.excel', array_merge(request()->all(), ['tipe' => $tipe])) }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Excel ({{ $tipe === 'harian' ? 'Harian' : 'Bulanan' }})</span>
            </a>

            <!-- Cetak PDF -->
            <a href="{{ route('rekap.perizinan.pdf', array_merge(request()->all(), ['tipe' => $tipe])) }}" target="_blank" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak PDF ({{ $tipe === 'harian' ? 'Harian' : 'Bulanan' }})</span>
            </a>
        </div>
    </div>

    @if($tipe === 'harian')
        {{-- ========================================================
             BAGIAN 1: REKAPITULASI PERIZINAN HARIAN
        ======================================================== --}}
        <!-- Filter Form Harian -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('rekap.perizinan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <input type="hidden" name="tipe" value="harian">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kategori Santri</label>
                    <select name="gender" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="semua" {{ $gender == 'semua' ? 'selected' : '' }}>Semua (Putra & Putri)</option>
                        <option value="putra" {{ $gender == 'putra' ? 'selected' : '' }}>Santri Putra</option>
                        <option value="putri" {{ $gender == 'putri' ? 'selected' : '' }}>Santri Putri</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Status Izin</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="semua" {{ $status == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="sedang_berlangsung" {{ $status == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang di Luar Komplek</option>
                        <option value="terlambat_kembali" {{ $status == 'terlambat_kembali' ? 'selected' : '' }}>Terlambat Kembali</option>
                        <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui (Belum Keluar)</option>
                        <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai / Sudah Kembali</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Jenis Izin</label>
                    <select name="jenis_izin" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="semua" {{ $jenisIzin == 'semua' ? 'selected' : '' }}>Semua Jenis Izin</option>
                        <option value="pulang" {{ $jenisIzin == 'pulang' ? 'selected' : '' }}>Izin Pulang</option>
                        <option value="keluar_komplek" {{ $jenisIzin == 'keluar_komplek' ? 'selected' : '' }}>Keluar Komplek</option>
                        <option value="sakit" {{ $jenisIzin == 'sakit' ? 'selected' : '' }}>Izin Sakit</option>
                        <option value="sambangan_keluarga" {{ $jenisIzin == 'sambangan_keluarga' ? 'selected' : '' }}>Sambangan Keluarga</option>
                        <option value="tugas_pondok" {{ $jenisIzin == 'tugas_pondok' ? 'selected' : '' }}>Tugas Pondok</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Summary Stats Cards Harian -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Izin Hari Ini</p>
                <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $tanggalFormatted ?? $tanggal }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Di Luar Pondok</p>
                <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['sedang_berlangsung'] }}</p>
                <p class="text-[11px] text-amber-500 mt-0.5">Sedang aktif di luar</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Terlambat Kembali</p>
                <p class="text-xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ $stats['terlambat_kembali'] }}</p>
                <p class="text-[11px] text-rose-500 mt-0.5">Melewati batas</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Izin Pulang</p>
                <p class="text-xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['pulang'] }}</p>
                <p class="text-[11px] text-blue-500 mt-0.5">Kepulangan santri</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Keluar Singkat</p>
                <p class="text-xl font-extrabold text-teal-600 dark:text-teal-400 mt-1">{{ $stats['keluar_komplek'] }}</p>
                <p class="text-[11px] text-teal-500 mt-0.5">Keperluan ringkas</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sudah Kembali</p>
                <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['selesai'] }}</p>
                <p class="text-[11px] text-emerald-500 mt-0.5">Telah check-in</p>
            </div>
        </div>

        <!-- Data Table Rekap Izin Harian -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📝</span>
                    <span>Daftar Perizinan Santri Aktif ({{ count($perizinans) }} Catatan)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800 font-bold">
                        <tr>
                            <th class="px-3.5 py-3 text-center w-10">No</th>
                            <th class="px-3.5 py-3">No. Surat / Santri</th>
                            <th class="px-3 py-3">Asrama / Kelas</th>
                            <th class="px-3 py-3">Jenis Izin</th>
                            <th class="px-3 py-3">Keperluan</th>
                            <th class="px-3 py-3">Periode Izin</th>
                            <th class="px-3 py-3 text-center">Status</th>
                            <th class="px-3 py-3">Pemberi Izin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70 font-medium">
                        @forelse($perizinans as $idx => $izin)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="px-3.5 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-3.5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        @if($izin->santri && $izin->santri->foto_path)
                                            <img src="{{ Storage::url($izin->santri->foto_path) }}" alt="{{ $izin->santri->nama }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($izin->santri->nama ?? 'S', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $izin->santri->nama ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $izin->nomor_surat ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                    <div>{{ $izin->santri->asrama->nama_asrama ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">Kelas: {{ $izin->santri->kelas->nama_kelas ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ ucwords(str_replace('_', ' ', $izin->jenis_izin)) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $izin->keperluan }}">
                                    {{ $izin->keperluan }}
                                </td>
                                <td class="px-3 py-3 text-slate-500 font-mono text-[11px]">
                                    <div>Mulai: {{ $izin->tanggal_mulai ? $izin->tanggal_mulai->format('d/m/Y H:i') : '-' }}</div>
                                    <div>Batas: {{ $izin->tanggal_selesai ? $izin->tanggal_selesai->format('d/m/Y H:i') : '-' }}</div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($izin->status === 'sedang_berlangsung')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Di Luar
                                        </span>
                                    @elseif($izin->status === 'terlambat_kembali')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400">
                                            Terlambat
                                        </span>
                                    @elseif($izin->status === 'selesai')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                            Selesai
                                        </span>
                                    @elseif($izin->status === 'disetujui')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400">
                                            Disetujui
                                        </span>
                                    @elseif($izin->status === 'pending')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400">
                                            {{ ucfirst($izin->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-slate-500 text-[11px]">
                                    {{ $izin->approvedBy->name ?? ($izin->nama_pemberi_izin ?? '-') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                    Tidak ada santri yang memiliki perizinan aktif pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- ========================================================
             BAGIAN 2: REKAPITULASI PERIZINAN BULANAN
        ======================================================== --}}
        <!-- Filter Form Bulanan -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('rekap.perizinan') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <input type="hidden" name="tipe" value="bulanan">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Bulan</label>
                    <select name="bulan" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        @foreach($bulanNames as $num => $nama)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Tahun</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kategori Santri</label>
                    <select name="gender" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="semua" {{ $gender == 'semua' ? 'selected' : '' }}>Semua (Putra & Putri)</option>
                        <option value="putra" {{ $gender == 'putra' ? 'selected' : '' }}>Santri Putra</option>
                        <option value="putri" {{ $gender == 'putri' ? 'selected' : '' }}>Santri Putri</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Status Izin</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="semua" {{ $status == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                        <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="sedang_berlangsung" {{ $status == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                        <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai / Kembali</option>
                        <option value="terlambat_kembali" {{ $status == 'terlambat_kembali' ? 'selected' : '' }}>Terlambat Kembali</option>
                        <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Jenis Izin</label>
                    <select name="jenis_izin" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="semua" {{ $jenisIzin == 'semua' ? 'selected' : '' }}>Semua Jenis</option>
                        <option value="pulang" {{ $jenisIzin == 'pulang' ? 'selected' : '' }}>Izin Pulang</option>
                        <option value="keluar_komplek" {{ $jenisIzin == 'keluar_komplek' ? 'selected' : '' }}>Keluar Komplek</option>
                        <option value="sakit" {{ $jenisIzin == 'sakit' ? 'selected' : '' }}>Izin Sakit</option>
                        <option value="sambangan_keluarga" {{ $jenisIzin == 'sambangan_keluarga' ? 'selected' : '' }}>Sambangan</option>
                        <option value="tugas_pondok" {{ $jenisIzin == 'tugas_pondok' ? 'selected' : '' }}>Tugas Pondok</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Summary Stats Cards Bulanan -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Pengajuan</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $stats['total'] }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Bulan {{ $bulanNama ?? '' }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Sedang Berlangsung</p>
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $stats['sedang_berlangsung'] }}</p>
                <p class="text-[11px] text-amber-500 mt-1">Di luar komplek</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Selesai / Kembali</p>
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $stats['selesai'] }}</p>
                <p class="text-[11px] text-emerald-500 mt-1">Telah check-in</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Terlambat Kembali</p>
                <p class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-0.5">{{ $stats['terlambat_kembali'] }}</p>
                <p class="text-[11px] text-rose-500 mt-1">Melanggar batas</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Menunggu (Pending)</p>
                <p class="text-xl font-bold text-slate-600 dark:text-slate-300 mt-0.5">{{ $stats['pending'] }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Belum disetujui</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Ditolak</p>
                <p class="text-xl font-bold text-rose-700 dark:text-rose-500 mt-0.5">{{ $stats['ditolak'] }}</p>
                <p class="text-[11px] text-rose-400 mt-1">Tidak disetujui</p>
            </div>
        </div>

        <!-- Data Table Rekap Izin Bulanan -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📝</span>
                    <span>Tabel Rekapitulasi Izin Santri Bulanan ({{ count($perizinans) }} Catatan)</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800 font-bold">
                        <tr>
                            <th class="px-3.5 py-3 text-center w-10">No</th>
                            <th class="px-3.5 py-3">No. Surat / Santri</th>
                            <th class="px-3 py-3">Asrama / Kelas</th>
                            <th class="px-3 py-3">Jenis Izin</th>
                            <th class="px-3 py-3">Keperluan</th>
                            <th class="px-3 py-3">Periode Izin</th>
                            <th class="px-3 py-3 text-center">Status</th>
                            <th class="px-3 py-3">Pemberi Izin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70 font-medium">
                        @forelse($perizinans as $idx => $izin)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="px-3.5 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-3.5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        @if($izin->santri && $izin->santri->foto_path)
                                            <img src="{{ Storage::url($izin->santri->foto_path) }}" alt="{{ $izin->santri->nama }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 font-bold text-xs flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($izin->santri->nama ?? 'S', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $izin->santri->nama ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $izin->nomor_surat ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                    <div>{{ $izin->santri->asrama->nama_asrama ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400">Kelas: {{ $izin->santri->kelas->nama_kelas ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ ucwords(str_replace('_', ' ', $izin->jenis_izin)) }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $izin->keperluan }}">
                                    {{ $izin->keperluan }}
                                </td>
                                <td class="px-3 py-3 text-slate-500 font-mono text-[11px]">
                                    <div>Mulai: {{ $izin->tanggal_mulai ? $izin->tanggal_mulai->format('d/m/Y H:i') : '-' }}</div>
                                    <div>Batas: {{ $izin->tanggal_selesai ? $izin->tanggal_selesai->format('d/m/Y H:i') : '-' }}</div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($izin->status === 'sedang_berlangsung')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Di Luar
                                        </span>
                                    @elseif($izin->status === 'terlambat_kembali')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400">
                                            Terlambat
                                        </span>
                                    @elseif($izin->status === 'selesai')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                            Selesai
                                        </span>
                                    @elseif($izin->status === 'disetujui')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400">
                                            Disetujui
                                        </span>
                                    @elseif($izin->status === 'pending')
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400">
                                            {{ ucfirst($izin->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-slate-500 text-[11px]">
                                    {{ $izin->approvedBy->name ?? ($izin->nama_pemberi_izin ?? '-') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                    Tidak ada data perizinan untuk filter bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
