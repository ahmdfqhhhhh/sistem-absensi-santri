@extends('layouts.app')

@section('title', 'Verifikasi Transfer - Bendahara')

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
                        💳
                    </div>
                    <div>
                        <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[11px] font-semibold">
                            <span>🏦 Bendahara Keuangan</span>
                        </div>
                        <p class="text-xs font-bold text-emerald-200 mt-0.5">Pondok Pesantren Miftahul Huda Kroya</p>
                    </div>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    <span>Antrean Verifikasi Transfer</span>
                    @if(count($tagihans ?? []) > 0)
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-amber-400 text-slate-900">
                            {{ count($tagihans) }} Menunggu
                        </span>
                    @endif
                </h1>
                <p class="mt-2 text-sm text-emerald-200/90 max-w-2xl">
                    Periksa bukti transfer dari wali santri, konfirmasi pembayaran lunas, atau tolak dengan catatan jelas.
                </p>
            </div>
            <a href="{{ route('bendahara.index') }}" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold text-xs tracking-wide transition active:scale-95">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Daftar Item Verifikasi --}}
    @forelse($tagihans ?? [] as $tagihan)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden"
             x-data="{ tolakModal: false, previewModal: false }">
            <div class="p-5">
                <div class="flex flex-col md:flex-row gap-5">
                    {{-- Preview Bukti Transfer --}}
                    <div class="shrink-0">
                        @if($tagihan->bukti_transfer_path)
                            @php
                                $isPdf = str_ends_with(strtolower($tagihan->bukti_transfer_path), '.pdf');
                                $fileUrl = Storage::url($tagihan->bukti_transfer_path);
                            @endphp
                            <div class="cursor-pointer group" @click="previewModal = true">
                                @if($isPdf)
                                    <div class="w-32 h-40 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 flex flex-col items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-105 transition shadow-sm p-2 text-center">
                                        <span class="text-4xl mb-1">📄</span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider">Dokumen PDF</span>
                                    </div>
                                @else
                                    <div class="w-32 h-40 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 relative group-hover:scale-105 transition shadow-sm">
                                        <img src="{{ $fileUrl }}"
                                             alt="Bukti Transfer"
                                             class="w-full h-full object-cover"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.classList.add('hidden'); this.parentElement.querySelector('.fallback-err').classList.remove('hidden');">
                                        <div class="fallback-err hidden absolute inset-0 flex flex-col items-center justify-center p-2 text-center text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-slate-800">
                                            <span class="text-2xl">⚠️</span>
                                            <span class="text-[10px] mt-1 font-semibold">Gagal memuat</span>
                                        </div>
                                    </div>
                                @endif
                                <p class="text-[11px] text-center text-emerald-600 dark:text-emerald-400 mt-1.5 font-semibold group-hover:underline flex items-center justify-center gap-1">
                                    <span>🔍</span>
                                    <span>Lihat Bukti</span>
                                </p>
                            </div>

                            {{-- Modal Preview Bukti Transfer (Lightbox) --}}
                            <div x-show="previewModal" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
                                 @click.self="previewModal = false"
                                 @keydown.escape.window="previewModal = false">
                                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                                    <div class="flex items-center justify-between p-4 border-b border-slate-100 dark:border-slate-800">
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Bukti Transfer - {{ $tagihan->santri->nama ?? '-' }}</h3>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $tagihan->judul_tagihan }} • Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ $fileUrl }}" target="_blank"
                                               class="px-2.5 py-1 text-xs rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold transition">
                                                ↗ Tab Baru
                                            </a>
                                            <button @click="previewModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold px-2 py-1 leading-none">✕</button>
                                        </div>
                                    </div>
                                    <div class="p-4 overflow-auto flex-1 flex items-center justify-center bg-slate-950/5 dark:bg-black/40 min-h-[300px]">
                                        @if($isPdf)
                                            <iframe src="{{ $fileUrl }}" class="w-full h-[550px] rounded-lg border-0"></iframe>
                                        @else
                                            <img src="{{ $fileUrl }}" alt="Bukti Transfer Penuh" class="max-w-full max-h-[70vh] rounded-lg shadow object-contain">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="w-32 h-40 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                <span class="text-3xl">📎</span>
                                <span class="text-[11px] mt-1">Tidak ada bukti</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ $tagihan->santri->nama ?? '-' }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $tagihan->santri->kelas->nama_kelas ?? '-' }} · {{ $tagihan->santri->asrama->nama_asrama ?? '-' }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 shrink-0">
                                ⏳ Menunggu Konfirmasi
                            </span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                            <div>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Tagihan</p>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $tagihan->judul_tagihan }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Total Tagihan</p>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                                @if($tagihan->nominal_dibayar > 0)
                                    <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">Sudah cicil: Rp {{ number_format($tagihan->nominal_dibayar, 0, ',', '.') }}</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Nominal Ditransfer</p>
                                @php
                                    $nominalPengajuan = $tagihan->nominal_transfer ?: $tagihan->sisa_tagihan;
                                    $isCicilan = $nominalPengajuan < $tagihan->sisa_tagihan;
                                @endphp
                                <p class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($nominalPengajuan, 0, ',', '.') }}
                                </p>
                                <span class="inline-block text-[10px] px-1.5 py-0.5 rounded {{ $isCicilan ? 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' }} font-bold">
                                    {{ $isCicilan ? 'Pembayaran Cicilan' : 'Pelunasan Penuh' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Metode & Waktu</p>
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ strtoupper($tagihan->metode_pembayaran ?? '-') }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $tagihan->updated_at?->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            @if($tagihan->catatan_wali)
                                <div class="col-span-2 md:col-span-4">
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Catatan Wali</p>
                                    <p class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/60 p-2 rounded-lg border border-slate-200 dark:border-slate-700 mt-0.5">{{ $tagihan->catatan_wali }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex flex-wrap gap-3">
                            {{-- Konfirmasi Pembayaran / Cicilan --}}
                            <form method="POST" action="{{ route('bendahara.konfirmasi', $tagihan->id) }}">
                                @csrf
                                <input type="hidden" name="action" value="konfirmasi">
                                <input type="hidden" name="nominal_konfirmasi" value="{{ $nominalPengajuan }}">
                                <button type="submit"
                                        data-confirm="Konfirmasi penerimaan pembayaran sebesar Rp {{ number_format($nominalPengajuan, 0, ',', '.') }}?"
                                        class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow active:scale-95">
                                    <span>✅</span>
                                    <span>{{ $isCicilan ? 'Konfirmasi Cicilan (Rp ' . number_format($nominalPengajuan, 0, ',', '.') . ')' : 'Konfirmasi Lunas' }}</span>
                                </button>
                            </form>

                            {{-- Tolak --}}
                            <button type="button"
                                    @click="tolakModal = true"
                                    class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow active:scale-95">
                                <span>❌</span>
                                <span>Tolak</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Tolak --}}
            <div x-show="tolakModal" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 @click.self="tolakModal = false">
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">❌ Tolak Pembayaran</h3>
                        <button @click="tolakModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition text-lg leading-none">✕</button>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        Pembayaran <span class="font-bold text-slate-700 dark:text-slate-300">{{ $tagihan->santri->nama ?? '-' }}</span>
                        untuk tagihan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $tagihan->judul_tagihan }}</span> akan ditolak.
                    </p>
                    <form method="POST" action="{{ route('bendahara.konfirmasi', $tagihan->id) }}">
                        @csrf
                        <input type="hidden" name="action" value="tolak">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Alasan Penolakan <span class="text-rose-500">*</span></label>
                            <textarea name="catatan_bendahara" required rows="3"
                                      class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 outline-none resize-none"
                                      placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="tolakModal = false"
                                    class="flex-1 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition">
                                ❌ Ya, Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-12 text-center">
            <div class="text-5xl mb-4">🎉</div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Tidak Ada Antrean Verifikasi</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Semua transfer telah diverifikasi. Bagus!</p>
        </div>
    @endforelse

</div>
@endsection
