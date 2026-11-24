@extends('layouts.app')

@section('title', 'Kuitansi Pembayaran - E-Absensi Santri')

@push('styles')
<style>
    @media print {
        body { background: white !important; }
        .print-container { max-width: 100% !important; }
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto space-y-4 print-container">

    {{-- Tombol Cetak (tidak tampil saat print) --}}
    <div class="no-print flex items-center justify-between">
        <a href="{{ url()->previous() }}" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">
            ← Kembali
        </a>
        <button onclick="window.print()"
                class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow no-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Kuitansi</span>
        </button>
    </div>

    {{-- Kuitansi Card --}}
    <div class="bg-white print:shadow-none rounded-2xl border border-slate-200 print:border-0 shadow-lg overflow-hidden relative">

        {{-- Stempel LUNAS --}}
        <div class="absolute top-6 right-6 print:top-4 print:right-4 rotate-12 z-10">
            <div class="border-4 border-emerald-500 rounded-xl px-4 py-2 opacity-80">
                <span class="text-2xl font-black text-emerald-600 tracking-widest uppercase">LUNAS</span>
            </div>
        </div>

        {{-- Header Kuitansi --}}
        <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 text-white p-6 print:p-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-white/10 p-1 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="text-sm font-extrabold uppercase tracking-wider text-emerald-100">Pondok Pesantren Miftahul Huda Kroya</h2>
                    <p class="text-xs text-emerald-200 mt-0.5">Jl. Pesantren, Kroya, Cilacap, Jawa Tengah</p>
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider">
                        🧾 KUITANSI PEMBAYARAN
                    </div>
                </div>
            </div>
        </div>

        {{-- Body Kuitansi --}}
        <div class="p-6 print:p-4">
            {{-- Nomor & Tanggal --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6 pb-4 border-b border-slate-100">
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Nomor Kuitansi</p>
                    <p class="text-sm font-extrabold font-mono text-slate-900">{{ $tagihan->kode_tagihan }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tanggal Lunas</p>
                    <p class="text-sm font-bold text-slate-900">
                        {{ $tagihan->dikonfirmasi_pada ? \Carbon\Carbon::parse($tagihan->dikonfirmasi_pada)->translatedFormat('d F Y') : ($tagihan->tanggal_bayar ? \Carbon\Carbon::parse($tagihan->tanggal_bayar)->translatedFormat('d F Y') : '-') }}
                    </p>
                </div>
            </div>

            {{-- Detail Pembayaran --}}
            <div class="space-y-3.5">
                <div class="flex items-start justify-between">
                    <span class="text-xs text-slate-500 font-semibold w-36 shrink-0">Nama Santri</span>
                    <span class="text-sm font-bold text-slate-900 text-right">{{ $tagihan->santri->nama ?? '-' }}</span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-xs text-slate-500 font-semibold w-36 shrink-0">NIS</span>
                    <span class="text-sm font-mono text-slate-800 text-right">{{ $tagihan->santri->nis ?? '-' }}</span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-xs text-slate-500 font-semibold w-36 shrink-0">Kelas / Asrama</span>
                    <span class="text-sm text-slate-800 text-right">
                        {{ $tagihan->santri->kelas->nama_kelas ?? '-' }} / {{ $tagihan->santri->asrama->nama_asrama ?? '-' }}
                    </span>
                </div>

                <div class="border-t border-slate-100 pt-3.5 mt-3.5">
                    <div class="flex items-start justify-between">
                        <span class="text-xs text-slate-500 font-semibold w-36 shrink-0">Judul Tagihan</span>
                        <span class="text-sm font-bold text-slate-900 text-right">{{ $tagihan->judul_tagihan }}</span>
                    </div>
                    <div class="flex items-start justify-between mt-3">
                        <span class="text-xs text-slate-500 font-semibold w-36 shrink-0">Kategori</span>
                        <span class="text-sm text-slate-800 capitalize text-right">{{ str_replace('_', ' ', $tagihan->kategori) }}</span>
                    </div>
                </div>

                <div class="bg-emerald-50 print:bg-transparent border border-emerald-200 print:border-slate-200 rounded-xl p-4 mt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-emerald-800">JUMLAH DIBAYAR</span>
                        <span class="text-xl font-extrabold text-emerald-700">Rp {{ number_format($tagihan->nominal_dibayar ?: $tagihan->nominal, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if(!empty($tagihan->riwayat_cicilan))
                    <div class="border-t border-slate-100 pt-3 mt-2">
                        <p class="text-xs text-slate-500 font-bold mb-2">Riwayat Pembayaran Bertahap / Cicilan:</p>
                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 text-xs space-y-1.5">
                            @foreach($tagihan->riwayat_cicilan as $i => $c)
                                <div class="flex items-center justify-between text-slate-700">
                                    <span>Cicilan ke-{{ $i + 1 }} ({{ \Carbon\Carbon::parse($c['tanggal'])->format('d M Y, H:i') }}) • {{ strtoupper($c['metode'] ?? '-') }}</span>
                                    <span class="font-bold">Rp {{ number_format($c['nominal'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex items-start justify-between">
                    <span class="text-xs text-slate-500 font-semibold w-36 shrink-0">Metode Pembayaran</span>
                    <span class="text-sm font-semibold text-slate-800 text-right uppercase">{{ $tagihan->metode_pembayaran ?? 'Tunai' }}</span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-xs text-slate-500 font-semibold w-36 shrink-0">Dikonfirmasi Oleh</span>
                    <span class="text-sm font-semibold text-slate-800 text-right">{{ $tagihan->dikonfirmasiOleh->name ?? 'Bendahara' }}</span>
                </div>
            </div>

            {{-- Footer Kuitansi --}}
            <div class="mt-8 pt-4 border-t border-dashed border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center">
                    <p class="text-xs text-slate-500 mb-6">Penerima Pembayaran,</p>
                    <p class="text-xs font-bold text-slate-900 border-t border-slate-300 pt-1 px-6">
                        {{ $tagihan->dikonfirmasiOleh->name ?? 'Bendahara' }}
                    </p>
                    <p class="text-[11px] text-slate-400">Bendahara PP. Miftahul Huda Kroya</p>
                </div>
                <div class="text-center">
                    <p class="text-[11px] text-slate-400 mt-2">Kuitansi dicetak oleh Sistem E-Absensi Santri</p>
                    <p class="text-[11px] text-slate-400">PP. Miftahul Huda Kroya · {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
