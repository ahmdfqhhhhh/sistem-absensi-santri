@extends('layouts.app')

@section('title', 'Kelola Tagihan - Bendahara')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{
    target: 'semua',
    bayarModal: false,
    selectedTagihanId: null,
    selectedTagihanJudul: '',
    selectedTagihanSisa: '',
    selectedTagihanSisaRaw: 0,
    nominalBayarInput: 0,
    bukaModalBayar(id, judul, sisaStr, sisaRaw) {
        this.selectedTagihanId = id;
        this.selectedTagihanJudul = judul;
        this.selectedTagihanSisa = sisaStr;
        this.selectedTagihanSisaRaw = sisaRaw;
        this.nominalBayarInput = sisaRaw;
        this.bayarModal = true;
    }
}">

    {{-- Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl text-white p-6 md:p-8 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-8 -bottom-8 w-60 h-60 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center text-2xl">
                        📋
                    </div>
                    <div>
                        <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[11px] font-semibold">
                            <span>🏦 Bendahara Keuangan</span>
                        </div>
                        <p class="text-xs font-bold text-emerald-200 mt-0.5">Pondok Pesantren Miftahul Huda Kroya</p>
                    </div>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Kelola Tagihan Santri
                </h1>
                <p class="mt-2 text-sm text-emerald-200/90 max-w-2xl">
                    Buat tagihan baru (akumulasi otomatis per santri tanpa duplikasi baris), catat cicilan, dan pantau status tagihan.
                </p>
            </div>
            <a href="{{ route('bendahara.index') }}" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold text-xs tracking-wide transition active:scale-95">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Form Tambah Tagihan Baru --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-slate-100 dark:border-slate-800 gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">➕ Buat & Terbitkan Tagihan Baru</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jika santri sudah punya tagihan aktif yang belum lunas, nominal otomatis diakumulasikan ke tagihan santri tersebut</p>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                <span>⚡</span>
                <span>Akumulasi Otomatis (1 Santri 1 Baris Tagihan Aktif)</span>
            </div>
        </div>

        <form method="POST" action="{{ route('bendahara.tagihan.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Judul Tagihan --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Judul Tagihan <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul_tagihan" required value="{{ old('judul_tagihan') }}"
                           placeholder="Misal: SPP Bulan Desember 2026"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    @error('judul_tagihan') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                    <select name="kategori" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">Pilih Kategori</option>
                        <option value="spp" {{ old('kategori') === 'spp' ? 'selected' : '' }}>SPP Bulanan / Syahriah</option>
                        <option value="daftar_ulang" {{ old('kategori') === 'daftar_ulang' ? 'selected' : '' }}>Daftar Ulang</option>
                        <option value="perlengkapan" {{ old('kategori') === 'perlengkapan' ? 'selected' : '' }}>Kitab & Perlengkapan</option>
                        <option value="uang_gedung" {{ old('kategori') === 'uang_gedung' ? 'selected' : '' }}>Uang Gedung / Sarpras</option>
                        <option value="lainnya" {{ old('kategori') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('kategori') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nominal --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Nominal Tagihan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="nominal" required min="1000" value="{{ old('nominal') }}"
                           placeholder="Misal: 350000"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    @error('nominal') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jatuh Tempo --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Batas Jatuh Tempo <span class="text-rose-500">*</span></label>
                    <input type="date" name="jatuh_tempo" required value="{{ old('jatuh_tempo', now()->addDays(14)->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                    @error('jatuh_tempo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Target Penerima --}}
            <div class="mt-5">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2">Target Penerima Tagihan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <label class="flex items-center space-x-2 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-emerald-400 transition"
                           :class="target === 'semua' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 ring-2 ring-emerald-500/20' : ''">
                        <input type="radio" name="target" value="semua" x-model="target" class="text-emerald-600">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">👥 Semua Santri</span>
                            <span class="text-[10px] text-slate-400">Seluruh santri aktif (1-klik)</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-2 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-emerald-400 transition"
                           :class="target === 'kelas' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 ring-2 ring-emerald-500/20' : ''">
                        <input type="radio" name="target" value="kelas" x-model="target" class="text-emerald-600">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">📚 Per Kelas</span>
                            <span class="text-[10px] text-slate-400">Pilih kelas tertentu</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-2 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-emerald-400 transition"
                           :class="target === 'asrama' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 ring-2 ring-emerald-500/20' : ''">
                        <input type="radio" name="target" value="asrama" x-model="target" class="text-emerald-600">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">🏠 Per Asrama</span>
                            <span class="text-[10px] text-slate-400">Pilih asrama santri</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-2 p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-emerald-400 transition"
                           :class="(target === 'single' || target === 'santri') ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 ring-2 ring-emerald-500/20' : ''">
                        <input type="radio" name="target" value="single" x-model="target" class="text-emerald-600">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">👤 Santri Tertentu</span>
                            <span class="text-[10px] text-slate-400">Kasuistik 1 anak saja</span>
                        </div>
                    </label>
                </div>

                {{-- Target Kelas --}}
                <div x-show="target === 'kelas'" x-cloak class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 mb-3">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Kelas Tujuan:</label>
                    <select name="kelas_id" class="w-full md:w-80 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas ?? $kelasList ?? [] as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Target Asrama --}}
                <div x-show="target === 'asrama'" x-cloak class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 mb-3">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Asrama Tujuan:</label>
                    <select name="asrama_id" class="w-full md:w-80 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">-- Pilih Asrama --</option>
                        @foreach($asramas ?? $asramaList ?? [] as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_asrama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Target Santri Tertentu --}}
                <div x-show="target === 'single' || target === 'santri'" x-cloak class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 mb-3">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Santri:</label>
                    <select name="santri_id" class="w-full md:w-96 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        <option value="">-- Cari / Pilih Santri --</option>
                        @foreach($santris ?? [] as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }} (NIS: {{ $s->nis ?? '-' }}) – {{ $s->kelas->nama_kelas ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Sistem otomatis mengakumulasikan tagihan aktif santri bila belum lunas, atau menerbitkan tagihan baru jika sudah lunas.
                </span>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-sm transition shadow shadow-emerald-700/20">
                    🚀 Terbitkan / Akumulasikan Tagihan
                </button>
            </div>
        </form>
    </div>

    {{-- Filter & Tabel Tagihan --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">📑 Daftar Tagihan Santri</h3>
                <p class="text-xs text-slate-400 mt-0.5">Setiap anak memiliki 1 baris tagihan aktif terakumulasi yang dapat dicicil</p>
            </div>
            <form method="GET" action="{{ route('bendahara.tagihan.index') }}" class="flex flex-wrap gap-2">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari santri / tagihan..."
                       class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                <select name="filter_status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                    <option value="">Semua Status</option>
                    <option value="belum_bayar" {{ request('filter_status') === 'belum_bayar' ? 'selected' : '' }}>Belum Lunas / Dicicil</option>
                    <option value="menunggu_konfirmasi" {{ request('filter_status') === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="lunas" {{ request('filter_status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="nunggak" {{ request('filter_status') === 'nunggak' ? 'selected' : '' }}>Nunggak</option>
                </select>
                @if(request()->anyFilled(['cari', 'filter_status']))
                    <a href="{{ route('bendahara.tagihan.index') }}" class="px-2.5 py-1.5 text-xs text-rose-500 hover:underline self-center">Reset</a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Santri & Kelas</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rincian Tagihan</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tagihan</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Dicicil</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sisa Tunggakan</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    @forelse($tagihans ?? [] as $tagihan)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('bendahara.santri.detail', $tagihan->santri_id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition block text-xs">
                                    {{ $tagihan->santri->nama ?? '-' }}
                                </a>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                    {{ $tagihan->santri->kelas->nama_kelas ?? '-' }} · {{ $tagihan->santri->asrama->nama_asrama ?? '-' }}
                                </p>
                                <span class="text-[10px] font-mono text-slate-400">{{ $tagihan->kode_tagihan }}</span>
                            </td>
                            <td class="px-5 py-3 max-w-xs">
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 line-clamp-2" title="{{ $tagihan->judul_tagihan }}">
                                    {{ $tagihan->judul_tagihan }}
                                </p>
                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                    <span class="capitalize">{{ str_replace('_', ' ', $tagihan->kategori) }}</span>
                                    <span>•</span>
                                    <span>Tempo: {{ $tagihan->jatuh_tempo?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-xs font-extrabold text-slate-900 dark:text-white">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-xs font-semibold {{ $tagihan->nominal_dibayar > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                    Rp {{ number_format($tagihan->nominal_dibayar, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-xs font-extrabold {{ $tagihan->sisa_tagihan > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                    Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($tagihan->status === 'lunas')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        ✅ Lunas
                                    </span>
                                @elseif($tagihan->status === 'menunggu_konfirmasi')
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">
                                        ⏳ Menunggu Verifikasi
                                    </span>
                                    @if($tagihan->nominal_transfer)
                                        <p class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">
                                            Cicilan: Rp {{ number_format($tagihan->nominal_transfer, 0, ',', '.') }}
                                        </p>
                                    @endif
                                @elseif($tagihan->nominal_dibayar > 0)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                                        💳 Dicicil
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        📋 Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($tagihan->status === 'lunas')
                                        <a href="{{ route('bendahara.kuitansi', $tagihan->id) }}" target="_blank"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-sm">
                                            🧾 Kuitansi
                                        </a>
                                    @else
                                        <button type="button"
                                                @click="bukaModalBayar({{ $tagihan->id }}, '{{ addslashes($tagihan->santri->nama ?? '-') }} - {{ addslashes($tagihan->judul_tagihan) }}', 'Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}', {{ $tagihan->sisa_tagihan }})"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm active:scale-95">
                                            <span>💵</span>
                                            <span>Bayar / Cicil</span>
                                        </button>
                                        @if($tagihan->status === 'menunggu_konfirmasi')
                                            <a href="{{ route('bendahara.verifikasi') }}"
                                               class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition shadow-sm" title="Periksa Bukti">
                                                🔍
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                Belum ada tagihan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($tagihans) && $tagihans->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $tagihans->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Catat Pembayaran Tunai (Lunas / Cicil) --}}
    <div x-show="bayarModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @click.self="bayarModal = false"
         @keydown.escape.window="bayarModal = false">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">💵 Catat Pembayaran Tunai</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Mendukung pembayaran lunas atau cicilan bertahap</p>
                </div>
                <button @click="bayarModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold px-2 py-1 leading-none">✕</button>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 mb-4 text-xs">
                <p class="text-slate-500 dark:text-slate-400">Santri / Tagihan:</p>
                <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="selectedTagihanJudul"></p>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                    <span class="text-slate-500">Sisa Tagihan Saat Ini:</span>
                    <span class="font-extrabold text-rose-600 dark:text-rose-400 text-sm" x-text="selectedTagihanSisa"></span>
                </div>
            </div>

            <form :action="`/bendahara/tagihan/${selectedTagihanId}/tunai`" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Nominal Dibayar (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" @click="nominalBayarInput = selectedTagihanSisaRaw"
                                    class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                ⚡ Bayar Lunas Semua
                            </button>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                            <input type="number" name="nominal_bayar" required min="1000" :max="selectedTagihanSisaRaw"
                                   x-model.number="nominalBayarInput"
                                   class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Isi nominal sesuai uang tunai yang diterima (bisa cicilan).
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Catatan Pembayaran (Opsional)</label>
                        <textarea name="catatan_bendahara" rows="2"
                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none resize-none"
                                  placeholder="Contoh: Cicilan ke-1 tunai via wali santri di kantor"></textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="bayarModal = false"
                                class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow">
                            💾 Simpan Pembayaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
