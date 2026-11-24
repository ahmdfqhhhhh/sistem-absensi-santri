@extends('layouts.app')

@section('title', 'Presensi Cepat / Batch Checklist - E-Absensi Santri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Presensi Cepat & Batch Checklist</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">PP. Miftahul Huda Kroya</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Metode pencatatan absensi massal per asrama & kelas saat darurat (Default: Hadir).</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('absensi.scan') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs tracking-wide transition-all border border-slate-200 dark:border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span>Beralih ke Scan QR</span>
            </a>
        </div>
    </div>

    <!-- Sesi & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-200 space-y-3">
        <form method="GET" action="{{ route('absensi.batch') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Sesi Absensi Aktif</label>
                <select name="sesi_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    @forelse($daftarSesi as $item)
                        @php
                            $isSekolah = $item->jenis === 'kegiatan_belajar';
                        @endphp
                        <option value="{{ $item->id }}" {{ ($sesiAktif?->id === $item->id) ? 'selected' : '' }}>
                            {{ $isSekolah ? '🎓' : '🕌' }} {{ $item->nama_sesi }} ({{ substr($item->jam_mulai, 0, 5) }} WIB{{ $item->batas_terlambat ? ' - Max ' . substr($item->batas_terlambat, 0, 5) : '' }})
                        </option>
                    @empty
                        <option value="">Tidak ada sesi absensi aktif</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Filter Asrama</label>
                <select name="asrama_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Semua Asrama --</option>
                    @foreach($asramas as $asrama)
                        <option value="{{ $asrama->id }}" {{ request('asrama_id') == $asrama->id ? 'selected' : '' }}>
                            {{ $asrama->nama_asrama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Filter Kelas</label>
                <select name="kelas_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }} ({{ $kelas->tingkat }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Pencarian Nama / NIS</label>
                <div class="flex items-center gap-1.5">
                    <div class="relative flex-1">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Ketik nama atau NIS santri..."
                               class="w-full pl-9 pr-8 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('absensi.batch', request()->except('search')) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="Hapus Pencarian">
                                ✕
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow transition-all shrink-0">
                        Cari
                    </button>
                </div>
            </div>
        </form>

        @if(request()->hasAny(['search', 'asrama_id', 'kelas_id']))
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-slate-700 dark:text-slate-300">Filter Aktif:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-medium">
                            Nama/NIS: "<strong>{{ request('search') }}</strong>"
                        </span>
                    @endif
                    @if(request('asrama_id') && $asramas->firstWhere('id', request('asrama_id')))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800 font-medium">
                            Asrama: {{ $asramas->firstWhere('id', request('asrama_id'))->nama_asrama }}
                        </span>
                    @endif
                    @if(request('kelas_id') && $kelases->firstWhere('id', request('kelas_id')))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800 font-medium">
                            Kelas: {{ $kelases->firstWhere('id', request('kelas_id'))->nama_kelas }}
                        </span>
                    @endif
                </div>
                <a href="{{ route('absensi.batch', ['sesi_id' => $sesiAktif?->id]) }}" class="text-rose-600 dark:text-rose-400 hover:underline font-bold text-xs">
                    Reset Semua Filter ✕
                </a>
            </div>
        @endif
    </div>

    @if(!$sesiAktif)
        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 p-6 rounded-2xl text-center text-amber-800 dark:text-amber-200">
            <p class="font-bold text-base">Tidak ada sesi absensi aktif saat ini.</p>
            <p class="text-xs mt-1">Silakan buka atau jadwalkan sesi absensi terlebih dahulu pada menu jadwal master.</p>
        </div>
    @else
        <!-- Bulk Form -->
        <form method="POST" action="{{ route('absensi.batch.process') }}">
            @csrf
            <input type="hidden" name="sesi_id" value="{{ $sesiAktif->id }}">

            <!-- Quick Action Toolbar -->
            <div class="bg-emerald-900 dark:bg-slate-900 text-white p-4 rounded-2xl shadow-lg border border-emerald-800/80 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm text-white">{{ $sesiAktif->nama_sesi }}</p>
                        <p id="counter-santri-text" class="text-xs text-emerald-200 dark:text-emerald-400">Total Santri: {{ $santris->count() }} orang</p>
                    </div>
                </div>

                <!-- Live Table Search Input -->
                <div class="w-full sm:w-64 relative order-last sm:order-none">
                    <input type="text"
                           id="live-santri-filter"
                           oninput="filterSantriTable(this.value)"
                           placeholder="Filter cepat nama / NIS..."
                           class="w-full pl-8 pr-7 py-1.5 text-xs rounded-xl bg-white/10 text-white placeholder-emerald-200/70 border border-emerald-700/60 focus:bg-white focus:text-slate-900 focus:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-colors">
                    <svg class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-emerald-300 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button"
                            id="clear-live-filter"
                            onclick="clearLiveFilter()"
                            class="hidden absolute right-2.5 top-2 text-slate-300 hover:text-white text-xs"
                            title="Bersihkan filter tabel">
                        ✕
                    </button>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="setAll('hadir')" class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-600 text-xs font-semibold text-emerald-100 transition-colors">
                        ✓ Set Hadir
                    </button>
                    <button type="button" onclick="setAll('alpa')" class="px-3 py-1.5 rounded-lg bg-rose-900/60 hover:bg-rose-800 text-xs font-semibold text-rose-200 transition-colors">
                        Set Alpa
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-brand-950 font-extrabold text-xs tracking-wider uppercase shadow-md active:scale-95 transition-all">
                        Simpan Presensi
                    </button>
                </div>
            </div>

            <!-- Santri Checklist Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="py-3 px-4 font-semibold w-12 text-center">No</th>
                                <th class="py-3 px-4 font-semibold">Santri</th>
                                <th class="py-3 px-4 font-semibold">Asrama / Kamar</th>
                                <th class="py-3 px-4 font-semibold">Kelas</th>
                                <th class="py-3 px-4 font-semibold text-center">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody id="santri-table-body" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($santris as $index => $santri)
                                @php
                                    $currentStatus = $existingAbsensis->get($santri->id, 'hadir');
                                @endphp
                                <tr class="santri-row hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors"
                                    data-nama="{{ strtolower($santri->nama) }}"
                                    data-nis="{{ strtolower($santri->nis) }}">
                                    <td class="py-3 px-4 text-center font-mono text-xs text-slate-400 dark:text-slate-500 santri-index">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-bold text-slate-900 dark:text-white santri-name">{{ $santri->nama }}</p>
                                        <p class="font-mono text-xs text-slate-400 dark:text-slate-500 santri-nis">{{ $santri->nis }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $santri->asrama->nama_asrama ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $santri->kamar ?? 'Kamar -' }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $santri->kelas->nama_kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-1.5 sm:gap-2">
                                            <!-- Hadir -->
                                            <label class="cursor-pointer inline-flex items-center px-2.5 py-1.5 rounded-lg border text-xs font-semibold has-[:checked]:bg-emerald-600 has-[:checked]:text-white has-[:checked]:border-emerald-600 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                <input type="radio" name="statuses[{{ $santri->id }}]" value="hadir" {{ $currentStatus === 'hadir' ? 'checked' : '' }} class="hidden status-radio status-hadir">
                                                <span>Hadir</span>
                                            </label>

                                            <!-- Terlambat -->
                                            <label class="cursor-pointer inline-flex items-center px-2.5 py-1.5 rounded-lg border text-xs font-semibold has-[:checked]:bg-amber-500 has-[:checked]:text-white has-[:checked]:border-amber-500 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                <input type="radio" name="statuses[{{ $santri->id }}]" value="terlambat" {{ $currentStatus === 'terlambat' ? 'checked' : '' }} class="hidden status-radio status-terlambat">
                                                <span>Telat</span>
                                            </label>

                                            <!-- Izin -->
                                            <label class="cursor-pointer inline-flex items-center px-2.5 py-1.5 rounded-lg border text-xs font-semibold has-[:checked]:bg-blue-600 has-[:checked]:text-white has-[:checked]:border-blue-600 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                <input type="radio" name="statuses[{{ $santri->id }}]" value="izin" {{ $currentStatus === 'izin' ? 'checked' : '' }} class="hidden status-radio status-izin">
                                                <span>Izin</span>
                                            </label>

                                            <!-- Sakit -->
                                            <label class="cursor-pointer inline-flex items-center px-2.5 py-1.5 rounded-lg border text-xs font-semibold has-[:checked]:bg-purple-600 has-[:checked]:text-white has-[:checked]:border-purple-600 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                <input type="radio" name="statuses[{{ $santri->id }}]" value="sakit" {{ $currentStatus === 'sakit' ? 'checked' : '' }} class="hidden status-radio status-sakit">
                                                <span>Sakit</span>
                                            </label>

                                            <!-- Alpa -->
                                            <label class="cursor-pointer inline-flex items-center px-2.5 py-1.5 rounded-lg border text-xs font-semibold has-[:checked]:bg-rose-600 has-[:checked]:text-white has-[:checked]:border-rose-600 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                                <input type="radio" name="statuses[{{ $santri->id }}]" value="alpa" {{ $currentStatus === 'alpa' ? 'checked' : '' }} class="hidden status-radio status-alpa">
                                                <span>Alpa</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                        Tidak ada data santri aktif pada filter asrama/kelas/pencarian ini.
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Row Khusus jika hasil Live Filter kosong -->
                            <tr id="live-empty-row" class="hidden">
                                <td colspan="5" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                    <p class="font-semibold text-sm">Tidak ada santri yang cocok dengan kata kunci di tabel ini.</p>
                                    <p class="text-xs mt-1">Coba gunakan kata kunci nama atau NIS lainnya.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
    const totalSantriAwal = {{ $santris->count() }};

    function filterSantriTable(query) {
        const cleanQuery = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.santri-row');
        const emptyRow = document.getElementById('live-empty-row');
        const clearBtn = document.getElementById('clear-live-filter');
        const counterText = document.getElementById('counter-santri-text');
        let visibleCount = 0;

        if (clearBtn) {
            clearBtn.classList.toggle('hidden', cleanQuery === '');
        }

        rows.forEach(function(row) {
            const nama = row.getAttribute('data-nama') || '';
            const nis = row.getAttribute('data-nis') || '';
            const matches = cleanQuery === '' || nama.includes(cleanQuery) || nis.includes(cleanQuery);

            if (matches) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        if (emptyRow) {
            emptyRow.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }

        if (counterText) {
            if (cleanQuery === '') {
                counterText.textContent = 'Total Santri: ' + totalSantriAwal + ' orang';
            } else {
                counterText.textContent = 'Menampilkan: ' + visibleCount + ' dari ' + totalSantriAwal + ' santri';
            }
        }
    }

    function clearLiveFilter() {
        const input = document.getElementById('live-santri-filter');
        if (input) {
            input.value = '';
            filterSantriTable('');
            input.focus();
        }
    }

    function setAll(status) {
        // Hanya set baris santri yang sedang terlihat (visible)
        const visibleRows = document.querySelectorAll('.santri-row:not(.hidden)');
        visibleRows.forEach(function(row) {
            const radio = row.querySelector('.status-' + status);
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }
</script>
@endsection
