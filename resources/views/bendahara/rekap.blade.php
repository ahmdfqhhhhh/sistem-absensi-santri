@extends('layouts.app')

@section('title', 'Rekap Pembayaran - Bendahara')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl text-white p-6 md:p-8 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-8 -bottom-8 w-60 h-60 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-2xl">
                        📊
                    </div>
                    <div>
                        <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[11px] font-semibold">
                            <span>🏦 Bendahara Pondok</span>
                        </div>
                        <p class="text-xs font-bold text-emerald-200 mt-0.5">Pondok Pesantren Miftahul Huda Kroya</p>
                    </div>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Rekapitulasi Pembayaran Santri
                </h1>
                <p class="mt-2 text-sm text-emerald-200/90 max-w-2xl">
                    Pantau status pelunasan, sisa tagihan, dan rincian keuangan seluruh santri pondok secara terpusat.
                </p>
            </div>
            <a href="{{ route('bendahara.index') }}" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold text-xs tracking-wide transition active:scale-95">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri Menunggak (Lewat Tempo)</p>
            <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ number_format($totalSantriNunggak ?? 0) }} <span class="text-sm font-normal text-slate-400">santri</span></p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Tunggakan Lewat Tempo</p>
            <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">Rp {{ number_format($grandTotalTunggakan ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Santri Belum Lunas (Aktif)</p>
            <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalBelumLunas ?? 0) }} <span class="text-sm font-normal text-slate-400">santri</span></p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
        <form method="GET" action="{{ route('bendahara.rekap') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Cari Santri</label>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Nama atau NIS..."
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Filter Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas ?? $kelasList ?? [] as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Filter Asrama</label>
                <select name="asrama_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option value="">Semua Asrama</option>
                    @foreach($asramas ?? $asramaList ?? [] as $a)
                        <option value="{{ $a->id }}" {{ request('asrama_id') == $a->id ? 'selected' : '' }}>{{ $a->nama_asrama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Status Pembayaran</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    <option value="">Semua Status</option>
                    <option value="nunggak" {{ request('status') === 'nunggak' ? 'selected' : '' }}>🚨 Hanya Nunggak (Lewat Tempo)</option>
                    <option value="belum_bayar" {{ request('status') === 'belum_bayar' ? 'selected' : '' }}>⏳ Belum Lunas (Aktif)</option>
                    <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>✅ Lunas</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition text-center">
                    🔍 Filter
                </button>
                <a href="{{ route('bendahara.rekap') }}" class="px-3 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-xs font-semibold transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Santri --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Santri</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas / Asrama</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tagihan</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Dibayar</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sisa Tagihan</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    @forelse($santris ?? [] as $i => $santri)
                        @php
                            $totalTagihan = $santri->total_tagihan ?? 0;
                            $totalDibayar = $santri->total_dibayar ?? 0;
                            $sisaTagihan = $santri->sisa_tagihan ?? max(0, $totalTagihan - $totalDibayar);
                            $sisaNunggak = $santri->sisa_nunggak ?? 0;
                            $statusKeuangan = $santri->status_keuangan ?? ($sisaTagihan === 0 ? 'lunas' : ($sisaNunggak > 0 ? 'nunggak' : 'belum_bayar'));
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3 text-xs text-slate-400 dark:text-slate-500">{{ $santris->firstItem() + $i }}</td>
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-900 dark:text-white text-xs">{{ $santri->nama }}</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">{{ $santri->nis ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-xs text-slate-700 dark:text-slate-300">{{ $santri->kelas->nama_kelas ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $santri->asrama->nama_asrama ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-xs font-semibold text-slate-900 dark:text-white">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-xs font-bold {{ $sisaTagihan > 0 ? ($sisaNunggak > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') : 'text-slate-400 dark:text-slate-500' }}">
                                    Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                </span>
                                @if($sisaNunggak > 0 && $sisaNunggak < $sisaTagihan)
                                    <span class="block text-[10px] text-rose-500 font-medium">Nunggak: Rp {{ number_format($sisaNunggak, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($statusKeuangan === 'lunas')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">✅ Lunas</span>
                                @elseif($statusKeuangan === 'nunggak')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300">🚨 Nunggak</span>
                                @elseif($statusKeuangan === 'belum_bayar')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">⏳ Belum Bayar</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">⚪ Bebas Biaya</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a href="{{ route('bendahara.santri.detail', $santri->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                Tidak ada data santri ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($santris) && $santris->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $santris->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
