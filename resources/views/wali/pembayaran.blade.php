@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran Ananda - Wali Santri')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{
    bayarModal: false,
    selectedTagihan: null,
    nominalBayarInput: 0,
    bukaModalBayar(tagihan) {
        this.selectedTagihan = tagihan;
        this.nominalBayarInput = tagihan.sisa_raw;
        this.bayarModal = true;
    }
}">

    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-5 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-xl">
                💳
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold">
                        <span>Portal Keuangan Santri</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-300">PP. Miftahul Huda Kroya</span>
                </div>
                <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    Tagihan &amp; Pembayaran Ananda
                </h1>
            </div>
        </div>
    </div>

    @if($santri)
        {{-- Profil Santri Singkat --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-center space-x-4">
                @if($santri->foto_path)
                    <img src="{{ Storage::url($santri->foto_path) }}" alt="{{ $santri->nama }}"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-emerald-200 dark:border-emerald-800">
                @else
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-950/60 flex items-center justify-center text-blue-700 dark:text-blue-400 text-xl font-bold border-2 border-blue-200 dark:border-blue-800">
                        {{ strtoupper(substr($santri->nama, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">{{ $santri->nama }}</h2>
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

        {{-- Filter Status Tabs --}}
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('wali.pembayaran') }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-blue-600 text-white shadow' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                Semua Tagihan
            </a>
            <a href="{{ route('wali.pembayaran', ['status' => 'belum_bayar']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'belum_bayar' ? 'bg-rose-600 text-white shadow' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                Belum Bayar
            </a>
            <a href="{{ route('wali.pembayaran', ['status' => 'menunggu_konfirmasi']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'menunggu_konfirmasi' ? 'bg-amber-600 text-white shadow' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                Menunggu Konfirmasi
            </a>
            <a href="{{ route('wali.pembayaran', ['status' => 'lunas']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'lunas' ? 'bg-emerald-600 text-white shadow' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-50' }}">
                Lunas
            </a>
        </div>

        {{-- Daftar Tagihan --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Tagihan Ananda</h3>
                <span class="text-xs text-slate-400">{{ $tagihans->total() ?? count($tagihans) }} tagihan</span>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($tagihans as $tagihan)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs text-slate-400">{{ $tagihan->kode_tagihan }}</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $tagihan->status_badge }}">
                                    {{ $tagihan->status_label }}
                                </span>
                            </div>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $tagihan->judul_tagihan }}
                            </h4>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                <span>Kategori: <strong class="text-slate-700 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $tagihan->kategori)) }}</strong></span>
                                <span>&bull;</span>
                                <span>Jatuh Tempo: <strong class="{{ $tagihan->is_nunggak ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $tagihan->jatuh_tempo?->format('d M Y') }}</strong></span>
                                @if($tagihan->status === 'lunas')
                                    <span>&bull;</span>
                                    <span>Lunas pada: <strong class="text-emerald-600">{{ $tagihan->tanggal_bayar?->format('d M Y, H:i') }}</strong></span>
                                @endif
                            </div>

                            @if($tagihan->catatan_bendahara)
                                <div class="mt-2 text-xs p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300">
                                    <strong>Catatan Bendahara:</strong> {{ $tagihan->catatan_bendahara }}
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col sm:items-end gap-2 shrink-0">
                            <span class="text-lg font-extrabold text-slate-900 dark:text-white">
                                {{ $tagihan->nominal_rupiah }}
                            </span>

                            @if($tagihan->status === 'belum_bayar')
                                <button type="button"
                                        @click="bukaModalBayar({
                                            id: {{ $tagihan->id }},
                                            kode: '{{ $tagihan->kode_tagihan }}',
                                            judul: '{{ addslashes($tagihan->judul_tagihan) }}',
                                            nominal: '{{ $tagihan->nominal_rupiah }}',
                                            dibayar: '{{ $tagihan->nominal_dibayar_rupiah }}',
                                            sisa: '{{ $tagihan->sisa_tagihan_rupiah }}',
                                            sisa_raw: {{ $tagihan->sisa_tagihan }}
                                        })"
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow transition active:scale-95">
                                    <span>💳</span>
                                    <span>{{ $tagihan->nominal_dibayar > 0 ? 'Cicil / Bayar Sisa' : 'Bayar / Cicil' }}</span>
                                </button>
                            @elseif($tagihan->status === 'menunggu_konfirmasi')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-3 py-1.5 rounded-xl border border-amber-200 dark:border-amber-800">
                                    <span>⏳</span>
                                    <span>Menunggu Verifikasi Bendahara</span>
                                </span>
                            @elseif($tagihan->status === 'lunas')
                                <a href="{{ route('wali.kuitansi', $tagihan->id) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                                    <span>🧾</span>
                                    <span>Lihat Kuitansi</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 dark:text-slate-500">
                        <span class="text-3xl block mb-2">🎉</span>
                        <p class="text-sm font-semibold">Tidak ada tagihan pada kategori ini.</p>
                    </div>
                @endforelse
            </div>

            @if(method_exists($tagihans, 'hasPages') && $tagihans->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $tagihans->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-12 text-center text-slate-400">
            <span class="text-3xl block mb-2">⚠️</span>
            <p class="text-sm font-semibold">Data santri belum terhubung dengan akun Anda. Silakan hubungi admin pondok.</p>
        </div>
    @endif

    {{-- Modal Bayar Tagihan (Alpine.js) --}}
    <div x-show="bayarModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 space-y-6"
             @click.away="bayarModal = false">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Konfirmasi Pembayaran</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400" x-text="selectedTagihan?.judul"></p>
                </div>
                <button type="button" @click="bayarModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">
                    ✕
                </button>
            </div>

            {{-- Rekening Tujuan & Petunjuk Transfer --}}
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 space-y-2">
                <p class="font-bold flex items-center gap-1 text-sm">
                    <span>🏦</span>
                    <span>Rekening Resmi Pondok Pesantren:</span>
                </p>
                <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-emerald-200 dark:border-emerald-700/60 font-mono">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Bank Syariah Indonesia (BSI)</p>
                    <p class="text-sm font-extrabold text-emerald-700 dark:text-emerald-300">7123-4567-89</p>
                    <p class="text-[11px] text-slate-600 dark:text-slate-300">a.n. PP Miftahul Huda Kroya</p>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-emerald-200/60 dark:border-emerald-800/60 text-xs">
                    <span>Total Tagihan: <strong x-text="selectedTagihan?.nominal"></strong></span>
                    <span>Sisa Wajib Bayar: <strong class="text-emerald-700 dark:text-emerald-300 font-extrabold" x-text="selectedTagihan?.sisa"></strong></span>
                </div>
            </div>

            {{-- Form Submit Bukti Transfer --}}
            <form :action="'{{ url('/wali/pembayaran') }}/' + selectedTagihan?.id"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-4">
                @csrf

                {{-- Input Nominal Cicilan / Bayar --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Nominal yang Ditransfer / Dicicil <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" @click="nominalBayarInput = selectedTagihan?.sisa_raw"
                                class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            ⚡ Bayar Lunas Semua
                        </button>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                        <input type="number" name="nominal_bayar" required min="1000" :max="selectedTagihan?.sisa_raw"
                               x-model.number="nominalBayarInput"
                               class="w-full text-xs font-bold rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white pl-9 pr-3 py-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        💡 Anda dapat melunasi sekaligus atau <strong>mencicil sebagian</strong> terlebih dahulu.
                    </p>
                </div>

                {{-- Pilihan Metode Pembayaran --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Metode Transfer <span class="text-rose-500">*</span>
                    </label>
                    <select name="metode_pembayaran" required
                            class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2.5 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="transfer_bsi">Transfer Bank Syariah Indonesia (BSI)</option>
                        <option value="transfer_bca">Transfer Bank BCA</option>
                        <option value="transfer_bri">Transfer Bank BRI</option>
                        <option value="transfer_mandiri">Transfer Bank Mandiri</option>
                        <option value="qris">QRIS Standar Pesantren</option>
                        <option value="transfer_bank_lain">Transfer Bank Lainnya</option>
                    </select>
                </div>

                {{-- Upload Bukti Transfer --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Unggah Foto / Struk Bukti Transfer <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="bukti_transfer" required accept="image/*,application/pdf"
                           class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 dark:file:bg-emerald-950/60 dark:file:text-emerald-300 hover:file:bg-emerald-100">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, atau PDF (Maksimal 5MB)</p>
                </div>

                {{-- Catatan dari Wali --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea name="catatan_wali" rows="2" placeholder="Contoh: Sudah ditransfer via m-banking BSI atas nama Ahmad..."
                              class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="bayarModal = false"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow transition active:scale-95">
                        Kirim Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
