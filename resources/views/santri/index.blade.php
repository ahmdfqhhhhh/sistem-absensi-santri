@extends('layouts.app')

@section('title', 'Manajemen Data Santri, Asrama & Kamar - E-Absensi Santri')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Data Santri & Master Kamar</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">PP. Miftahul Huda Kroya</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola santri, asrama, kamar santri, kelas, dan cetak kartu identitas QR resmi.</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Tambah Santri -->
            <button type="button" onclick="openModal('modal-tambah-santri')" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-all shadow-md shadow-emerald-700/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>Santri</span>
            </button>

            <!-- Tombol Tambah Kamar -->
            <button type="button" onclick="openModal('modal-tambah-kamar')" class="inline-flex items-center space-x-1.5 px-3 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-all shadow-md shadow-blue-700/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>Kamar</span>
            </button>

            <!-- Tombol Tambah Asrama -->
            <button type="button" onclick="openModal('modal-tambah-asrama')" class="inline-flex items-center space-x-1.5 px-3 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm transition-all shadow-md shadow-purple-700/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                <span>Asrama</span>
            </button>


            <!-- Tombol Kelola Kamar -->
            <button type="button" onclick="openModal('modal-kelola-kamar')" class="inline-flex items-center space-x-1.5 px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/30 text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 font-semibold text-sm transition-all border border-slate-300 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-800 active:scale-95" title="Daftar, Edit & Hapus Kamar">
                <span>🛏️ Kelola Kamar ({{ $kamars->count() }})</span>
            </button>

            <!-- Tombol Kelola Asrama -->
            <button type="button" onclick="openModal('modal-kelola-asrama')" class="inline-flex items-center space-x-1.5 px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-purple-50 dark:hover:bg-purple-950/30 text-slate-700 dark:text-slate-200 hover:text-purple-600 dark:hover:text-purple-400 font-semibold text-sm transition-all border border-slate-300 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-800 active:scale-95" title="Daftar, Edit & Hapus Asrama">
                <span>🏢 Kelola Asrama ({{ $asramas->count() }})</span>
            </button>

            <!-- Tombol Kelola Kelas -->
            <button type="button" onclick="openModal('modal-kelola-kelas')" class="inline-flex items-center space-x-1.5 px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-teal-50 dark:hover:bg-teal-950/30 text-slate-700 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-400 font-semibold text-sm transition-all border border-slate-300 dark:border-slate-700 hover:border-teal-300 dark:hover:border-teal-800 active:scale-95" title="Daftar, Edit & Hapus Kelas">
                <span>📚 Kelola Kelas ({{ $kelases->count() }})</span>
            </button>

            <!-- Tombol Cetak / Unduh Kartu Massal -->
            <button type="button" onclick="openModal('modal-cetak-massal')" class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-sm transition-all shadow-md shadow-amber-600/20 active:scale-95" title="Download & Cetak Kartu Ber-QR Massal (A4 / PDF)">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>🖨️ Cetak Kartu Massal</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Santri Terdaftar</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $stats['total_semua'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Santri Putra</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $stats['total_putra'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Santri Putri</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $stats['total_putri'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Kamar & Asrama</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">{{ $kamars->count() }} <span class="text-xs font-normal text-slate-400">Kamar / {{ $asramas->count() }} Asrama</span></p>
            </div>
        </div>
    </div>

    {{-- Pembatas / Navigasi Tab Santri Putra & Putri --}}
    @if(!isset($lockedGender))
        <div class="flex items-center space-x-2">
            <a href="{{ route('santri.index', request()->except('gender')) }}"
               class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ !request('gender') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                <span>Semua Santri</span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ !request('gender') ? 'bg-emerald-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $stats['total_semua'] }}</span>
            </a>
            <a href="{{ route('santri.index', array_merge(request()->except('gender'), ['gender' => 'L'])) }}"
               class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ request('gender') === 'L' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                <span>Santri Putra</span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ request('gender') === 'L' ? 'bg-blue-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $stats['total_putra'] }}</span>
            </a>
            <a href="{{ route('santri.index', array_merge(request()->except('gender'), ['gender' => 'P'])) }}"
               class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ request('gender') === 'P' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100' }}">
                <span>Santri Putri</span>
                <span class="px-2 py-0.5 rounded-full text-xs {{ request('gender') === 'P' ? 'bg-purple-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">{{ $stats['total_putri'] }}</span>
            </a>
        </div>
    @else
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200">
            <span>🔒 Mode Terisolasi: Database Santri {{ $lockedGender === 'L' ? 'Putra' : 'Putri' }}</span>
        </div>
    @endif

    <!-- Filters & Search Form -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('santri.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            @if(request('gender'))
                <input type="hidden" name="gender" value="{{ request('gender') }}">
            @endif
            <div class="md:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan Nama atau NIS Santri..." class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div>
                <select name="asrama_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Semua Asrama --</option>
                    @foreach($asramas as $asrama)
                        <option value="{{ $asrama->id }}" {{ request('asrama_id') == $asrama->id ? 'selected' : '' }}>
                            {{ $asrama->nama_asrama }} ({{ ucfirst($asrama->gender) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="kamar" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Semua Kamar --</option>
                    @foreach($kamars as $k)
                        <option value="{{ $k->nama_kamar }}" {{ request('kamar') == $k->nama_kamar ? 'selected' : '' }}>
                            {{ $k->nama_kamar }} ({{ $k->asrama->nama_asrama ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="kelas_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelases as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @if(request()->hasAny(['search', 'asrama_id', 'kamar', 'kelas_id', 'gender']))
                    <a href="{{ route('santri.index') }}" class="p-2 text-xs text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg shrink-0 font-bold" title="Reset Filter">✕</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <!-- Form Tersembunyi untuk Hapus Massal -->
    <form id="form-bulk-delete" method="POST" action="{{ route('santri.bulk-delete') }}" class="hidden">
        @csrf
        <div id="bulk-delete-inputs"></div>
    </form>

    <!-- Toolbar Aksi Hapus Massal (Muncul ketika data dipilih) -->
    <div id="bulk-action-bar" class="hidden mb-3 bg-gradient-to-r from-rose-50 to-amber-50 dark:from-rose-950/40 dark:to-slate-900 border-2 border-rose-300 dark:border-rose-900/80 rounded-2xl p-3.5 shadow-md transition-all duration-300">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600 text-white font-black text-sm shadow-sm ring-2 ring-rose-300 dark:ring-rose-800">
                    <span id="bulk-selected-badge">0</span>
                </span>
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span><span id="bulk-selected-count">0</span> Data Santri Dipilih</span>
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih secara manual atau tentukan jumlah santri yang ingin dihapus sekaligus.</p>
                </div>
            </div>

            <!-- Kontrol Pilih Jumlah Tertentu & Tombol Eksekusi Hapus -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs shadow-xs">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Pilih Jumlah:</span>
                    <button type="button" onclick="selectSantriCount(5)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold transition">5</button>
                    <button type="button" onclick="selectSantriCount(10)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold transition">10</button>
                    <button type="button" onclick="selectSantriCount(25)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold transition">25</button>
                    <div class="h-3.5 w-px bg-slate-200 dark:bg-slate-700 mx-0.5"></div>
                    <div class="flex items-center gap-1">
                        <input type="number" id="custom-select-count" min="1" max="{{ $santris->count() }}" placeholder="Qty" class="w-14 px-1.5 py-0.5 text-xs text-center rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 font-semibold">
                        <button type="button" onclick="applyCustomSelectCount()" class="px-2 py-0.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition active:scale-95">Pilih</button>
                    </div>
                </div>

                <button type="button" onclick="deselectAllSantri()" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold border border-slate-200 dark:border-slate-700 transition">
                    ✕ Batal
                </button>

                <button type="button" onclick="executeBulkDelete()" class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-md shadow-rose-600/30 active:scale-95 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span>Hapus <span id="btn-delete-count">0</span> Santri Terpilih</span>
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/80 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-3.5 w-10 text-center">
                            <input type="checkbox" id="check-all-santri" class="w-4 h-4 rounded text-emerald-600 border-slate-300 dark:border-slate-700 focus:ring-emerald-500 cursor-pointer" title="Pilih Semua Santri di Halaman Ini">
                        </th>
                        <th class="py-3.5 px-4 font-semibold">Santri</th>
                        <th class="py-3.5 px-4 font-semibold">NIS</th>
                        <th class="py-3.5 px-4 font-semibold">Asrama & Kamar</th>
                        <th class="py-3.5 px-4 font-semibold">Kelas</th>
                        <th class="py-3.5 px-4 font-semibold">Wali & WhatsApp</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($santris as $santri)
                        <tr id="row-santri-{{ $santri->id }}" class="santri-row hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="py-3.5 px-3.5 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $santri->id }}" class="santri-checkbox w-4 h-4 rounded text-emerald-600 border-slate-300 dark:border-slate-700 focus:ring-emerald-500 cursor-pointer" data-id="{{ $santri->id }}" data-nama="{{ $santri->nama }}">
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    @if($santri->foto_url)
                                        <img src="{{ $santri->foto_url }}" alt="{{ $santri->nama }}" class="w-10 h-10 rounded-full object-cover border-2 {{ $santri->jenis_kelamin === 'L' ? 'border-blue-500' : 'border-purple-500' }} shadow-sm shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-full {{ $santri->jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border-blue-200 dark:border-blue-800' : 'bg-purple-100 text-purple-800 dark:bg-purple-950/70 dark:text-purple-300 border-purple-200 dark:border-purple-800' }} font-bold flex items-center justify-center shrink-0 border">
                                            {{ substr($santri->nama, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="font-semibold text-slate-900 dark:text-white">{{ $santri->nama }}</p>
                                            @if($santri->has_face_descriptor)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800" title="Wajah terdaftar untuk presensi AI">
                                                    <span>👤</span>
                                                    <span>Wajah AI</span>
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mt-0.5">
                                            @if($santri->jenis_kelamin === 'L')
                                                <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                                                <span class="text-blue-600 dark:text-blue-400 font-medium">Santri Putra</span>
                                            @else
                                                <span class="inline-block w-2 h-2 rounded-full bg-purple-500"></span>
                                                <span class="text-purple-600 dark:text-purple-400 font-medium">Santri Putri</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-xs font-semibold px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-md">
                                    {{ $santri->nis }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-slate-800 dark:text-slate-200 font-medium">{{ $santri->asrama->nama_asrama ?? '-' }}</p>
                                <div class="flex items-center space-x-1 mt-0.5">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                        {{ $santri->kamar ?? 'Kamar belum diatur' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    {{ $santri->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-slate-800 dark:text-slate-200 font-medium">{{ $santri->nama_wali ?? '-' }}</p>
                                @if($santri->no_wa_wali)
                                    @php
                                        $cleanWa = preg_replace('/[^0-9]/', '', $santri->no_wa_wali);
                                        if (str_starts_with($cleanWa, '0')) {
                                            $cleanWa = '62' . substr($cleanWa, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanWa }}" target="_blank" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 mt-0.5">
                                        <span>{{ $santri->no_wa_wali }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($santri->status === 'aktif')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300">
                                        Aktif
                                    </span>
                                @elseif($santri->status === 'izin_pulang')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                        Izin Pulang
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ ucfirst($santri->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Tombol Rekam Wajah AI -->
                                    <button type="button"
                                        class="btn-rekam-wajah p-1.5 rounded-lg {{ $santri->has_face_descriptor ? 'text-blue-700 dark:text-blue-400 bg-blue-50/80 dark:bg-blue-950/60 border-blue-300 dark:border-blue-700 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border-slate-200 dark:border-slate-700' }} text-xs flex items-center gap-1 border transition"
                                        data-id="{{ $santri->id }}"
                                        data-nama="{{ htmlspecialchars($santri->nama, ENT_QUOTES, 'UTF-8') }}"
                                        data-nis="{{ $santri->nis }}"
                                        data-foto="{{ $santri->foto_url ?? '' }}"
                                        data-has-face="{{ $santri->has_face_descriptor ? '1' : '0' }}"
                                        title="{{ $santri->has_face_descriptor ? 'Wajah Terdaftar (Klik untuk rekam ulang / hapus)' : 'Rekam Wajah Santri untuk Presensi AI' }}">
                                        <span>👤</span>
                                        <span>{{ $santri->has_face_descriptor ? 'Wajah ✅' : 'Wajah' }}</span>
                                    </button>

                                    <!-- Tombol Preview Kartu ID -->
                                    <button type="button"
                                        class="btn-preview-kartu p-1.5 rounded-lg text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 font-medium text-xs flex items-center gap-1 border border-emerald-200 dark:border-emerald-800"
                                        data-santri="{{ json_encode($santri, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                                        data-asrama="{{ $santri->asrama->nama_asrama ?? '-' }}"
                                        data-kelas="{{ $santri->kelas->nama_kelas ?? '-' }}"
                                        title="Cetak Kartu ID QR">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                        <span>Kartu</span>
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button type="button"
                                        class="btn-edit-santri p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700"
                                        data-santri="{{ htmlspecialchars(json_encode($santri), ENT_QUOTES, 'UTF-8') }}"
                                        title="Edit Santri">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form method="POST" action="{{ route('santri.destroy', $santri->id) }}" data-confirm="Apakah Anda yakin ingin menghapus santri {{ addslashes($santri->nama) }}? Data presensi dan file foto/QR santri akan ikut terhapus." class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-rose-200 dark:border-rose-900/60" title="Hapus Santri">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <p class="text-base font-semibold">Belum ada data santri</p>
                                <p class="text-xs mt-1">Klik tombol "Tambah Santri" untuk menambahkan santri pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($santris->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50">
                {{ $santris->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ========================================================
     MODAL 1: TAMBAH DATA SANTRI
======================================================== --}}
<div id="modal-tambah-santri" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Data Santri Baru</h3>
            <button type="button" onclick="closeModal('modal-tambah-santri')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('santri.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS (Nomor Induk Santri) *</label>
                    <input type="text" name="nis" required value="{{ old('nis') }}" placeholder="Contoh: 2026001" class="w-full px-3 py-2 text-sm rounded-xl border @error('nis') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    @error('nis')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin *</label>
                    <select id="tambah-jenis-kelamin" name="jenis_kelamin" required onchange="filterAsramaByGender()" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="L" {{ old('jenis_kelamin', request('gender')) === 'L' ? 'selected' : '' }}>Laki-laki (Putra)</option>
                        <option value="P" {{ old('jenis_kelamin', request('gender')) === 'P' ? 'selected' : '' }}>Perempuan (Putri)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Santri *</label>
                <input type="text" name="nama" required value="{{ old('nama') }}" placeholder="Nama lengkap sesuai akta" class="w-full px-3 py-2 text-sm rounded-xl border @error('nama') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Upload Foto Santri (Opsional)</label>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/60 dark:file:text-emerald-300 border border-slate-300 dark:border-slate-700 rounded-xl">
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Format JPG, PNG, atau WEBP. Maksimal 5MB.</p>
                @error('foto')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Asrama Penempatan *</label>
                    <select id="tambah-asrama-select" name="asrama_id" required class="w-full px-3 py-2 text-sm rounded-xl border @error('asrama_id') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">-- Pilih Asrama --</option>
                        @foreach($asramas as $asrama)
                            <option value="{{ $asrama->id }}" data-gender="{{ $asrama->gender }}" {{ old('asrama_id') == $asrama->id ? 'selected' : '' }}>{{ $asrama->nama_asrama }} ({{ ucfirst($asrama->gender) }})</option>
                        @endforeach
                    </select>
                    @error('asrama_id')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kamar Asrama</label>
                    <input list="tambah-kamar-datalist" type="text" name="kamar" value="{{ old('kamar') }}" placeholder="Pilih / ketik nama kamar..." class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <datalist id="tambah-kamar-datalist">
                        @foreach($kamars as $k)
                            <option value="{{ $k->nama_kamar }}">{{ $k->nama_kamar }} ({{ $k->asrama->nama_asrama ?? '-' }})</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas Sekolah *</label>
                <select name="kelas_id" required class="w-full px-3 py-2 text-sm rounded-xl border @error('kelas_id') border-rose-500 @else border-slate-300 dark:border-slate-700 @enderror bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Pilih Jenjang & Kelas Sekolah --</option>
                    <optgroup label="🏫 SMP (Sekolah Menengah Pertama)">
                        @php $k7 = $kelases->first(fn($k) => $k->tingkat === '7 SMP' || $k->nama_kelas === 'Kelas 7 SMP'); @endphp
                        <option value="{{ $k7?->id ?? '7 SMP' }}" {{ old('kelas_id') == ($k7?->id ?? '7 SMP') ? 'selected' : '' }}>Kelas 7 SMP</option>
                        @php $k8 = $kelases->first(fn($k) => $k->tingkat === '8 SMP' || $k->nama_kelas === 'Kelas 8 SMP'); @endphp
                        <option value="{{ $k8?->id ?? '8 SMP' }}" {{ old('kelas_id') == ($k8?->id ?? '8 SMP') ? 'selected' : '' }}>Kelas 8 SMP</option>
                        @php $k9 = $kelases->first(fn($k) => $k->tingkat === '9 SMP' || $k->nama_kelas === 'Kelas 9 SMP'); @endphp
                        <option value="{{ $k9?->id ?? '9 SMP' }}" {{ old('kelas_id') == ($k9?->id ?? '9 SMP') ? 'selected' : '' }}>Kelas 9 SMP</option>
                    </optgroup>
                    <optgroup label="📚 SMA (Sekolah Menengah Atas)">
                        @php $k10sma = $kelases->first(fn($k) => $k->tingkat === '10 SMA' || $k->nama_kelas === 'Kelas 10 SMA'); @endphp
                        <option value="{{ $k10sma?->id ?? '10 SMA' }}" {{ old('kelas_id') == ($k10sma?->id ?? '10 SMA') ? 'selected' : '' }}>Kelas 10 SMA</option>
                        @php $k11sma = $kelases->first(fn($k) => $k->tingkat === '11 SMA' || $k->nama_kelas === 'Kelas 11 SMA'); @endphp
                        <option value="{{ $k11sma?->id ?? '11 SMA' }}" {{ old('kelas_id') == ($k11sma?->id ?? '11 SMA') ? 'selected' : '' }}>Kelas 11 SMA</option>
                        @php $k12sma = $kelases->first(fn($k) => $k->tingkat === '12 SMA' || $k->nama_kelas === 'Kelas 12 SMA'); @endphp
                        <option value="{{ $k12sma?->id ?? '12 SMA' }}" {{ old('kelas_id') == ($k12sma?->id ?? '12 SMA') ? 'selected' : '' }}>Kelas 12 SMA</option>
                    </optgroup>
                    <optgroup label="🔧 SMK (Sekolah Menengah Kejuruan)">
                        @php $k10smk = $kelases->first(fn($k) => $k->tingkat === '10 SMK' || $k->nama_kelas === 'Kelas 10 SMK'); @endphp
                        <option value="{{ $k10smk?->id ?? '10 SMK' }}" {{ old('kelas_id') == ($k10smk?->id ?? '10 SMK') ? 'selected' : '' }}>Kelas 10 SMK</option>
                        @php $k11smk = $kelases->first(fn($k) => $k->tingkat === '11 SMK' || $k->nama_kelas === 'Kelas 11 SMK'); @endphp
                        <option value="{{ $k11smk?->id ?? '11 SMK' }}" {{ old('kelas_id') == ($k11smk?->id ?? '11 SMK') ? 'selected' : '' }}>Kelas 11 SMK</option>
                        @php $k12smk = $kelases->first(fn($k) => $k->tingkat === '12 SMK' || $k->nama_kelas === 'Kelas 12 SMK'); @endphp
                        <option value="{{ $k12smk?->id ?? '12 SMK' }}" {{ old('kelas_id') == ($k12smk?->id ?? '12 SMK') ? 'selected' : '' }}>Kelas 12 SMK</option>
                    </optgroup>
                    @php
                        $customKelases = $kelases->filter(function($k) {
                            $stdTingkats = ['7 SMP', '8 SMP', '9 SMP', '10 SMA', '11 SMA', '12 SMA', '10 SMK', '11 SMK', '12 SMK'];
                            return !in_array($k->tingkat, $stdTingkats) && !in_array($k->nama_kelas, ['Kelas 7 SMP', 'Kelas 8 SMP', 'Kelas 9 SMP', 'Kelas 10 SMA', 'Kelas 11 SMA', 'Kelas 12 SMA', 'Kelas 10 SMK', 'Kelas 11 SMK', 'Kelas 12 SMK']);
                        });
                    @endphp
                    @if($customKelases->isNotEmpty())
                        <optgroup label="🏷️ Kelas Lainnya">
                            @foreach($customKelases as $ck)
                                <option value="{{ $ck->id }}" {{ old('kelas_id') == $ck->id ? 'selected' : '' }}>{{ $ck->nama_kelas }} ({{ $ck->tingkat }})</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                @error('kelas_id')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Wali Santri</label>
                    <input type="text" name="nama_wali" value="{{ old('nama_wali') }}" placeholder="Nama ayah/ibu/wali" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp Wali</label>
                    <input type="text" name="no_wa_wali" value="{{ old('no_wa_wali') }}" placeholder="08123456789" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modal-tambah-santri')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-emerald-700/20">Simpan Santri</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL 2: TAMBAH DATA KAMAR
======================================================== --}}
<div id="modal-tambah-kamar" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Kamar Asrama Baru</h3>
            <button type="button" onclick="closeModal('modal-tambah-kamar')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('kamar.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Asrama *</label>
                <select name="asrama_id" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">-- Pilih Asrama Penempatan --</option>
                    @foreach($asramas as $asrama)
                        <option value="{{ $asrama->id }}">{{ $asrama->nama_asrama }} ({{ ucfirst($asrama->gender) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama / Nomor Kamar *</label>
                <input type="text" name="nama_kamar" required placeholder="Contoh: Kamar 102 atau Abu Bakar 3" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Maksimal Santri</label>
                <input type="number" name="kapasitas" value="10" min="1" max="100" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Posisi Lantai (Opsional)</label>
                <input type="text" name="keterangan" placeholder="Contoh: Lantai 2 Sayap Timur" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modal-tambah-kamar')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md shadow-blue-700/20">Simpan Kamar</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL 3: TAMBAH DATA ASRAMA
======================================================== --}}
<div id="modal-tambah-asrama" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Gedung / Asrama Baru</h3>
            <button type="button" onclick="closeModal('modal-tambah-asrama')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form method="POST" action="{{ route('asrama.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Asrama *</label>
                <input type="text" name="nama_asrama" required placeholder="Contoh: Asrama Al-Farabi" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Peruntukan Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        <option value="putra">Santri Putra</option>
                        <option value="putri">Santri Putri</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Gedung / Blok</label>
                    <input type="text" name="gedung" placeholder="Contoh: Gedung B Lt. 2" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ustadz Pembina Asrama</label>
                    <input type="text" name="pembina" placeholder="Nama Musyrif / Pembina" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Total</label>
                    <input type="number" name="kapasitas" value="50" min="1" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modal-tambah-asrama')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-md shadow-purple-700/20">Simpan Asrama</button>
            </div>
        </form>
    </div>
</div>


        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
            <button type="button" onclick="closeModal('modal-kelola-kamar')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Tutup</button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL: KELOLA DAFTAR ASRAMA & HAPUS ASRAMA
======================================================== --}}
<div id="modal-kelola-asrama" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-3xl w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Master Asrama Pesantren</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar gedung asrama, pembina, kapasitas, dan pengelolaan hapus asrama.</p>
            </div>
            <button type="button" onclick="closeModal('modal-kelola-asrama')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <div class="mt-4 flex-1 overflow-y-auto space-y-4 pr-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Total {{ $asramas->count() }} Asrama Terdaftar</span>
                <button type="button" onclick="closeModal('modal-kelola-asrama'); openModal('modal-tambah-asrama');" class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1">
                    <span>+ Tambah Asrama Baru</span>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Asrama Putra -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between pb-1 border-b border-blue-200 dark:border-blue-900/60">
                        <span class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🏢</span>
                            <span>Asrama Putra ({{ $asramasPutra->count() }})</span>
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                        @forelse($asramasPutra as $asr)
                            <div class="p-3 flex items-center justify-between bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $asr->nama_asrama }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">
                                            {{ $asr->gedung ?? 'Gedung Putra' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">
                                        Pembina: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $asr->pembina ?? '-' }}</span> &bull;
                                        Kapasitas: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $asr->kapasitas ? $asr->kapasitas.' santri' : '-' }}</span> &bull;
                                        Penghuni: <span class="font-bold {{ ($asr->santris_count ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">{{ $asr->santris_count ?? 0 }} santri</span> &bull;
                                        Kamar: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $asr->kamars_count ?? $asr->kamars->count() }} kamar</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" class="btn-edit-asrama p-1.5 px-3 rounded-lg text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 border border-amber-200 dark:border-amber-900/60 text-xs font-semibold inline-flex items-center gap-1" data-asrama="{{ json_encode($asr) }}" title="Edit Asrama">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span>Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('asrama.destroy', $asr->id) }}" data-confirm="Apakah Anda yakin ingin menghapus asrama {{ addslashes($asr->nama_asrama) }}? Pastikan asrama tidak memiliki santri dan kamar." class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 px-3 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-rose-200 dark:border-rose-900/60 text-xs font-semibold inline-flex items-center gap-1" title="Hapus Asrama">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-slate-400 dark:text-slate-500 text-xs">
                                Belum ada data asrama putra.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Asrama Putri -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between pb-1 border-b border-purple-200 dark:border-purple-900/60">
                        <span class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🧕</span>
                            <span>Asrama Putri ({{ $asramasPutri->count() }})</span>
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                        @forelse($asramasPutri as $asr)
                            <div class="p-3 flex items-center justify-between bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $asr->nama_asrama }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300">
                                            {{ $asr->gedung ?? 'Gedung Putri' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">
                                        Pembina: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $asr->pembina ?? '-' }}</span> &bull;
                                        Kapasitas: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $asr->kapasitas ? $asr->kapasitas.' santri' : '-' }}</span> &bull;
                                        Penghuni: <span class="font-bold {{ ($asr->santris_count ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">{{ $asr->santris_count ?? 0 }} santri</span> &bull;
                                        Kamar: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $asr->kamars_count ?? $asr->kamars->count() }} kamar</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" class="btn-edit-asrama p-1.5 px-3 rounded-lg text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 border border-amber-200 dark:border-amber-900/60 text-xs font-semibold inline-flex items-center gap-1" data-asrama="{{ json_encode($asr) }}" title="Edit Asrama">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        <span>Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('asrama.destroy', $asr->id) }}" data-confirm="Apakah Anda yakin ingin menghapus asrama {{ addslashes($asr->nama_asrama) }}? Pastikan asrama tidak memiliki santri dan kamar." class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 px-3 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-rose-200 dark:border-rose-900/60 text-xs font-semibold inline-flex items-center gap-1" title="Hapus Asrama">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-slate-400 dark:text-slate-500 text-xs">
                                Belum ada data asrama putri.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
            <button type="button" onclick="closeModal('modal-kelola-asrama')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Tutup</button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL: KELOLA DAFTAR KELAS & HAPUS KELAS
======================================================== --}}
<div id="modal-kelola-kelas" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Master Kelas Sekolah Umum</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar kelas sekolah umum/formal, wali kelas, jumlah santri terdaftar, dan kelola kelas.</p>
            </div>
            <button type="button" onclick="closeModal('modal-kelola-kelas')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <div class="mt-4 flex-1 overflow-y-auto space-y-4 pr-1">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Total {{ $kelases->count() }} Kelas Terdaftar</span>

            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                @forelse($kelases as $kls)
                    <div class="p-3.5 flex items-center justify-between bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors">
                        <div class="space-y-0.5">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $kls->nama_kelas }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300">
                                    Tingkat {{ $kls->tingkat }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                Wali Kelas: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $kls->wali_kelas ?? '-' }}</span> &bull;
                                Santri Terdaftar: <span class="font-bold {{ ($kls->santris_count ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">{{ $kls->santris_count ?? 0 }} santri</span>
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" class="btn-edit-kelas p-1.5 px-3 rounded-lg text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 border border-amber-200 dark:border-amber-900/60 text-xs font-semibold inline-flex items-center gap-1" data-kelas="{{ json_encode($kls) }}" title="Edit Kelas">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                <span>Edit</span>
                            </button>
                            <form method="POST" action="{{ route('kelas.destroy', $kls->id) }}" data-confirm="Apakah Anda yakin ingin menghapus kelas {{ addslashes($kls->nama_kelas) }}? Pastikan tidak ada santri yang terdaftar di kelas ini." class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 px-3 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-rose-200 dark:border-rose-900/60 text-xs font-semibold inline-flex items-center gap-1" title="Hapus Kelas">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-400 dark:text-slate-500 text-xs">
                        Belum ada data kelas yang terdaftar.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
            <button type="button" onclick="closeModal('modal-kelola-kelas')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Tutup</button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL: EDIT KAMAR
======================================================== --}}
<div id="modal-edit-kamar" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Kamar</h3>
            <button type="button" onclick="closeModal('modal-edit-kamar')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form id="form-edit-kamar" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Gedung Asrama *</label>
                <select id="edit-kamar-asrama-id" name="asrama_id" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @foreach($asramas as $asr)
                        <option value="{{ $asr->id }}">{{ $asr->nama_asrama }} ({{ ucfirst($asr->gender) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama / Nomor Kamar *</label>
                <input type="text" id="edit-kamar-nama" name="nama_kamar" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Maksimal (Santri) *</label>
                <input type="number" id="edit-kamar-kapasitas" name="kapasitas" min="1" max="100" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Fasilitas</label>
                <input type="text" id="edit-kamar-keterangan" name="keterangan" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('modal-edit-kamar')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md shadow-blue-700/20">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL: EDIT ASRAMA
======================================================== --}}
<div id="modal-edit-asrama" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Asrama</h3>
            <button type="button" onclick="closeModal('modal-edit-asrama')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form id="form-edit-asrama" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Asrama *</label>
                <input type="text" id="edit-asrama-nama" name="nama_asrama" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Peruntukan Gender *</label>
                    <select id="edit-asrama-gender" name="gender" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        <option value="putra">Santri Putra</option>
                        <option value="putri">Santri Putri</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Gedung / Blok</label>
                    <input type="text" id="edit-asrama-gedung" name="gedung" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pembina / Musyrif</label>
                    <input type="text" id="edit-asrama-pembina" name="pembina" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas (Santri)</label>
                    <input type="number" id="edit-asrama-kapasitas" name="kapasitas" min="1" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                <input type="text" id="edit-asrama-keterangan" name="keterangan" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('modal-edit-asrama')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-md shadow-purple-700/20">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL: EDIT KELAS
======================================================== --}}
<div id="modal-edit-kelas" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Data Kelas</h3>
            <button type="button" onclick="closeModal('modal-edit-kelas')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form id="form-edit-kelas" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Kelas Sekolah *</label>
                <input type="text" id="edit-kelas-nama" name="nama_kelas" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tingkat / Jenjang *</label>
                <select id="edit-kelas-tingkat" name="tingkat" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    <option value="">-- Pilih Tingkat Kelas --</option>
                    <optgroup label="🏫 SMP (Sekolah Menengah Pertama)">
                        <option value="7 SMP">Kelas 7 SMP</option>
                        <option value="8 SMP">Kelas 8 SMP</option>
                        <option value="9 SMP">Kelas 9 SMP</option>
                    </optgroup>
                    <optgroup label="📚 SMA (Sekolah Menengah Atas)">
                        <option value="10 SMA">Kelas 10 SMA</option>
                        <option value="11 SMA">Kelas 11 SMA</option>
                        <option value="12 SMA">Kelas 12 SMA</option>
                    </optgroup>
                    <optgroup label="🔧 SMK (Sekolah Menengah Kejuruan)">
                        <option value="10 SMK">Kelas 10 SMK</option>
                        <option value="11 SMK">Kelas 11 SMK</option>
                        <option value="12 SMK">Kelas 12 SMK</option>
                    </optgroup>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jurusan / Peminatan</label>
                    <input type="text" id="edit-kelas-jurusan" name="jurusan" placeholder="Contoh: IPA, IPS, Umum" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Wali Kelas</label>
                    <input type="text" id="edit-kelas-wali" name="wali_kelas" placeholder="Nama Guru / Wali Kelas" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('modal-edit-kelas')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-md shadow-teal-700/20">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL 5: EDIT SANTRI
======================================================== --}}
<div id="modal-edit-santri" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Perbarui Data Santri</h3>
            <button type="button" onclick="closeModal('modal-edit-santri')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form id="form-edit-santri" method="POST" action="" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">NIS (Nomor Induk)</label>
                    <input type="text" id="edit-nis" name="nis" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin</label>
                    <select id="edit-jenis-kelamin" name="jenis_kelamin" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="L">Laki-laki (Putra)</option>
                        <option value="P">Perempuan (Putri)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Santri *</label>
                <input type="text" id="edit-nama" name="nama" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ganti Foto Santri (Opsional)</label>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/60 dark:file:text-emerald-300 border border-slate-300 dark:border-slate-700 rounded-xl">
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Biarkan kosong jika tidak ingin mengubah foto santri.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Asrama *</label>
                    <select id="edit-asrama-id" name="asrama_id" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        @foreach($asramas as $asrama)
                            <option value="{{ $asrama->id }}">{{ $asrama->nama_asrama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kamar Asrama</label>
                    <input list="edit-kamar-datalist" type="text" id="edit-kamar" name="kamar" placeholder="Pilih / ketik nama kamar..." class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <datalist id="edit-kamar-datalist">
                        @foreach($kamars as $k)
                            <option value="{{ $k->nama_kamar }}">{{ $k->nama_kamar }} ({{ $k->asrama->nama_asrama ?? '-' }})</option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelas Sekolah *</label>
                <select id="edit-kelas-id" name="kelas_id" required class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Pilih Jenjang & Kelas Sekolah --</option>
                    <optgroup label="🏫 SMP (Sekolah Menengah Pertama)">
                        @php $k7 = $kelases->first(fn($k) => $k->tingkat === '7 SMP' || $k->nama_kelas === 'Kelas 7 SMP'); @endphp
                        <option value="{{ $k7?->id ?? '7 SMP' }}">Kelas 7 SMP</option>
                        @php $k8 = $kelases->first(fn($k) => $k->tingkat === '8 SMP' || $k->nama_kelas === 'Kelas 8 SMP'); @endphp
                        <option value="{{ $k8?->id ?? '8 SMP' }}">Kelas 8 SMP</option>
                        @php $k9 = $kelases->first(fn($k) => $k->tingkat === '9 SMP' || $k->nama_kelas === 'Kelas 9 SMP'); @endphp
                        <option value="{{ $k9?->id ?? '9 SMP' }}">Kelas 9 SMP</option>
                    </optgroup>
                    <optgroup label="📚 SMA (Sekolah Menengah Atas)">
                        @php $k10sma = $kelases->first(fn($k) => $k->tingkat === '10 SMA' || $k->nama_kelas === 'Kelas 10 SMA'); @endphp
                        <option value="{{ $k10sma?->id ?? '10 SMA' }}">Kelas 10 SMA</option>
                        @php $k11sma = $kelases->first(fn($k) => $k->tingkat === '11 SMA' || $k->nama_kelas === 'Kelas 11 SMA'); @endphp
                        <option value="{{ $k11sma?->id ?? '11 SMA' }}">Kelas 11 SMA</option>
                        @php $k12sma = $kelases->first(fn($k) => $k->tingkat === '12 SMA' || $k->nama_kelas === 'Kelas 12 SMA'); @endphp
                        <option value="{{ $k12sma?->id ?? '12 SMA' }}">Kelas 12 SMA</option>
                    </optgroup>
                    <optgroup label="🔧 SMK (Sekolah Menengah Kejuruan)">
                        @php $k10smk = $kelases->first(fn($k) => $k->tingkat === '10 SMK' || $k->nama_kelas === 'Kelas 10 SMK'); @endphp
                        <option value="{{ $k10smk?->id ?? '10 SMK' }}">Kelas 10 SMK</option>
                        @php $k11smk = $kelases->first(fn($k) => $k->tingkat === '11 SMK' || $k->nama_kelas === 'Kelas 11 SMK'); @endphp
                        <option value="{{ $k11smk?->id ?? '11 SMK' }}">Kelas 11 SMK</option>
                        @php $k12smk = $kelases->first(fn($k) => $k->tingkat === '12 SMK' || $k->nama_kelas === 'Kelas 12 SMK'); @endphp
                        <option value="{{ $k12smk?->id ?? '12 SMK' }}">Kelas 12 SMK</option>
                    </optgroup>
                    @if($customKelases->isNotEmpty())
                        <optgroup label="🏷️ Kelas Lainnya">
                            @foreach($customKelases as $ck)
                                <option value="{{ $ck->id }}">{{ $ck->nama_kelas }} ({{ $ck->tingkat }})</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Wali Santri</label>
                    <input type="text" id="edit-nama-wali" name="nama_wali" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp Wali</label>
                    <input type="text" id="edit-no-wa" name="no_wa_wali" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Keaktifan</label>
                <select id="edit-status" name="status" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="aktif">Aktif</option>
                    <option value="izin_pulang">Izin Pulang</option>
                    <option value="skorsing">Skorsing</option>
                    <option value="alumni">Alumni</option>
                    <option value="mutasi">Mutasi</option>
                </select>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('modal-edit-santri')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-emerald-700/20">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL 6: PREVIEW & CETAK KARTU ID SANTRI BER-QR
======================================================== --}}
<div id="modal-kartu" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="no-print flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Kartu Tanda Santri (PVC Ready)</h3>
            <button type="button" onclick="closeModal('modal-kartu')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold">✕</button>
        </div>

        <div id="kartu-printable" class="mt-4 p-5 rounded-2xl bg-gradient-to-br from-emerald-900 via-brand-900 to-teal-950 text-white shadow-xl border-2 border-amber-400/40 relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-emerald-500/10 blur-xl"></div>
            <div class="absolute -left-8 -top-8 w-28 h-28 rounded-full bg-amber-400/10 blur-lg"></div>

            <div class="flex items-center space-x-3 pb-3 border-b border-emerald-700/60 relative z-10">
                <div class="w-12 h-12 rounded-full bg-white p-0.5 flex items-center justify-center shrink-0 shadow-md ring-2 ring-amber-400/50 overflow-hidden">
                    <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo PPMH Kroya" class="w-full h-full object-contain">
                </div>
                <div>
                    <h4 class="font-extrabold text-xs sm:text-sm tracking-wide text-white uppercase">PONDOK PESANTREN MIFTAHUL HUDA KROYA</h4>
                    <p class="text-[10px] text-amber-300 font-medium">KARTU IDENTITAS RESMI PRESENSI SANTRI</p>
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3.5 relative z-10">
                {{-- Pas Foto Santri --}}
                <div id="kartu-foto-container" class="w-20 h-24 rounded-xl border-2 border-amber-400/80 bg-emerald-950/80 shrink-0 overflow-hidden flex items-center justify-center shadow-md">
                    <!-- Foto diinjeksi via JS -->
                </div>

                {{-- QR Code Santri --}}
                <div class="bg-white p-2 rounded-xl shadow-lg shrink-0 flex items-center justify-center" style="width: 95px; height: 95px;">
                    <div id="kartu-qrcode" class="w-full h-full flex items-center justify-center">
                        <!-- QR Code image injected dynamically -->
                    </div>
                </div>

                {{-- Identitas Santri --}}
                <div class="space-y-1 text-xs flex-1 min-w-0">
                    <div>
                        <p class="text-[9px] text-emerald-300 uppercase font-bold">Nama Santri</p>
                        <p id="kartu-nama" class="font-extrabold text-sm text-white tracking-tight truncate"></p>
                    </div>
                    <div>
                        <p class="text-[9px] text-emerald-300 uppercase font-bold">NIS (Kode Scan)</p>
                        <p id="kartu-nis" class="font-mono text-xs font-bold text-amber-300"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5 pt-0.5 text-[10px] text-emerald-100">
                        <div class="truncate">
                            <span class="block text-[8px] text-emerald-400 font-semibold">Asrama / Kamar</span>
                            <span id="kartu-asrama" class="font-medium truncate block"></span>
                        </div>
                        <div class="truncate">
                            <span class="block text-[8px] text-emerald-400 font-semibold">Kelas</span>
                            <span id="kartu-kelas" class="font-medium truncate block"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-emerald-800/80 flex items-center justify-between text-[9px] text-emerald-300/80 relative z-10">
                <span>Scan kartu ini pada setiap sesi absensi</span>
                <span class="font-semibold text-amber-300">E-ABSENSI SANTRI</span>
            </div>
        </div>

        <div class="no-print mt-5 flex items-center justify-end gap-2">
            <button type="button" onclick="closeModal('modal-kartu')" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Tutup</button>
            <button type="button" onclick="window.print()" class="px-4 py-2 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Cetak Kartu</span>
            </button>
        </div>
    </div>
</div>

{{-- ========================================================
     MODAL: CETAK KARTU SANTRI BER-QR MASSAL
======================================================== --}}
<div id="modal-cetak-massal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>🖨️ Cetak & Download Kartu Massal</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Cetak kartu identitas ber-QR dan pas foto santri per kelas, asrama, atau seluruhnya.</p>
            </div>
            <button type="button" onclick="closeModal('modal-cetak-massal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <form method="GET" action="{{ route('santri.cetak-kartu') }}" target="_blank" class="mt-4 space-y-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-xl text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                <span class="text-lg leading-none shrink-0">💡</span>
                <p>Kartu akan otomatis disusun dalam lembaran A4 standar (siap cetak printer atau simpan sebagai PDF) lengkap dengan <strong>Pas Foto</strong>, <strong>QR Code Resmi</strong>, dan identitas santri.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Filter Berdasarkan Kelas Sekolah</label>
                <select name="kelas_id" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Semua Kelas (Seluruh Santri) --</option>
                    @foreach($kelases as $kls)
                        <option value="{{ $kls->id }}">{{ $kls->nama_kelas }} ({{ $kls->santris_count ?? 0 }} santri)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Filter Berdasarkan Asrama Santri</label>
                <select name="asrama_id" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    <option value="">-- Semua Asrama --</option>
                    @foreach($asramas as $asr)
                        <option value="{{ $asr->id }}">{{ $asr->nama_asrama }} ({{ ucfirst($asr->gender) }} - {{ $asr->santris_count ?? 0 }} santri)</option>
                    @endforeach
                </select>
            </div>

            @if(!$lockedGender)
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Filter Gender / Satuan Santri</label>
                    <select name="gender" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">Semua Santri (Putra & Putri)</option>
                        <option value="L">Khusus Santri Putra</option>
                        <option value="P">Khusus Santri Putri</option>
                    </select>
                </div>
            @endif

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-cetak-massal')" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Batal</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md shadow-emerald-700/20 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    <span>Buka Lembar Cetak / PDF</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================
     MODAL: PEREKAMAN FITUR WAJAH SANTRI (FACE AI RECOGNITION)
======================================================== --}}
<div id="modal-rekam-wajah" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                    👤
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Perekaman Wajah Santri</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300">AI Face Recognition</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        <span id="face-modal-santri-nama" class="font-bold text-slate-800 dark:text-slate-200">-</span>
                        (<span id="face-modal-santri-nis" class="font-mono">-</span>)
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeFaceEnrollModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">✕</button>
        </div>

        <div class="mt-4 space-y-4">
            <!-- Viewfinder Kamera Live -->
            <div class="relative w-full aspect-[4/3] bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center shadow-inner">
                <video id="face-enroll-video" autoplay muted playsinline class="w-full h-full object-cover transform -scale-x-100"></video>
                <canvas id="face-enroll-canvas" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

                <!-- Reticle Panduan Wajah -->
                <div id="face-enroll-reticle" class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-44 h-56 border-2 border-dashed border-blue-400/50 rounded-[45%] flex items-center justify-center">
                        <span class="text-[10px] text-blue-300/80 bg-slate-950/70 px-2 py-0.5 rounded-full">Posisikan Wajah</span>
                    </div>
                </div>

                <!-- Overlay Status & Loading -->
                <div id="face-enroll-loading" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm flex flex-col items-center justify-center gap-2 text-white text-xs z-20">
                    <div class="w-8 h-8 border-4 border-blue-500/20 border-t-blue-500 rounded-full animate-spin"></div>
                    <span id="face-enroll-loading-text" class="font-semibold text-blue-300">Memuat AI Face Recognition...</span>
                </div>
            </div>

            <!-- Petunjuk & Status Real-time -->
            <div class="flex items-center justify-between text-xs px-1">
                <div class="flex items-center gap-2">
                    <span id="face-detect-indicator" class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span id="face-detect-status" class="font-medium text-slate-700 dark:text-slate-300">Menghubungkan kamera...</span>
                </div>
                <span id="face-quality-badge" class="font-mono text-[11px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Siap</span>
            </div>

            <!-- Opsi Ekstrak dari Foto Profil Jika Ada -->
            <div id="face-from-photo-section" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 text-xs hidden">
                <div class="flex items-center gap-2.5">
                    <img id="face-photo-preview" src="" alt="Foto" class="w-10 h-10 rounded-full object-cover border border-slate-300 dark:border-slate-600 shrink-0">
                    <div>
                        <p class="font-semibold text-slate-800 dark:text-slate-200">Gunakan Pas Foto Terdaftar</p>
                        <p class="text-[11px] text-slate-500">Ekstrak otomatis dari foto profil santri yang ada.</p>
                    </div>
                </div>
                <button type="button" id="btn-extract-from-photo" onclick="extractFaceFromExistingPhoto()" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-950 dark:hover:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-xl font-bold text-xs transition shrink-0">
                    Ekstrak Foto
                </button>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-between pt-2">
                <button type="button" id="btn-delete-face" onclick="deleteSantriFace()" class="px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl border border-rose-200 dark:border-rose-900/60 transition hidden">
                    🗑️ Hapus Wajah
                </button>
                <div class="flex items-center gap-2 ml-auto">
                    <button type="button" onclick="closeFaceEnrollModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">
                        Tutup
                    </button>
                    <button type="button" id="btn-save-face" onclick="saveEnrolledFace()" disabled class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 dark:disabled:bg-slate-700 text-white rounded-xl shadow-md transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>Simpan Wajah</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/face-api.js') }}"></script>
<script>
    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }

    // Event listener untuk tombol Edit Santri (Aman dari kutip satu / karakter khusus)
    document.querySelectorAll('.btn-edit-santri').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const santri = JSON.parse(this.getAttribute('data-santri'));
                document.getElementById('form-edit-santri').action = '/santri/' + santri.id;
                document.getElementById('edit-nis').value = santri.nis || '';
                document.getElementById('edit-jenis-kelamin').value = santri.jenis_kelamin || 'L';
                document.getElementById('edit-nama').value = santri.nama || '';
                document.getElementById('edit-asrama-id').value = santri.asrama_id || '';
                document.getElementById('edit-kamar').value = santri.kamar || '';
                document.getElementById('edit-kelas-id').value = santri.kelas_id || '';
                document.getElementById('edit-nama-wali').value = santri.nama_wali || '';
                document.getElementById('edit-no-wa').value = santri.no_wa_wali || '';
                document.getElementById('edit-status').value = santri.status || 'aktif';

                openModal('modal-edit-santri');
            } catch (e) {
                console.error("Gagal memproses data santri:", e);
            }
        });
    });

    // Event listener untuk tombol Edit Kamar
    document.querySelectorAll('.btn-edit-kamar').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const kamar = JSON.parse(this.getAttribute('data-kamar'));
                document.getElementById('form-edit-kamar').action = '/kamar/' + kamar.id;
                document.getElementById('edit-kamar-asrama-id').value = kamar.asrama_id || '';
                document.getElementById('edit-kamar-nama').value = kamar.nama_kamar || '';
                document.getElementById('edit-kamar-kapasitas').value = kamar.kapasitas || 10;
                document.getElementById('edit-kamar-keterangan').value = kamar.keterangan || '';

                closeModal('modal-kelola-kamar');
                openModal('modal-edit-kamar');
            } catch (e) {
                console.error("Gagal memproses data kamar:", e);
            }
        });
    });

    // Event listener untuk tombol Edit Asrama
    document.querySelectorAll('.btn-edit-asrama').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const asrama = JSON.parse(this.getAttribute('data-asrama'));
                document.getElementById('form-edit-asrama').action = '/asrama/' + asrama.id;
                document.getElementById('edit-asrama-nama').value = asrama.nama_asrama || '';
                document.getElementById('edit-asrama-gender').value = asrama.gender || 'putra';
                document.getElementById('edit-asrama-gedung').value = asrama.gedung || '';
                document.getElementById('edit-asrama-pembina').value = asrama.pembina || '';
                document.getElementById('edit-asrama-kapasitas').value = asrama.kapasitas || '';
                document.getElementById('edit-asrama-keterangan').value = asrama.keterangan || '';

                closeModal('modal-kelola-asrama');
                openModal('modal-edit-asrama');
            } catch (e) {
                console.error("Gagal memproses data asrama:", e);
            }
        });
    });

    // Event listener untuk tombol Edit Kelas
    document.querySelectorAll('.btn-edit-kelas').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const kelas = JSON.parse(this.getAttribute('data-kelas'));
                document.getElementById('form-edit-kelas').action = '/kelas/' + kelas.id;
                document.getElementById('edit-kelas-nama').value = kelas.nama_kelas || '';
                document.getElementById('edit-kelas-tingkat').value = kelas.tingkat || '7 SMP';
                if (document.getElementById('edit-kelas-jurusan')) {
                    document.getElementById('edit-kelas-jurusan').value = kelas.jurusan || '';
                }
                document.getElementById('edit-kelas-wali').value = kelas.wali_kelas || '';

                closeModal('modal-kelola-kelas');
                openModal('modal-edit-kelas');
            } catch (e) {
                console.error("Gagal memproses data kelas:", e);
            }
        });
    });

    // Event listener untuk Preview Kartu Santri
    document.querySelectorAll('.btn-preview-kartu').forEach(btn => {
        btn.addEventListener('click', function() {
            try {
                const santri = JSON.parse(this.getAttribute('data-santri'));
                const asramaNama = this.getAttribute('data-asrama') || '-';
                const kelasNama = this.getAttribute('data-kelas') || '-';

                document.getElementById('kartu-nama').innerText = santri.nama || '';
                document.getElementById('kartu-nis').innerText = santri.nis || '';
                document.getElementById('kartu-asrama').innerText = asramaNama + (santri.kamar ? ' (' + santri.kamar + ')' : '');
                document.getElementById('kartu-kelas').innerText = kelasNama;

                // Pas Foto Santri
                const fotoContainer = document.getElementById('kartu-foto-container');
                if (fotoContainer) {
                    if (santri.foto_url) {
                        fotoContainer.innerHTML = `<img src="${santri.foto_url}" alt="${santri.nama}" class="w-full h-full object-cover">`;
                    } else {
                        const inisial = (santri.nama || 'S').trim().charAt(0).toUpperCase();
                        fotoContainer.innerHTML = `
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-emerald-800 to-teal-950 text-white p-1 text-center">
                                <span class="text-xl font-bold text-amber-300">${inisial}</span>
                                <span class="text-[8px] text-emerald-300/80 mt-1">Pas Foto</span>
                            </div>
                        `;
                    }
                }

                // QR Code generator
                const qrContainer = document.getElementById('kartu-qrcode');
                qrContainer.innerHTML = `
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(santri.nis)}" alt="QR ${santri.nis}" class="w-full h-full object-contain">
                `;

                openModal('modal-kartu');
            } catch (e) {
                console.error("Gagal memproses kartu santri:", e);
            }
        });
    });
    // Filter asrama berdasarkan jenis kelamin yang dipilih saat tambah santri
    function filterAsramaByGender() {
        const genderEl = document.getElementById('tambah-jenis-kelamin');
        if (!genderEl) return;
        const gender = genderEl.value;
        const targetGender = (gender === 'L') ? 'putra' : 'putri';
        const asramaSelect = document.getElementById('tambah-asrama-select');
        if (!asramaSelect) return;

        const previousAsramaVal = "{{ old('asrama_id') }}";
        let hasSelected = false;
        asramaSelect.querySelectorAll('option[data-gender]').forEach(opt => {
            if (opt.getAttribute('data-gender') === targetGender) {
                opt.style.display = '';
                opt.disabled = false;
                if (previousAsramaVal && opt.value === previousAsramaVal) {
                    asramaSelect.value = opt.value;
                    hasSelected = true;
                } else if (!hasSelected) {
                    asramaSelect.value = opt.value;
                    hasSelected = true;
                }
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
            }
        });

        if (!hasSelected) {
            asramaSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        filterAsramaByGender();
        @if ($errors->any())
            openModal('modal-tambah-santri');
        @endif
    });

    // ==========================================
    // SISTEM CHECK BUTTON & HAPUS MASSAL SANTRI
    // ==========================================
    function updateBulkSelectionUI() {
        const checkboxes = document.querySelectorAll('.santri-checkbox');
        const checkedBoxes = document.querySelectorAll('.santri-checkbox:checked');
        const count = checkedBoxes.length;

        const actionBar = document.getElementById('bulk-action-bar');
        const selectedBadge = document.getElementById('bulk-selected-badge');
        const selectedCount = document.getElementById('bulk-selected-count');
        const btnDeleteCount = document.getElementById('btn-delete-count');
        const checkAll = document.getElementById('check-all-santri');

        if (selectedBadge) selectedBadge.innerText = count;
        if (selectedCount) selectedCount.innerText = count;
        if (btnDeleteCount) btnDeleteCount.innerText = count;

        if (count > 0) {
            actionBar.classList.remove('hidden');
        } else {
            actionBar.classList.add('hidden');
        }

        // Highlight table rows yang diceklis
        checkboxes.forEach(cb => {
            const row = document.getElementById('row-santri-' + cb.value);
            if (row) {
                if (cb.checked) {
                    row.classList.add('bg-rose-50/60', 'dark:bg-rose-950/30');
                } else {
                    row.classList.remove('bg-rose-50/60', 'dark:bg-rose-950/30');
                }
            }
        });

        // Update status master checkbox
        if (checkAll && checkboxes.length > 0) {
            checkAll.checked = (count === checkboxes.length);
            checkAll.indeterminate = (count > 0 && count < checkboxes.length);
        }
    }

    // Toggle Semua Checkbox di Halaman
    const checkAllSantriEl = document.getElementById('check-all-santri');
    if (checkAllSantriEl) {
        checkAllSantriEl.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.santri-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            updateBulkSelectionUI();
        });
    }

    // Event listener per checkbox baris
    document.querySelectorAll('.santri-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkSelectionUI();
        });
    });

    // Pilih jumlah tertentu (5, 10, 25, atau angka berapapun)
    window.selectSantriCount = function(n) {
        const checkboxes = document.querySelectorAll('.santri-checkbox');
        checkboxes.forEach((cb, index) => {
            cb.checked = (index < n);
        });
        updateBulkSelectionUI();
    };

    // Terapkan jumlah custom dari input box
    window.applyCustomSelectCount = function() {
        const input = document.getElementById('custom-select-count');
        if (!input) return;
        const val = parseInt(input.value, 10);
        if (isNaN(val) || val <= 0) {
            window.appAlert({
                title: 'Jumlah Tidak Valid',
                message: 'Silakan masukkan jumlah santri yang valid (angka lebih dari 0).',
                icon: 'warning'
            });
            return;
        }
        selectSantriCount(val);
    };

    // Batalkan seluruh pilihan
    window.deselectAllSantri = function() {
        document.querySelectorAll('.santri-checkbox').forEach(cb => {
            cb.checked = false;
        });
        const checkAll = document.getElementById('check-all-santri');
        if (checkAll) {
            checkAll.checked = false;
            checkAll.indeterminate = false;
        }
        updateBulkSelectionUI();
    };

    // Eksekusi Hapus Massal dengan Konfirmasi Modern Global
    window.executeBulkDelete = function() {
        const checkedBoxes = document.querySelectorAll('.santri-checkbox:checked');
        const count = checkedBoxes.length;

        if (count === 0) {
            window.appAlert({
                title: 'Tidak Ada Data Terpilih',
                message: 'Silakan pilih minimal satu data santri yang ingin dihapus terlebih dahulu.',
                icon: 'warning'
            });
            return;
        }

        window.appConfirm({
            title: 'Konfirmasi Penghapusan Massal',
            message: `Apakah Anda yakin ingin menghapus <strong class="text-rose-600 dark:text-rose-400">${count} data santri</strong> yang dipilih?`,
            subtext: `
                <div class="space-y-1.5 font-medium">
                    <p class="font-bold text-rose-700 dark:text-rose-400">⚠️ Tindakan ini akan menghapus:</p>
                    <ul class="list-disc list-inside space-y-1 ml-1 text-slate-600 dark:text-slate-300">
                        <li>Data identitas & nomor induk santri</li>
                        <li>File pas foto & QR Code resmi santri</li>
                        <li>Riwayat absensi dan sesi presensi terkait</li>
                    </ul>
                    <p class="pt-1 text-[11px] text-rose-600 dark:text-rose-400 font-bold uppercase tracking-wider">Tindakan ini permanen & tidak dapat dibatalkan</p>
                </div>
            `,
            icon: 'danger',
            confirmText: `Ya, Hapus ${count} Santri`,
            cancelText: 'Batal',
        }).then(confirmed => {
            if (confirmed) {
                const container = document.getElementById('bulk-delete-inputs');
                container.innerHTML = '';

                checkedBoxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    container.appendChild(input);
                });

                document.getElementById('form-bulk-delete').submit();
            }
        });
    };

    // ==========================================
    // FACE AI RECOGNITION ENROLLMENT
    // ==========================================
    let faceEnrollStream = null;
    let faceEnrollInterval = null;
    let currentEnrollSantriId = null;
    let currentEnrollDescriptor = null;
    let faceApiModelsLoaded = false;
    let currentSantriBtn = null;

    async function ensureFaceApiLoaded() {
        if (faceApiModelsLoaded) return true;
        const loadingEl = document.getElementById('face-enroll-loading');
        const loadingText = document.getElementById('face-enroll-loading-text');
        if (loadingEl) loadingEl.classList.remove('hidden');
        if (loadingText) loadingText.textContent = 'Memuat model AI Face Recognition...';

        try {
            const LOCAL_MODELS = '{{ asset("models/face-api") }}';
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(LOCAL_MODELS),
                faceapi.nets.faceLandmark68Net.loadFromUri(LOCAL_MODELS),
                faceapi.nets.faceRecognitionNet.loadFromUri(LOCAL_MODELS),
            ]);
            faceApiModelsLoaded = true;
            if (loadingEl) loadingEl.classList.add('hidden');
            return true;
        } catch (e) {
            console.warn('Fallback CDN untuk model face-api:', e);
            try {
                const CDN_MODELS = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(CDN_MODELS),
                    faceapi.nets.faceLandmark68Net.loadFromUri(CDN_MODELS),
                    faceapi.nets.faceRecognitionNet.loadFromUri(CDN_MODELS),
                ]);
                faceApiModelsLoaded = true;
                if (loadingEl) loadingEl.classList.add('hidden');
                return true;
            } catch (err) {
                console.error('Fatal load face models:', err);
                if (loadingText) loadingText.textContent = 'Gagal memuat AI wajah. Periksa koneksi internet / file model.';
                return false;
            }
        }
    }

    document.querySelectorAll('.btn-rekam-wajah').forEach(btn => {
        btn.addEventListener('click', async function() {
            currentSantriBtn = this;
            const santriId = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const nis = this.getAttribute('data-nis');
            const foto = this.getAttribute('data-foto');
            const hasFace = this.getAttribute('data-has-face') === '1';

            currentEnrollSantriId = santriId;
            currentEnrollDescriptor = null;

            document.getElementById('face-modal-santri-nama').textContent = nama;
            document.getElementById('face-modal-santri-nis').textContent = nis;

            const btnDelete = document.getElementById('btn-delete-face');
            if (hasFace) {
                btnDelete.classList.remove('hidden');
            } else {
                btnDelete.classList.add('hidden');
            }

            const photoSection = document.getElementById('face-from-photo-section');
            const photoPreview = document.getElementById('face-photo-preview');
            if (foto && foto.length > 5) {
                photoPreview.src = foto;
                photoPreview.classList.remove('hidden');
                photoSection.classList.remove('hidden');
            } else {
                photoPreview.src = '';
                photoSection.classList.add('hidden');
            }

            const btnSave = document.getElementById('btn-save-face');
            btnSave.disabled = true;

            openModal('modal-rekam-wajah');

            const loaded = await ensureFaceApiLoaded();
            if (loaded) {
                startFaceEnrollCamera();
            }
        });
    });

    async function startFaceEnrollCamera() {
        const video = document.getElementById('face-enroll-video');
        const canvas = document.getElementById('face-enroll-canvas');
        const statusText = document.getElementById('face-detect-status');
        const indicator = document.getElementById('face-detect-indicator');
        const badge = document.getElementById('face-quality-badge');
        const btnSave = document.getElementById('btn-save-face');

        try {
            stopFaceEnrollCamera();
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }
            });
            faceEnrollStream = stream;
            video.srcObject = stream;
            await video.play();

            statusText.textContent = 'Mendeteksi wajah... Hadapkan wajah ke kamera';
            indicator.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse';

            const displaySize = { width: video.videoWidth || 640, height: video.videoHeight || 480 };
            faceapi.matchDimensions(canvas, displaySize);

            faceEnrollInterval = setInterval(async () => {
                if (!video || video.paused || video.ended || !faceApiModelsLoaded) return;

                const detection = await faceapi.detectSingleFace(
                    video,
                    new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                ).withFaceLandmarks().withFaceDescriptor();

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detection) {
                    const resized = faceapi.resizeResults(detection, displaySize);
                    faceapi.draw.drawDetections(canvas, resized);
                    faceapi.draw.drawFaceLandmarks(canvas, resized);

                    currentEnrollDescriptor = Array.from(detection.descriptor);
                    statusText.textContent = '✅ Wajah terdeteksi jelas! Siap disimpan.';
                    indicator.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500';
                    badge.textContent = `Score: ${(detection.detection.score * 100).toFixed(0)}%`;
                    badge.className = 'font-mono text-[11px] px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold';
                    btnSave.disabled = false;
                } else {
                    currentEnrollDescriptor = null;
                    statusText.textContent = 'Posisikan wajah tepat di tengah kamera';
                    indicator.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse';
                    badge.textContent = 'Mencari...';
                    badge.className = 'font-mono text-[11px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400';
                    btnSave.disabled = true;
                }
            }, 250);
        } catch (err) {
            console.error('Kamera error:', err);
            statusText.textContent = 'Izin kamera ditolak atau tidak ditemukan.';
            indicator.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
        }
    }

    function stopFaceEnrollCamera() {
        if (faceEnrollInterval) {
            clearInterval(faceEnrollInterval);
            faceEnrollInterval = null;
        }
        if (faceEnrollStream) {
            faceEnrollStream.getTracks().forEach(t => t.stop());
            faceEnrollStream = null;
        }
        const video = document.getElementById('face-enroll-video');
        if (video) video.srcObject = null;
        const canvas = document.getElementById('face-enroll-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    window.closeFaceEnrollModal = function() {
        stopFaceEnrollCamera();
        closeModal('modal-rekam-wajah');
    };

    window.saveEnrolledFace = async function() {
        if (!currentEnrollSantriId || !currentEnrollDescriptor || currentEnrollDescriptor.length !== 128) {
            window.appAlert({ title: 'Wajah Belum Terdeteksi', message: 'Pastikan wajah santri terlihat jelas di layar kamera.', icon: 'warning' });
            return;
        }

        const btnSave = document.getElementById('btn-save-face');
        btnSave.disabled = true;
        btnSave.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></span> Menyimpan...`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const resp = await fetch(`/santri/${currentEnrollSantriId}/face-descriptor`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ face_descriptor: currentEnrollDescriptor })
            });

            const data = await resp.json();

            if (resp.ok && data.success) {
                window.appAlert({
                    title: 'Berhasil Merekam Wajah!',
                    message: data.message || 'Fitur wajah santri berhasil disimpan untuk presensi AI.',
                    icon: 'success'
                });

                if (currentSantriBtn) {
                    currentSantriBtn.setAttribute('data-has-face', '1');
                    currentSantriBtn.className = 'btn-rekam-wajah p-1.5 rounded-lg text-blue-700 dark:text-blue-400 bg-blue-50/80 dark:bg-blue-950/60 border-blue-300 dark:border-blue-700 font-semibold text-xs flex items-center gap-1 border transition';
                    currentSantriBtn.innerHTML = `<span>👤</span><span>Wajah ✅</span>`;
                }

                closeFaceEnrollModal();
            } else {
                throw new Error(data.message || 'Gagal menyimpan wajah');
            }
        } catch (err) {
            window.appAlert({ title: 'Gagal Menyimpan', message: err.message, icon: 'danger' });
            btnSave.disabled = false;
            btnSave.innerHTML = `<span>Simpan Wajah</span>`;
        }
    };

    window.deleteSantriFace = async function() {
        if (!currentEnrollSantriId) return;

        window.appConfirm({
            title: 'Hapus Data Wajah?',
            message: 'Apakah Anda yakin ingin menghapus data wajah santri ini dari sistem presensi AI?',
            icon: 'warning',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal'
        }).then(async confirmed => {
            if (!confirmed) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const resp = await fetch(`/santri/${currentEnrollSantriId}/face-descriptor`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await resp.json();
                if (resp.ok && data.success) {
                    window.appAlert({ title: 'Dihapus', message: data.message, icon: 'success' });
                    if (currentSantriBtn) {
                        currentSantriBtn.setAttribute('data-has-face', '0');
                        currentSantriBtn.className = 'btn-rekam-wajah p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border-slate-200 dark:border-slate-700 text-xs flex items-center gap-1 border transition';
                        currentSantriBtn.innerHTML = `<span>👤</span><span>Wajah</span>`;
                    }
                    closeFaceEnrollModal();
                }
            } catch (err) {
                window.appAlert({ title: 'Gagal', message: err.message, icon: 'danger' });
            }
        });
    };

    window.extractFaceFromExistingPhoto = async function() {
        const photoPreview = document.getElementById('face-photo-preview');
        const statusText = document.getElementById('face-detect-status');
        const badge = document.getElementById('face-quality-badge');
        const btnSave = document.getElementById('btn-save-face');

        if (!photoPreview.src) return;

        statusText.textContent = 'Mengekstrak fitur wajah dari pas foto...';
        badge.textContent = 'Memproses...';

        try {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.src = photoPreview.src;
            await img.decode();

            const detection = await faceapi.detectSingleFace(
                img,
                new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.4 })
            ).withFaceLandmarks().withFaceDescriptor();

            if (detection) {
                currentEnrollDescriptor = Array.from(detection.descriptor);
                statusText.textContent = '✅ Berhasil mengekstrak wajah dari foto!';
                badge.textContent = `Foto: ${(detection.detection.score * 100).toFixed(0)}%`;
                badge.className = 'font-mono text-[11px] px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold';
                btnSave.disabled = false;
                window.appAlert({ title: 'Wajah Terdeteksi', message: 'Wajah berhasil dideteksi dari foto profil santri! Klik tombol "Simpan Wajah" untuk mendaftarkan.', icon: 'success' });
            } else {
                statusText.textContent = 'Wajah pada foto tidak terdeteksi atau resolusi kurang jelas.';
                badge.textContent = 'Gagal';
                window.appAlert({ title: 'Gagal Deteksi Foto', message: 'AI tidak dapat mendeteksi wajah jelas pada foto profil ini. Silakan gunakan kamera langsung.', icon: 'warning' });
            }
        } catch (err) {
            console.error(err);
            statusText.textContent = 'Gagal memproses foto.';
        }
    };
</script>
@endpush
@endsection