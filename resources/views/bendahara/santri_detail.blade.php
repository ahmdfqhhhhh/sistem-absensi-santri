@extends('layouts.app')

@section('title', 'Detail Santri - Bendahara')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ bayarModal: false, selectedTagihanId: null, selectedTagihanJudul: '' }">

    {{-- Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('bendahara.rekap') }}" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">
            ← Kembali ke Rekap
        </a>
    </div>

    {{-- Info Santri --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-start justify-between">
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
                    <div class="flex items-center space-x-2 mt-1.5">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            📚 {{ $santri->kelas->nama_kelas ?? '-' }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                            🏠 {{ $santri->asrama->nama_asrama ?? '-' }}
                        </span>
                    </div>
                    @if($santri->waliSantri)
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Wali: {{ $santri->waliSantri->name }} · {{ $santri->waliSantri->no_wa ?? '-' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Keuangan --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 text-center">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Tagihan</p>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">Rp {{ number_format($summaryNominal ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 text-center">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sudah Dibayar</p>
            <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($summaryDibayar ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 text-center">
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sisa Tunggakan</p>
            <p class="text-xl font-extrabold {{ ($summaryNunggak ?? 0) > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400 dark:text-slate-500' }} mt-1">
                Rp {{ number_format($summaryNunggak ?? 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Tabel Riwayat Tagihan --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">📋 Riwayat Tagihan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Judul Tagihan</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nominal</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    @forelse($tagihans ?? [] as $tagihan)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3">
                                <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400">{{ $tagihan->kode_tagihan }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-xs font-semibold text-slate-900 dark:text-white">{{ $tagihan->judul_tagihan }}</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 capitalize">{{ str_replace('_', ' ', $tagihan->kategori) }}</p>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-xs font-bold text-slate-900 dark:text-white">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs text-slate-600 dark:text-slate-400">{{ $tagihan->jatuh_tempo?->format('d M Y') ?? '-' }}</span>
                                @if($tagihan->status === 'nunggak' || ($tagihan->jatuh_tempo && $tagihan->jatuh_tempo->isPast() && $tagihan->status !== 'lunas'))
                                    <span class="block text-[11px] text-rose-500 dark:text-rose-400 font-semibold">Terlambat!</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($tagihan->status === 'lunas')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">✅ Lunas</span>
                                @elseif($tagihan->status === 'menunggu_konfirmasi')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">⏳ Menunggu</span>
                                @elseif($tagihan->status === 'nunggak')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300">🚨 Nunggak</span>
                                @elseif($tagihan->status === 'dibatalkan')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">🚫 Dibatalkan</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">📋 Belum Bayar</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($tagihan->bukti_transfer_path)
                                        <a href="{{ Storage::url($tagihan->bukti_transfer_path) }}" target="_blank"
                                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition shadow-sm" title="Buka Bukti Transfer">
                                            📎 Bukti
                                        </a>
                                    @endif

                                    @if($tagihan->status === 'lunas')
                                        <a href="{{ route('bendahara.kuitansi', $tagihan->id) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                                            🧾 Kuitansi
                                        </a>
                                    @elseif(in_array($tagihan->status, ['belum_bayar', 'nunggak']))
                                        <button type="button"
                                            @click="bayarModal = true; selectedTagihanId = {{ $tagihan->id }}; selectedTagihanJudul = '{{ addslashes($tagihan->judul_tagihan) }}'"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                            💵 Catat Tunai
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                Belum ada tagihan untuk santri ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Catat Bayar Tunai --}}
    <div x-show="bayarModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="bayarModal = false">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">💵 Catat Pembayaran Tunai</h3>
                <button @click="bayarModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition text-lg leading-none">✕</button>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Tagihan: <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="selectedTagihanJudul"></span></p>
            <form :action="`/bendahara/tagihan/${selectedTagihanId}/tunai`" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nominal Dibayar (Rp)</label>
                        <input type="number" name="nominal_dibayar" required min="1"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Catatan (opsional)</label>
                        <textarea name="catatan_bendahara" rows="2"
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none resize-none"
                                  placeholder="Keterangan pembayaran tunai..."></textarea>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="bayarModal = false"
                                class="flex-1 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                            ✅ Konfirmasi Lunas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
