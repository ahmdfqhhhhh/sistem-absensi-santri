@extends('layouts.app')

@section('title', ($tipe === 'harian' ? 'Rekapitulasi Absensi Harian' : 'Rekapitulasi Absensi Bulanan') . ' - E-Absensi Santri')

@section('content')
<div class="space-y-6">
    <!-- Header Section & Tab Navigasi -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Rekapitulasi Absensi Santri</h1>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $tipe === 'harian' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-700' : 'bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-400 border border-teal-300 dark:border-teal-700' }}">
                        {{ $tipe === 'harian' ? 'Harian' : 'Bulanan' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $tipe === 'harian' ? 'Laporan presensi sholat & kegiatan harian santri — ' . ($tanggalFormatted ?? $tanggal) : 'Laporan agregasi presensi santri periode ' . ($bulanNama ?? '') . ' ' . ($tahun ?? '') }} — PP. Miftahul Huda Kroya.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Tab Switcher Harian & Bulanan -->
            <div class="inline-flex p-1 rounded-xl bg-slate-200/80 dark:bg-slate-800 border border-slate-300/50 dark:border-slate-700">
                <a href="{{ route('rekap.absensi', ['tipe' => 'harian', 'tanggal' => $tanggal ?? today()->toDateString(), 'gender' => $gender ?? 'semua', 'asrama_id' => $asramaId ?? '', 'kelas_id' => $kelasId ?? '']) }}" class="px-3.5 py-1.5 rounded-lg text-xs {{ $tipe === 'harian' ? 'font-bold bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm' : 'font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition flex items-center gap-1.5">
                    <span>📅</span>
                    <span>Laporan Harian</span>
                </a>
                <a href="{{ route('rekap.absensi', ['tipe' => 'bulanan', 'bulan' => $bulan ?? now()->month, 'tahun' => $tahun ?? now()->year, 'gender' => $gender ?? 'semua', 'asrama_id' => $asramaId ?? '', 'kelas_id' => $kelasId ?? '']) }}" class="px-3.5 py-1.5 rounded-lg text-xs {{ $tipe === 'bulanan' ? 'font-bold bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm' : 'font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }} transition flex items-center gap-1.5">
                    <span>📊</span>
                    <span>Laporan Bulanan</span>
                </a>
            </div>

            <!-- Ekspor Excel -->
            <a href="{{ route('rekap.absensi.excel', array_merge(request()->all(), ['tipe' => $tipe])) }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Excel ({{ $tipe === 'harian' ? 'Harian' : 'Bulanan' }})</span>
            </a>

            <!-- Cetak PDF -->
            <a href="{{ route('rekap.absensi.pdf', array_merge(request()->all(), ['tipe' => $tipe])) }}" target="_blank" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs transition-all shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak PDF ({{ $tipe === 'harian' ? 'Harian' : 'Bulanan' }})</span>
            </a>
        </div>
    </div>

    @if($tipe === 'harian')
        {{-- ========================================================
             BAGIAN 1: REKAPITULASI HARIAN
        ======================================================== --}}
        <!-- Filter Form Harian -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('rekap.absensi') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <input type="hidden" name="tipe" value="harian">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                           class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kategori Santri</label>
                    <select name="gender" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="semua" {{ $gender == 'semua' ? 'selected' : '' }}>Semua (Putra & Putri)</option>
                        <option value="putra" {{ $gender == 'putra' ? 'selected' : '' }}>Santri Putra</option>
                        <option value="putri" {{ $gender == 'putri' ? 'selected' : '' }}>Santri Putri</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Asrama</label>
                    <select name="asrama_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">-- Semua Asrama --</option>
                        @foreach($asramas as $asr)
                            <option value="{{ $asr->id }}" {{ $asramaId == $asr->id ? 'selected' : '' }}>{{ $asr->nama_asrama }} ({{ ucfirst($asr->gender) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kelas Sekolah</label>
                    <select name="kelas_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelases as $kls)
                            <option value="{{ $kls->id }}" {{ $kelasId == $kls->id ? 'selected' : '' }}>{{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Filter Sesi Spesifik</label>
                    <select name="sesi_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">-- Seluruh Sesi Hari Ini --</option>
                        @foreach($allSesis as $sesi)
                            <option value="{{ $sesi->id }}" {{ ($sesiId ?? null) == $sesi->id ? 'selected' : '' }}>{{ $sesi->nama_sesi }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Summary Metrics Hari Ini -->
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3.5">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Santri</p>
                <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $summary['santri_count'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Santri terdata</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Total Hadir</p>
                <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ $summary['hadir'] }}</p>
                <p class="text-[11px] text-emerald-500 mt-0.5">Tepat waktu</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Terlambat</p>
                <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ $summary['terlambat'] }}</p>
                <p class="text-[11px] text-amber-500 mt-0.5">Melewati batas</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Izin</p>
                <p class="text-xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $summary['izin'] }}</p>
                <p class="text-[11px] text-blue-500 mt-0.5">Dispensasi izin</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sakit</p>
                <p class="text-xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ $summary['sakit'] }}</p>
                <p class="text-[11px] text-purple-500 mt-0.5">Kondisi uzur</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Alpa / Tanpa Ket.</p>
                <p class="text-xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ $summary['alpa'] }}</p>
                <p class="text-[11px] text-rose-500 mt-0.5">Perlu tindak lanjut</p>
            </div>
        </div>

        <!-- Fitur Salin Laporan WhatsApp Harian (Siap Kirim ke Grup) -->
        <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-emerald-950 text-white p-5 rounded-2xl border border-emerald-700/50 shadow-lg"
             x-data="{
                 openPreview: false,
                 copied: false,
                 waText: @js($waText ?? ''),
                 copyWa() {
                     if (navigator.clipboard && window.isSecureContext) {
                         navigator.clipboard.writeText(this.waText).then(() => {
                             this.copied = true;
                             setTimeout(() => { this.copied = false; }, 3000);
                         });
                     } else {
                         const ta = document.createElement('textarea');
                         ta.value = this.waText;
                         document.body.appendChild(ta);
                         ta.select();
                         document.execCommand('copy');
                         document.body.removeChild(ta);
                         this.copied = true;
                         setTimeout(() => { this.copied = false; }, 3000);
                     }
                 }
             }">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-400 flex items-center justify-center shrink-0 text-xl font-bold">
                        💬
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Format Laporan WhatsApp Grup</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 font-semibold">
                                {{ $tanggalFormatted ?? $tanggal }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-300">
                            Salin ringkasan santri Izin, Terlambat, Sakit, dan Alpa untuk dikirimkan langsung ke grup WhatsApp musyrif/pengurus.
                        </p>
                        <div class="flex items-center gap-3 text-xs mt-2 text-slate-200 flex-wrap">
                            <span class="inline-flex items-center gap-1 font-semibold text-blue-300">
                                <span class="w-2 h-2 rounded-full bg-blue-400"></span> Izin: {{ count($santriIzin ?? []) }}
                            </span>
                            <span class="inline-flex items-center gap-1 font-semibold text-amber-300">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Terlambat: {{ count($santriTerlambat ?? []) }}
                            </span>
                            <span class="inline-flex items-center gap-1 font-semibold text-purple-300">
                                <span class="w-2 h-2 rounded-full bg-purple-400"></span> Sakit: {{ count($santriSakit ?? []) }}
                            </span>
                            <span class="inline-flex items-center gap-1 font-semibold text-rose-300">
                                <span class="w-2 h-2 rounded-full bg-rose-400"></span> Alpa: {{ count($santriAlpa ?? []) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 md:shrink-0">
                    <button type="button" @click="copyWa()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-brand-950 font-bold text-xs shadow-lg transition-all active:scale-95">
                        <template x-if="!copied">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span>Salin Teks WhatsApp</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="flex items-center gap-1.5 text-brand-950 font-extrabold">
                                <span>✅</span>
                                <span>Berhasil Disalin!</span>
                            </span>
                        </template>
                    </button>

                    <a href="https://api.whatsapp.com/send?text={{ rawurlencode($waText ?? '') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs shadow-sm transition active:scale-95">
                        <span>📲</span>
                        <span>Buka WhatsApp</span>
                    </a>

                    <button type="button" @click="openPreview = !openPreview" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 transition active:scale-95">
                        <span x-text="openPreview ? 'Tutup Preview' : 'Lihat Format Teks'"></span>
                        <svg class="w-3.5 h-3.5 transition-transform" :class="openPreview ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Box Preview Format Pesan WA (Collapsible) -->
            <div x-show="openPreview" x-cloak class="mt-4 pt-4 border-t border-emerald-800/60">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-mono text-emerald-300">Preview Teks Siap Kirim:</span>
                    <button type="button" @click="copyWa()" class="text-[11px] font-bold text-amber-300 hover:underline flex items-center gap-1">
                        <span>📋 Salin Semua</span>
                    </button>
                </div>
                <div class="p-4 rounded-xl bg-black/40 border border-emerald-800/40 font-mono text-xs text-emerald-100 whitespace-pre-wrap select-all leading-relaxed max-h-64 overflow-y-auto">{{ $waText ?? '' }}</div>
            </div>
        </div>

        <!-- Ringkasan Per Sesi Hari Ini -->
        @if(isset($sesiBreakdown) && count($sesiBreakdown) > 0)
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-1.5">
                    <span>🕌</span>
                    <span>Ringkasan Kehadiran Per Sesi Sholat &amp; Kegiatan ({{ $tanggalFormatted ?? $tanggal }})</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                    @foreach($sesiBreakdown as $sb)
                        <div class="p-3 rounded-xl border border-slate-200/70 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $sb['sesi']->nama_sesi }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                    {{ substr($sb['sesi']->jam_mulai, 0, 5) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 text-[11px] flex-wrap">
                                <span class="text-emerald-700 dark:text-emerald-400 font-bold">{{ $sb['hadir'] }} H</span>
                                <span class="text-slate-300 dark:text-slate-600">·</span>
                                <span class="text-amber-700 dark:text-amber-400 font-semibold">{{ $sb['terlambat'] }} T</span>
                                <span class="text-slate-300 dark:text-slate-600">·</span>
                                <span class="text-blue-700 dark:text-blue-400 font-semibold">{{ $sb['izin'] }} I</span>
                                <span class="text-slate-300 dark:text-slate-600">·</span>
                                <span class="text-rose-700 dark:text-rose-400 font-bold">{{ $sb['alpa'] }} A</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Data Table Rekap Harian -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋</span>
                    <span>Daftar Presensi Santri ({{ count($rekapList) }} Santri Terfilter)</span>
                </h2>
                <div class="flex items-center gap-2 text-[11px]">
                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hadir</span>
                    <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-semibold"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Terlambat</span>
                    <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-semibold"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Izin</span>
                    <span class="inline-flex items-center gap-1 text-purple-600 dark:text-purple-400 font-semibold"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Sakit</span>
                    <span class="inline-flex items-center gap-1 text-rose-600 dark:text-rose-400 font-semibold"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Alpa</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800 font-bold">
                        <tr>
                            <th class="px-3.5 py-3 text-center w-10">No</th>
                            <th class="px-3.5 py-3">Santri</th>
                            <th class="px-3 py-3">Asrama / Kamar</th>
                            <th class="px-3 py-3">Kelas</th>
                            @if(isset($sesiId) && $sesiId)
                                <th class="px-3 py-3 text-center">Status Absen</th>
                                <th class="px-3 py-3">Waktu Presensi</th>
                                <th class="px-3 py-3">Metode / Pencatat</th>
                            @else
                                @foreach($allSesis as $sesi)
                                    <th class="px-2 py-3 text-center font-bold" title="{{ $sesi->nama_sesi }}">
                                        {{ Str::limit(str_replace(['Sholat ', ' Berjamaah', 'Presensi '], '', $sesi->nama_sesi), 10) }}
                                    </th>
                                @endforeach
                                <th class="px-3 py-3 text-center">Total H</th>
                                <th class="px-3 py-3 text-center">Total A</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70 font-medium">
                        @forelse($rekapList as $idx => $item)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="px-3.5 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-3.5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        @if($item['santri']->foto_path)
                                            <img src="{{ Storage::url($item['santri']->foto_path) }}" alt="{{ $item['santri']->nama }}" class="w-7 h-7 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                        @else
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-bold text-[11px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($item['santri']->nama, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $item['santri']->nama }}</p>
                                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">NIS: {{ $item['santri']->nis ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                    <div>{{ $item['santri']->asrama->nama_asrama ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">Kamar: {{ $item['santri']->kamarRelation->nama_kamar ?? ($item['santri']->kamar ?? '-') }}</div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $item['santri']->kelas->nama_kelas ?? '-' }}
                                </td>

                                @if(isset($sesiId) && $sesiId)
                                    @php
                                        $rec = $item['latest_record'];
                                        $st = $rec ? $rec->status : null;
                                    @endphp
                                    <td class="px-3 py-3 text-center">
                                        @if($st === 'hadir')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">Hadir</span>
                                        @elseif($st === 'terlambat')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400">Terlambat</span>
                                        @elseif($st === 'izin')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400">Izin</span>
                                        @elseif($st === 'sakit')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-400">Sakit</span>
                                        @elseif($st === 'alpa')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400">Alpa</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">Belum Absen</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-500 font-mono text-[11px]">
                                        {{ $rec && $rec->waktu_absen ? $rec->waktu_absen->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-slate-500 text-[11px]">
                                        {{ $rec ? ucfirst($rec->metode ?? 'manual') . ' (' . ($rec->recordedBy->name ?? 'Sistem') . ')' : '-' }}
                                    </td>
                                @else
                                    @foreach($allSesis as $sesi)
                                        @php
                                            $st = $item['sesi_status'][$sesi->id] ?? null;
                                        @endphp
                                        <td class="px-2 py-3 text-center">
                                            @if($st === 'hadir')
                                                <span class="inline-block w-6 h-6 leading-6 text-center rounded-lg font-bold text-[11px] bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400" title="Hadir">H</span>
                                            @elseif($st === 'terlambat')
                                                <span class="inline-block w-6 h-6 leading-6 text-center rounded-lg font-bold text-[11px] bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400" title="Terlambat">T</span>
                                            @elseif($st === 'izin')
                                                <span class="inline-block w-6 h-6 leading-6 text-center rounded-lg font-bold text-[11px] bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400" title="Izin">I</span>
                                            @elseif($st === 'sakit')
                                                <span class="inline-block w-6 h-6 leading-6 text-center rounded-lg font-bold text-[11px] bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-400" title="Sakit">S</span>
                                            @elseif($st === 'alpa')
                                                <span class="inline-block w-6 h-6 leading-6 text-center rounded-lg font-bold text-[11px] bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400" title="Alpa">A</span>
                                            @else
                                                <span class="inline-block w-6 h-6 leading-6 text-center rounded-lg text-slate-300 dark:text-slate-600 font-mono text-[11px]" title="Belum / Tidak Hadir">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-3 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $item['hadir'] }}
                                    </td>
                                    <td class="px-3 py-3 text-center font-bold text-rose-600 dark:text-rose-400">
                                        {{ $item['alpa'] }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (isset($sesiId) && $sesiId) ? 7 : (count($allSesis) + 6) }}" class="px-4 py-8 text-center text-slate-400">
                                    Tidak ada data santri yang sesuai filter untuk tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- ========================================================
             BAGIAN 2: REKAPITULASI BULANAN
        ======================================================== --}}
        <!-- Filter Form Bulanan -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('rekap.absensi') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                <input type="hidden" name="tipe" value="bulanan">

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Bulan</label>
                    <select name="bulan" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        @foreach($bulanNames as $num => $nama)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Tahun</label>
                    <select name="tahun" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kategori Santri</label>
                    <select name="gender" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="semua" {{ $gender == 'semua' ? 'selected' : '' }}>Semua Santri (Putra & Putri)</option>
                        <option value="putra" {{ $gender == 'putra' ? 'selected' : '' }}>Khusus Santri Putra</option>
                        <option value="putri" {{ $gender == 'putri' ? 'selected' : '' }}>Khusus Santri Putri</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Asrama</label>
                    <select name="asrama_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">-- Semua Asrama --</option>
                        @foreach($asramas as $asr)
                            <option value="{{ $asr->id }}" {{ $asramaId == $asr->id ? 'selected' : '' }}>{{ $asr->nama_asrama }} ({{ ucfirst($asr->gender) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kelas Sekolah</label>
                    <select name="kelas_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelases as $kls)
                            <option value="{{ $kls->id }}" {{ $kelasId == $kls->id ? 'selected' : '' }}>{{ $kls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Summary Metrics Bulanan -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Santri</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $summary['santri_count'] }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Santri terdata</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Hadir</p>
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $summary['hadir'] }}</p>
                <p class="text-[11px] text-emerald-500 mt-1">Presensi tepat waktu</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Terlambat</p>
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $summary['terlambat'] }}</p>
                <p class="text-[11px] text-amber-500 mt-1">Melewati toleransi</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Izin &amp; Sakit</p>
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ $summary['izin'] + $summary['sakit'] }}</p>
                <p class="text-[11px] text-blue-500 mt-1">{{ $summary['izin'] }} Izin · {{ $summary['sakit'] }} Sakit</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Alpa</p>
                <p class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-0.5">{{ $summary['alpa'] }}</p>
                <p class="text-[11px] text-rose-500 mt-1">Tanpa keterangan</p>
            </div>
        </div>

        <!-- Data Table Rekap Bulanan -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📋</span>
                    <span>Tabel Rekapitulasi Presensi Santri Bulanan ({{ count($rekapList) }} Santri)</span>
                </h2>
                <div class="flex items-center gap-2 text-[11px]">
                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hadir</span>
                    <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-semibold"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Terlambat</span>
                    <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-semibold"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Izin</span>
                    <span class="inline-flex items-center gap-1 text-purple-600 dark:text-purple-400 font-semibold"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Sakit</span>
                    <span class="inline-flex items-center gap-1 text-rose-600 dark:text-rose-400 font-semibold"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Alpa</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800 font-bold">
                        <tr>
                            <th class="px-3.5 py-3 text-center w-10">No</th>
                            <th class="px-3.5 py-3">Santri</th>
                            <th class="px-3 py-3">Asrama / Kamar</th>
                            <th class="px-3 py-3">Kelas</th>
                            <th class="px-3 py-3 text-center">Hadir</th>
                            <th class="px-3 py-3 text-center">Terlambat</th>
                            <th class="px-3 py-3 text-center">Izin</th>
                            <th class="px-3 py-3 text-center">Sakit</th>
                            <th class="px-3 py-3 text-center">Alpa</th>
                            <th class="px-3 py-3 text-center">Total Sesi</th>
                            <th class="px-3 py-3 text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70 font-medium">
                        @forelse($rekapList as $idx => $item)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="px-3.5 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="px-3.5 py-3">
                                    <div class="flex items-center gap-2.5">
                                        @if($item['santri']->foto_path)
                                            <img src="{{ Storage::url($item['santri']->foto_path) }}" alt="{{ $item['santri']->nama }}" class="w-7 h-7 rounded-lg object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                        @else
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-bold text-[11px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($item['santri']->nama, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white leading-tight">{{ $item['santri']->nama }}</p>
                                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">NIS: {{ $item['santri']->nis ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                    <div>{{ $item['santri']->asrama->nama_asrama ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">Kamar: {{ $item['santri']->kamarRelation->nama_kamar ?? ($item['santri']->kamar ?? '-') }}</div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                    {{ $item['santri']->kelas->nama_kelas ?? '-' }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $item['hadir'] }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-amber-600 dark:text-amber-400">
                                    {{ $item['terlambat'] }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-blue-600 dark:text-blue-400">
                                    {{ $item['izin'] }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-purple-600 dark:text-purple-400">
                                    {{ $item['sakit'] }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-rose-600 dark:text-rose-400">
                                    {{ $item['alpa'] }}
                                </td>
                                <td class="px-3 py-3 text-center font-bold text-slate-700 dark:text-slate-200">
                                    {{ $item['total'] }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold {{ ($item['persentase'] ?? 0) >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' : (($item['persentase'] ?? 0) >= 60 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400') }}">
                                        {{ $item['persentase'] ?? 0 }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-slate-400">
                                    Tidak ada data absensi untuk filter bulan ini.
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
