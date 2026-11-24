@extends('layouts.app')

@section('title', 'Surat Izin Digital - ' . ($perizinan->nomor_surat ?? 'Santri'))

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <!-- Back & Print Buttons (Hidden during Print) -->
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('perizinan.index') }}" class="inline-flex items-center space-x-1 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
            <span>&larr; Kembali ke Daftar Perizinan</span>
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Surat Izin</span>
        </button>
    </div>

    <!-- Surat Izin Digital Document Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 md:p-10 border border-slate-200 dark:border-slate-800 shadow-xl relative overflow-hidden print:bg-white print:border-none print:shadow-none print:p-0">
        <!-- Letterhead (Kop Surat Resmi Pesantren) -->
        <div class="pb-2 relative">
            <div class="flex items-center space-x-3 md:space-x-5">
                <!-- Logo Pesantren Resmi -->
                <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo Pondok Pesantren Miftahul Huda Kroya" class="w-20 h-20 md:w-24 md:h-24 object-contain shrink-0">

                <!-- Teks Kop Surat -->
                <div class="flex-1 text-center pr-2">
                    <!-- Tulisan Arab -->
                    <p class="font-bold text-xl md:text-2xl text-slate-900 dark:text-white print:text-black leading-tight" dir="rtl" style="font-family: 'Amiri', 'Traditional Arabic', 'Scheherazade New', serif; font-size: 1.5rem;">
                        معهد مفتاح الهدى الإسلامى النهضى
                    </p>

                    <!-- PONDOK PESANTREN -->
                    <p class="font-extrabold text-xs md:text-sm uppercase tracking-wider text-[#e36f1f] print:text-[#e36f1f] mt-0.5" style="color: #e36f1f; font-weight: 800; letter-spacing: 0.06em;">
                        PONDOK PESANTREN
                    </p>

                    <!-- MIFTAHUL HUDA KROYA -->
                    <h2 class="font-black text-xl md:text-2xl uppercase tracking-wide text-[#0d780e] print:text-[#0d780e] leading-tight" style="color: #0d780e; font-weight: 900;">
                        MIFTAHUL HUDA KROYA
                    </h2>

                    <!-- Alamat Lengkap & No. HP -->
                    <p class="text-[10px] md:text-xs font-bold text-slate-900 dark:text-slate-100 print:text-black mt-1 leading-snug">
                        Jl. Merak 30 Bajing Kulon, Kroya, Cilacap, Jawa Tengah 53282 Hp: 081390751800
                    </p>
                </div>
            </div>

            <!-- Garis Ganda Kop Surat Resmi (Garis Atas Tebal, Bawah Tipis) -->
            <div class="mt-2.5">
                <div class="border-b-[3px] border-slate-900 dark:border-white print:border-black"></div>
                <div class="border-b border-slate-900 dark:border-white print:border-black mt-[2px]"></div>
            </div>
        </div>

        <!-- Surat Title -->
        <div class="text-center my-6">
            <h1 class="text-lg md:text-xl font-black uppercase text-slate-900 dark:text-white print:text-slate-900 underline tracking-wide">SURAT KETERANGAN IZIN SANTRI</h1>
            <p class="font-mono text-xs font-bold text-slate-600 dark:text-slate-400 print:text-slate-600 mt-1">Nomor: {{ $perizinan->nomor_surat }}</p>
        </div>

        <!-- Surat Body -->
        <div class="space-y-4 text-xs md:text-sm text-slate-700 dark:text-slate-300 print:text-slate-700 leading-relaxed">
            <p>Yang bertanda tangan di bawah ini Pengurus Pengasuhan Pondok Pesantren menerangkan bahwa santri berikut:</p>

            <div class="bg-slate-50/80 dark:bg-slate-800/80 print:bg-slate-50 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-700 print:border-slate-200 space-y-2">
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Nama Lengkap</span>
                    <span class="col-span-2 font-bold text-slate-900 dark:text-white print:text-slate-900">: {{ $perizinan->santri->nama ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">NIS / ID Santri</span>
                    <span class="col-span-2 font-mono font-semibold text-slate-800 dark:text-slate-200 print:text-slate-800">: {{ $perizinan->santri->nis ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Asrama & Kamar</span>
                    <span class="col-span-2 text-slate-800 dark:text-slate-200 print:text-slate-800">: {{ $perizinan->santri->asrama->nama_asrama ?? '-' }} ({{ $perizinan->santri->kamar ?? 'Kamar -' }})</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Kelas</span>
                    <span class="col-span-2 text-slate-800 dark:text-slate-200 print:text-slate-800">: {{ $perizinan->santri->kelas->nama_kelas ?? '-' }}</span>
                </div>
            </div>

            <p>Telah diberikan izin dengan rincian sebagai berikut:</p>

            <div class="bg-blue-50/50 dark:bg-blue-950/40 print:bg-blue-50/50 p-4 rounded-2xl border border-blue-200/60 dark:border-blue-900/60 print:border-blue-200/60 space-y-2">
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Keperluan Izin</span>
                    <span class="col-span-2 font-bold text-blue-900 dark:text-blue-300 print:text-blue-900">: {{ $perizinan->keperluan }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Jenis Izin</span>
                    <span class="col-span-2 font-semibold text-slate-800 dark:text-slate-200 print:text-slate-800">: {{ ucfirst(str_replace('_', ' ', $perizinan->jenis_izin)) }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Waktu Mulai Izin</span>
                    <span class="col-span-2 font-medium text-slate-800 dark:text-slate-200 print:text-slate-800">: {{ $perizinan->tanggal_mulai ? $perizinan->tanggal_mulai->translatedFormat('d F Y H:i') : '-' }} WIB</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Batas Waktu Kembali</span>
                    <span class="col-span-2 font-bold text-rose-700 dark:text-rose-400 print:text-rose-700">: {{ $perizinan->tanggal_selesai ? $perizinan->tanggal_selesai->translatedFormat('d F Y H:i') : '-' }} WIB</span>
                </div>
                @if($perizinan->penjemput_nama)
                    <div class="grid grid-cols-3">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Penjemput</span>
                        <span class="col-span-2 text-slate-800 dark:text-slate-200 print:text-slate-800">: {{ $perizinan->penjemput_nama }} ({{ $perizinan->penjemput_hubungan }})</span>
                    </div>
                @endif
                <div class="grid grid-cols-3">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Disetujui Oleh</span>
                    <span class="col-span-2 font-bold text-emerald-800 dark:text-emerald-400 print:text-emerald-800">: {{ $perizinan->nama_pemberi_izin ?? $perizinan->approvedBy->name ?? 'Pengurus / Pengasuhan Pondok' }}</span>
                </div>
            </div>

            <p>Demikian surat izin ini diterbitkan secara digital. Santri <strong>WAJIB memindai QR Code ini atau Kartu Santri di Pos Keamanan Gerbang</strong> saat keluar dan <strong>wajib scan QR lagi saat KEMBALI</strong> ke pondok pesantren sebagai bukti sah kepulangan.</p>

            @if($perizinan->tanggal_kembali_aktual)
                <div class="mt-4 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border-2 border-emerald-500 text-emerald-900 dark:text-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-sm">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl leading-none">✅</span>
                        <div>
                            <p class="font-black text-sm uppercase tracking-wide text-emerald-800 dark:text-emerald-300">BUKTI KEPULANGAN SAH TERVERIFIKASI</p>
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">
                                Santri telah melakukan <strong>Scan QR Kembali</strong> di Pos Kamtib pada:
                                <span class="font-bold underline">{{ \Carbon\Carbon::parse($perizinan->tanggal_kembali_aktual)->translatedFormat('l, d F Y H:i') }} WIB</span>
                            </p>
                            @if($perizinan->status === 'terlambat_kembali')
                                <p class="text-[11px] font-semibold text-rose-600 dark:text-rose-400 mt-1">
                                    ⚠️ Catatan: Santri kembali melewati batas waktu izin (Terlambat Kembali).
                                </p>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-mono font-bold text-[11px] shadow shrink-0">
                        VERIFIED RETURN
                    </span>
                </div>
            @elseif($perizinan->status === 'sedang_berlangsung')
                <div class="mt-4 p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200 flex items-center space-x-3 text-xs">
                    <span class="text-xl">⚠️</span>
                    <div>
                        <p class="font-bold text-xs uppercase tracking-wide text-amber-800 dark:text-amber-300">STATUS: SANTRI SEDANG DI LUAR PESANTREN</p>
                        <p class="text-[11px] text-amber-700 dark:text-amber-400">Saat santri tiba di pondok, wajib langsung menghadap Petugas Kamtib untuk scan QR bukti kepulangan.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- QR Code Verification Section -->
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 print:border-slate-200 flex flex-col items-center justify-center text-center">
            <!-- Digital QR Stempel -->
            <div class="flex flex-col items-center p-4 rounded-2xl border border-slate-200 dark:border-slate-700 print:border-slate-200 bg-slate-50/50 dark:bg-slate-800/50 print:bg-slate-50 max-w-xs w-full shadow-sm">
                <div class="bg-white p-2.5 rounded-xl shadow-sm border border-slate-200" style="width: 140px; height: 140px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($perizinan->qr_token) }}" alt="QR Verifikasi Kamtib" class="w-full h-full object-contain">
                </div>
                <p class="font-mono text-[11px] font-bold text-slate-700 dark:text-slate-300 print:text-slate-700 mt-2.5">TOKEN: {{ substr($perizinan->qr_token, 0, 16) }}...</p>
                <span class="text-[10px] text-emerald-700 dark:text-emerald-400 print:text-emerald-700 font-bold uppercase mt-0.5">Scan di Pos Keamanan Gerbang</span>
                <span class="text-[9px] text-slate-400 dark:text-slate-500 print:text-slate-500 mt-0.5">Valid & Terverifikasi Sistem Resmi Pesantren</span>
            </div>
        </div>
    </div>
</div>
@endsection
