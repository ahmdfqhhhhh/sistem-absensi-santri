@extends('layouts.app')

@section('title', 'Portal Wali Santri - E-Absensi Santri')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-5 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-xl">
                👨‍👩‍👧
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold">
                        <span>🏠 Portal Wali Santri</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-300">PP. Miftahul Huda Kroya</span>
                </div>
                <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    Selamat Datang, {{ Auth::user()->name }}
                </h1>
            </div>
        </div>
    </div>

    @if($santri)
        {{-- Profil Santri --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center space-x-4">
                @if($santri->foto_path)
                    <img src="{{ Storage::url($santri->foto_path) }}" alt="{{ $santri->nama }}"
                         class="w-16 h-16 rounded-2xl object-cover border-2 border-emerald-200 dark:border-emerald-800">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-700 dark:text-emerald-400 text-2xl font-bold border-2 border-emerald-200 dark:border-emerald-800">
                        {{ strtoupper(substr($santri->nama, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $santri->nama }}</h2>
                    <p class="text-xs font-mono text-slate-500 dark:text-slate-400">NIS: {{ $santri->nis ?? '-' }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            {{ $santri->asrama->nama_asrama ?? '-' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            {{ $santri->kelas->nama_kelas ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik Kehadiran --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach([
                ['label' => 'Hadir',     'value' => $stats['hadir'],     'color' => 'emerald'],
                ['label' => 'Terlambat', 'value' => $stats['terlambat'], 'color' => 'amber'],
                ['label' => 'Izin',      'value' => $stats['izin'],      'color' => 'blue'],
                ['label' => 'Sakit',     'value' => $stats['sakit'],     'color' => 'purple'],
                ['label' => 'Alpa',      'value' => $stats['alpa'],      'color' => 'rose'],
                ['label' => 'Total',     'value' => $stats['total'],     'color' => 'slate'],
            ] as $stat)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-4 text-center">
                <p class="text-2xl font-extrabold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">{{ $stat['value'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $stat['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Section Tagihan & Pembayaran Keuangan --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl text-white p-5 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl shrink-0">
                    💳
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Administrasi & Pembayaran Santri</h3>
                    <p class="text-xs text-emerald-100">
                        Bayar SPP, syahriah bulanan, dan administrasi pesantren dengan mudah & aman via transfer / QRIS.
                    </p>
                </div>
            </div>
            <a href="{{ route('wali.pembayaran') }}"
               class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-white text-emerald-800 font-extrabold text-xs shadow hover:bg-emerald-50 transition active:scale-95 shrink-0">
                <span>Buka Pembayaran</span>
                <span>→</span>
            </a>
        </div>

        {{-- Riwayat Absensi --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Absensi Terbaru</h3>
            </div>
            @forelse($riwayatAbsensi as $absensi)
                <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-800/60 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
                            {{ $absensi->sesiAbsensi->nama_sesi ?? 'Sesi' }}
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $absensi->created_at?->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $absensi->status === 'hadir'     ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : '' }}
                        {{ $absensi->status === 'terlambat' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300'       : '' }}
                        {{ $absensi->status === 'izin'      ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300'           : '' }}
                        {{ $absensi->status === 'sakit'     ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300'   : '' }}
                        {{ $absensi->status === 'alpa'      ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300'           : '' }}
                    ">
                        {{ ucfirst($absensi->status) }}
                    </span>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-slate-400 dark:text-slate-500 text-sm">
                    Belum ada data absensi.
                </div>
            @endforelse
        </div>

        {{-- Riwayat Izin --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Perizinan</h3>
                <a href="{{ route('perizinan.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Lihat semua →
                </a>
            </div>
            @forelse($riwayatIzin as $izin)
                <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-800/60 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
                            {{ ucfirst(str_replace('_', ' ', $izin->jenis_izin)) }}
                        </p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $izin->tanggal_mulai?->format('d M Y') }} – {{ $izin->tanggal_selesai?->format('d M Y') }}
                        </p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $izin->status === 'disetujui'         ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300'  : '' }}
                        {{ $izin->status === 'pending'           ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300'          : '' }}
                        {{ $izin->status === 'ditolak'          ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300'              : '' }}
                        {{ $izin->status === 'sedang_berlangsung'? 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300'              : '' }}
                        {{ $izin->status === 'selesai'          ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'             : '' }}
                    ">
                        {{ ucfirst(str_replace('_', ' ', $izin->status)) }}
                    </span>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-slate-400 dark:text-slate-500 text-sm">
                    Belum ada data perizinan.
                </div>
            @endforelse
        </div>

        {{-- Ajukan Izin --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Ajukan Perizinan Baru</h3>
            <a href="{{ route('perizinan.index') }}"
               class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow transition-all active:scale-95">
                <span>➕</span>
                <span>Buka Halaman Perizinan</span>
            </a>
        </div>

    @else
        {{-- Santri tidak ditemukan --}}
        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 p-8 rounded-2xl text-center">
            <div class="text-4xl mb-3">🔍</div>
            <h3 class="text-base font-bold text-amber-800 dark:text-amber-200">Data Santri Belum Terhubung</h3>
            <p class="text-sm text-amber-700 dark:text-amber-300 mt-2">
                Akun Anda belum terhubung dengan data santri. Pastikan nomor WhatsApp yang Anda daftarkan sesuai dengan
                nomor yang tercatat pada data santri. Hubungi pengurus pondok untuk bantuan lebih lanjut.
            </p>
        </div>
    @endif

</div>
@endsection
