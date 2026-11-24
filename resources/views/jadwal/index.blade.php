@extends('layouts.app')

@section('title', 'Pengaturan Jadwal Sholat & Sesi Presensi - E-Absensi Santri')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{
    filter: 'all',
    confirmReset: false,
    jamMulaiDirty: {}
}">

    {{-- =============================================
         HEADER & HERO BANNER
    ============================================= --}}
    <div class="relative overflow-hidden rounded-3xl text-white px-5 py-4 md:px-7 md:py-5 shadow-xl"
         style="background: linear-gradient(135deg, #0f172a 0%, #064e3b 40%, #0d3b2e 100%)">
        <div class="absolute -right-12 -top-12 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-400/40 text-amber-300 shadow-lg shrink-0 flex items-center justify-center font-bold text-xl">
                ⏱️
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[10px] font-semibold">
                        <span>⏱️ Manajemen Waktu Presensi</span>
                        <span>&bull;</span>
                        <span>Super Admin</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-200">PP. Miftahul Huda Kroya</span>
                </div>
                <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white leading-tight mt-0.5">
                    Pengaturan Jadwal Sholat &amp; Sesi Harian
                </h1>
            </div>
        </div>
    </div>

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold text-xs transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Dashboard</span>
                </a>
                <a href="{{ route('absensi.scan') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-emerald-600/80 hover:bg-emerald-600 text-white font-semibold text-xs transition-all active:scale-95">
                    <span>Lihat Scanner QR &rarr;</span>
                </a>
                <button type="button" @click="confirmReset = true" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 border border-rose-400/30 text-rose-200 hover:text-white font-semibold text-xs transition-all active:scale-95 ml-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset ke Bawaan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =============================================
         PANDUAN & STATUS SESI HARI INI
    ============================================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Card 1: Cara Kerja --}}
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-start space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white">Otomatisasi Harian</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                    Setiap hari sistem otomatis men-generate sesi sholat 5 waktu & KBM menggunakan jam yang Anda atur di bawah ini.
                </p>
            </div>
        </div>

        {{-- Card 2: Batas Terlambat --}}
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-start space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white">Batas Terlambat</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                    Santri yang scan sebelum batas waktu dicatat <span class="text-emerald-600 dark:text-emerald-400 font-bold">Hadir Tepat Waktu</span>. Lewat batas dicatat <span class="text-amber-600 dark:text-amber-400 font-bold">Terlambat</span>.
                </p>
            </div>
        </div>

        {{-- Card 3: Sinkronisasi Hari Ini --}}
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-start space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white">Sesi Hari Ini ({{ $sesiHariIni->count() }})</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                    Centang opsi sinkronisasi saat menyimpan agar perubahan jam langsung berlaku untuk sesi hari ini yang sedang berjalan.
                </p>
            </div>
        </div>
    </div>

    {{-- =============================================
         FORM SETTING JADWAL SHOLAT & SESI
    ============================================= --}}
    <form action="{{ route('jadwal.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Daftar Template Sesi Presensi Harian</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                            {{ $templates->count() }} Sesi
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Ubah jam mulai, jam selesai, atau nonaktifkan sesi yang sedang libur/tidak berlaku.</p>
                </div>

                {{-- Status Hari Ini Quick Badge --}}
                <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 px-3 py-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Waktu Server: <strong class="text-slate-700 dark:text-slate-200 font-mono">{{ now()->format('H:i') }} WIB</strong></span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                @foreach($templates as $index => $item)
                    @php
                        $badgeColors = match($item->kode) {
                            'SUBUH' => 'from-indigo-600 to-blue-500 text-white',
                            'KBM' => 'from-amber-500 to-orange-500 text-white',
                            'DZUHUR' => 'from-amber-400 to-yellow-500 text-slate-900',
                            'ASHAR' => 'from-orange-400 to-rose-500 text-white',
                            'MAGHRIB' => 'from-purple-600 to-pink-500 text-white',
                            'ISYA' => 'from-slate-800 to-indigo-950 text-white',
                            default => 'from-emerald-600 to-teal-500 text-white',
                        };

                        $iconEmoji = match($item->kode) {
                            'SUBUH' => '🌅',
                            'KBM' => '🎓',
                            'DZUHUR' => '☀️',
                            'ASHAR' => '🌤',
                            'MAGHRIB' => '🌆',
                            'ISYA' => '🌙',
                            default => '⏰',
                        };
                    @endphp

                    <div class="p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/30 hover:border-emerald-300 dark:hover:border-emerald-700/60 transition-all">
                        <input type="hidden" name="jadwal[{{ $index }}][id]" value="{{ $item->id }}">

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4 pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                            {{-- Header Sesi --}}
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr {{ $badgeColors }} flex items-center justify-center text-xl shadow-md shrink-0">
                                    {{ $iconEmoji }}
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-black px-2 py-0.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono">
                                            {{ $item->kode }}
                                        </span>
                                        <span class="text-xs text-slate-400 dark:text-slate-500">&bull;</span>
                                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                                            {{ ucfirst(str_replace('_', ' ', $item->jenis)) }}
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        <input type="text"
                                               name="jadwal[{{ $index }}][nama_sesi]"
                                               value="{{ old('jadwal.'.$index.'.nama_sesi', $item->nama_sesi) }}"
                                               required
                                               class="text-base font-bold text-slate-900 dark:text-white bg-transparent border-b border-dashed border-slate-300 dark:border-slate-600 focus:border-emerald-500 dark:focus:border-emerald-400 focus:outline-none w-full max-w-md py-0.5"
                                               placeholder="Nama Sesi Absensi">
                                    </div>
                                </div>
                            </div>

                            {{-- Toggle Aktif / Nonaktif --}}
                            <div class="flex items-center space-x-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="jadwal[{{ $index }}][aktif]"
                                           value="1"
                                           {{ old('jadwal.'.$index.'.aktif', $item->aktif) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600"></div>
                                    <span class="ml-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                        Sesi Aktif
                                    </span>
                                </label>
                            </div>
                        </div>

                        {{-- Input Grid: Jam Mulai, Batas Terlambat, Jam Selesai --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- Jam Mulai Presensi --}}
                            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-xs">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Jam Buka / Mulai</span>
                                        <span class="text-rose-500">*</span>
                                    </span>
                                </label>
                                <input type="time"
                                       name="jadwal[{{ $index }}][jam_mulai]"
                                       value="{{ old('jadwal.'.$index.'.jam_mulai', substr($item->jam_mulai, 0, 5)) }}"
                                       required
                                       class="w-full text-base font-mono font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Presensi dibuka di scanner</p>
                            </div>

                            {{-- Batas Waktu Terlambat --}}
                            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-amber-200 dark:border-amber-900/40 shadow-xs">
                                <label class="block text-xs font-semibold text-amber-700 dark:text-amber-400 mb-1">
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Batas Terlambat</span>
                                    </span>
                                </label>
                                <input type="time"
                                       name="jadwal[{{ $index }}][batas_terlambat]"
                                       value="{{ old('jadwal.'.$index.'.batas_terlambat', $item->batas_terlambat ? substr($item->batas_terlambat, 0, 5) : '') }}"
                                       class="w-full text-base font-mono font-bold text-slate-900 dark:text-white bg-amber-50/50 dark:bg-slate-800 rounded-lg border border-amber-200 dark:border-slate-700 px-3 py-1.5 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1">Lewat jam ini = Status Terlambat</p>
                            </div>

                            {{-- Jam Tutup / Selesai --}}
                            <div class="bg-white dark:bg-slate-900 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/80 shadow-xs">
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                        <span>Jam Tutup / Selesai</span>
                                        <span class="text-rose-500">*</span>
                                    </span>
                                </label>
                                <input type="time"
                                       name="jadwal[{{ $index }}][jam_selesai]"
                                       value="{{ old('jadwal.'.$index.'.jam_selesai', substr($item->jam_selesai, 0, 5)) }}"
                                       required
                                       class="w-full text-base font-mono font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-1.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Presensi ditutup otomatis</p>
                            </div>
                        </div>

                        {{-- Deskripsi / Keterangan Sesi --}}
                        <div class="mt-3">
                            <input type="text"
                                   name="jadwal[{{ $index }}][deskripsi]"
                                   value="{{ old('jadwal.'.$index.'.deskripsi', $item->deskripsi) }}"
                                   placeholder="Catatan / keterangan sesi (opsional)"
                                   class="w-full text-xs text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900 rounded-lg border border-slate-200/80 dark:border-slate-700/80 px-3 py-1.5 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FOOTER SUBMIT BAR --}}
            <div class="p-6 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                {{-- Checkbox Sinkron Sesi Hari Ini --}}
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox"
                           name="apply_to_today"
                           value="1"
                           checked
                           class="mt-1 w-4 h-4 text-emerald-600 rounded border-slate-300 dark:border-slate-600 focus:ring-emerald-500 dark:bg-slate-700">
                    <div>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            Sinkronkan juga ke sesi absensi hari ini yang aktif
                        </span>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Jam pada sesi hari ini ({{ today()->translatedFormat('l, d F Y') }}) akan langsung diperbarui mengikuti isian di atas.
                        </p>
                    </div>
                </label>

                {{-- Action Buttons --}}
                <div class="flex items-center space-x-3 shrink-0">
                    <a href="{{ url('/') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-emerald-950/20 active:scale-95 transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan Jadwal</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- =============================================
         MODAL KONFIRMASI RESET DEFAULTS
    ============================================= --}}
    <div x-show="confirmReset"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-2xl relative"
             @click.away="confirmReset = false">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-white">Reset Jadwal ke Standar Pesantren?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Tindakan ini akan mengembalikan seluruh jam sholat 5 waktu & KBM ke jam standar default pesantren (Subuh 04:15, KBM 06:30, Dzuhur 11:45, Ashar 15:00, Maghrib 17:45, Isya 19:15).
            </p>

            <form action="{{ route('jadwal.reset') }}" method="POST" class="mt-6 flex items-center justify-end space-x-3">
                @csrf
                <button type="button" @click="confirmReset = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm active:scale-95 transition-all">
                    Ya, Reset Sekarang
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
