@extends('layouts.app')

@section('title', 'Dashboard Bendahara - E-Absensi Santri')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- Header Gradient --}}
    <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-5 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-xl">
                💰
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold">
                        <span>🏦 Dashboard Bendahara</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-300">PP. Miftahul Huda Kroya</span>
                </div>
                <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    Selamat Datang, Bendahara Pondok
                </h1>
            </div>
        </div>
    </div>

    {{-- 4 Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Kas Masuk Bulan Ini --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl">💵</div>
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">Bulan Ini</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kas Masuk</p>
            <p class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                Rp {{ number_format($kasmasuk ?? 0, 0, ',', '.') }}
            </p>
        </div>

        {{-- Total Tagihan Aktif (Jumlah Anak Nunggak) --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl">📋</div>
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-full">Aktif</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Tagihan Aktif</p>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                {{ number_format($totalTagihanAktif ?? 0) }}
            </p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Anak belum lunas</p>
        </div>

        {{-- Total Tunggakan --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/60 flex items-center justify-center text-rose-600 dark:text-rose-400 text-xl">🚨</div>
                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded-full">Nunggak</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Tunggakan</p>
            <p class="text-xl md:text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">
                Rp {{ number_format($totalTunggakan ?? 0, 0, ',', '.') }}
            </p>
        </div>

        {{-- Antrean Konfirmasi --}}
        <a href="{{ route('bendahara.verifikasi') }}"
           class="block bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 hover:border-amber-400/50 hover:shadow-md transition group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl group-hover:scale-105 transition">⏳</div>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 rounded-full">Menunggu</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Antrean Konfirmasi</p>
            <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">
                {{ number_format($antreanKonfirmasi ?? 0) }}
            </p>
            <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-1 group-hover:underline">Periksa bukti transfer →</p>
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Tabel Konfirmasi Transfer Terbaru --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">⏳ Konfirmasi Transfer Terbaru</h3>
                <a href="{{ route('bendahara.verifikasi') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Lihat semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Santri</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tagihan</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nominal</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                        @forelse($recentVerifikasi ?? [] as $item)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-white text-xs">{{ $item->santri->nama ?? '-' }}</p>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $item->santri->kelas->nama_kelas ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $item->judul_tagihan }}</p>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $item->updated_at?->diffForHumans() }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('bendahara.verifikasi') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition">
                                        Verifikasi →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500 text-sm">
                                    Tidak ada antrean konfirmasi transfer.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Link Cepat --}}
        <div class="space-y-3">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-4">🔗 Aksi Cepat</h3>
                <div class="space-y-2.5">
                    <a href="{{ route('bendahara.rekap') }}"
                       class="flex items-center space-x-3 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-950/60 border border-emerald-200/60 dark:border-emerald-900/40 transition group">
                        <span class="text-xl">📊</span>
                        <div>
                            <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Rekap Pembayaran</p>
                            <p class="text-[11px] text-emerald-600/70 dark:text-emerald-400/70">Lihat status bayar semua santri</p>
                        </div>
                    </a>
                    <a href="{{ route('bendahara.tagihan.index') }}"
                       class="flex items-center space-x-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-100 dark:hover:bg-blue-950/60 border border-blue-200/60 dark:border-blue-900/40 transition group">
                        <span class="text-xl">📋</span>
                        <div>
                            <p class="text-xs font-bold text-blue-800 dark:text-blue-300">Kelola Tagihan</p>
                            <p class="text-[11px] text-blue-600/70 dark:text-blue-400/70">Buat dan kelola tagihan santri</p>
                        </div>
                    </a>
                    <a href="{{ route('bendahara.verifikasi') }}"
                       class="flex items-center space-x-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-950/60 border border-amber-200/60 dark:border-amber-900/40 transition group">
                        <span class="text-xl">✅</span>
                        <div>
                            <p class="text-xs font-bold text-amber-800 dark:text-amber-300">Verifikasi Transfer</p>
                            @if(($antreanKonfirmasi ?? 0) > 0)
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold">{{ $antreanKonfirmasi }} menunggu konfirmasi</p>
                            @else
                                <p class="text-[11px] text-amber-600/70 dark:text-amber-400/70">Konfirmasi bukti transfer</p>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
