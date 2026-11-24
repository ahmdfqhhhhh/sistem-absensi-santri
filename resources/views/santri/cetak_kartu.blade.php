<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Tanda Santri Ber-QR Massal - PP. Miftahul Huda Kroya</title>
    <!-- Tailwind CSS CDN for high fidelity rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk Switch Mode Tampilan Kartu / QR Code -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 6mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .kartu-container {
                gap: 6mm 6mm !important;
                padding: 0 !important;
            }
            .kartu-item {
                break-inside: avoid;
                page-break-inside: avoid;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }

        .kartu-pvc {
            width: 96mm;
            height: 60mm;
            box-sizing: border-box;
            background: linear-gradient(135deg, #064e3b 0%, #065f46 45%, #042f2e 100%);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
</head>
<body x-data="{ viewMode: 'kartu' }" class="bg-slate-100 text-slate-800 font-sans min-h-screen">

    {{-- Top Filter & Print Toolbar (Hidden on Print) --}}
    <header class="no-print sticky top-0 z-50 bg-slate-900/95 backdrop-blur-md text-white border-b border-slate-800 shadow-xl px-4 py-3">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <a href="{{ route('santri.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Kembali ke Master Data</span>
                </a>
                <div>
                    <h1 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>🖨️ Cetak & Download Kartu Santri Massal</span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            {{ $santris->count() }} Santri Siap Cetak
                        </span>
                    </h1>
                </div>
            </div>

            {{-- Mode Tampilan Switcher --}}
            <div class="inline-flex p-0.5 rounded-xl bg-slate-800 border border-slate-700 text-xs shrink-0">
                <button type="button" @click="viewMode = 'kartu'" :class="viewMode === 'kartu' ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <span>🪪</span>
                    <span>Desain Kartu PVC</span>
                </button>
                <button type="button" @click="viewMode = 'qr'" :class="viewMode === 'qr' ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <span>🔳</span>
                    <span>Khusus QR Code Saja</span>
                </button>
            </div>

            {{-- Filter Form & Action Buttons --}}
            <form method="GET" action="{{ route('santri.cetak-kartu') }}" class="flex flex-wrap items-center gap-2 text-xs">
                {{-- Filter Kelas --}}
                <select name="kelas_id" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelases as $kls)
                        <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                            {{ $kls->nama_kelas }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Asrama --}}
                <select name="asrama_id" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500">
                    <option value="">-- Semua Asrama --</option>
                    @foreach($asramas as $asr)
                        <option value="{{ $asr->id }}" {{ request('asrama_id') == $asr->id ? 'selected' : '' }}>
                            {{ $asr->nama_asrama }} ({{ ucfirst($asr->gender) }})
                        </option>
                    @endforeach
                </select>

                {{-- Filter Gender --}}
                @if(!$lockedGender)
                    <select name="gender" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white focus:ring-1 focus:ring-emerald-500">
                        <option value="">Semua Santri</option>
                        <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Putra (L)</option>
                        <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Putri (P)</option>
                    </select>
                @endif

                {{-- Cari Nama / NIS --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NIS..." class="px-2.5 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-400 w-36 focus:ring-1 focus:ring-emerald-500">

                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-semibold transition">
                    Cari
                </button>

                @if(request()->hasAny(['kelas_id', 'asrama_id', 'gender', 'search']))
                    <a href="{{ route('santri.cetak-kartu') }}" class="px-2.5 py-1.5 rounded-lg bg-rose-950/60 text-rose-300 border border-rose-800 hover:bg-rose-900 transition">
                        Reset
                    </a>
                @endif

                {{-- Action Download ZIP QR Code (Massal) --}}
                <a href="{{ route('santri.download-qr-zip', request()->all()) }}" class="px-3.5 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold flex items-center gap-1.5 shadow-lg shadow-teal-700/30 active:scale-95 transition ml-1" title="Unduh seluruh file QR Code santri terfilter dalam 1 file ZIP (Format SVG Vector resolusi tajam dengan penamaan [NIS] - [Nama Santri].svg)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Unduh Semua QR (ZIP)</span>
                </a>

                {{-- Action Print Button --}}
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold flex items-center gap-1.5 shadow-lg shadow-emerald-700/30 active:scale-95 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </form>
        </div>

        {{-- Print notice & download info banner --}}
        <div class="max-w-7xl mx-auto mt-2 text-[11px] text-slate-300 flex flex-wrap items-center justify-between gap-2 border-t border-slate-800/80 pt-1.5">
            <div class="flex items-center gap-1.5 text-amber-300">
                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Tips Cetak: Destination: <strong>Save as PDF</strong>, Paper: <strong>A4</strong>, dan centang <strong>"Background graphics"</strong>.</span>
            </div>
            <div class="flex items-center gap-1.5 text-emerald-300 font-medium">
                <span>💡 <strong>Desain Sendiri:</strong> Klik tombol <strong>"Unduh Semua QR (ZIP)"</strong> untuk mendapatkan file SVG vector berkualitas tinggi dengan format nama <code>[NIS] - [Nama Santri].svg</code> siap pakai di CorelDraw, Photoshop, Canva, atau Illustrator.</span>
            </div>
        </div>
    </header>

    {{-- Main Printable Content --}}
    <main class="max-w-[210mm] mx-auto p-4 sm:p-6">
        @if($santris->isEmpty())
            <div class="no-print bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm mt-8">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-3xl mx-auto mb-3">📭</div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Data Santri Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tidak ada data santri yang cocok dengan filter kelas/asrama yang Anda pilih. Silakan sesuaikan kembali filter di atas.</p>
                <a href="{{ route('santri.cetak-kartu') }}" class="inline-block mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-xs shadow">Reset Filter</a>
            </div>
        @else
            {{-- Mode 1: Kartu PVC Resmi Lengkap --}}
            <div x-show="viewMode === 'kartu'" class="kartu-container grid grid-cols-1 md:grid-cols-2 gap-4 justify-items-center">
                @foreach($santris as $s)
                    <div class="kartu-item kartu-pvc rounded-2xl border-2 border-amber-400/50 shadow-md p-3.5 flex flex-col justify-between text-white relative overflow-hidden">
                        {{-- Background Accent Glow --}}
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-emerald-400/10 blur-xl pointer-events-none"></div>
                        <div class="absolute -left-6 -top-6 w-20 h-20 rounded-full bg-amber-400/10 blur-lg pointer-events-none"></div>

                        {{-- Card Header --}}
                        <div class="flex items-center space-x-2.5 pb-2 border-b border-emerald-700/60 relative z-10">
                            <div class="w-9 h-9 rounded-full bg-white p-0.5 shrink-0 flex items-center justify-center shadow-sm ring-1 ring-amber-400/70 overflow-hidden">
                                <img src="{{ asset('images/logo-pesantren.png') }}" alt="Logo" class="w-full h-full object-contain" onerror="this.src='https://ui-avatars.com/api/?name=MH&background=064e3b&color=fff'">
                            </div>
                            <div class="min-w-0 flex-1 leading-tight">
                                <h4 class="font-extrabold text-[11px] tracking-wide text-white uppercase truncate">PONDOK PESANTREN MIFTAHUL HUDA KROYA</h4>
                                <p class="text-[9px] text-amber-300 font-semibold tracking-wider uppercase">KARTU IDENTITAS RESMI SANTRI</p>
                            </div>
                        </div>

                        {{-- Card Body: Foto + Info + QR Code --}}
                        <div class="flex items-center gap-2.5 my-auto py-1 relative z-10">
                            {{-- 1. Foto Santri --}}
                            <div class="w-[20mm] h-[26mm] rounded-lg border-2 border-amber-400/80 bg-emerald-950/80 shrink-0 overflow-hidden flex items-center justify-center shadow-sm">
                                @if($s->foto_url)
                                    <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-emerald-800 to-teal-950 text-white p-1 text-center">
                                        <span class="text-xs font-bold text-amber-300">{{ substr($s->nama, 0, 1) }}</span>
                                        <span class="text-[7px] text-emerald-300/80 mt-0.5">Pas Foto</span>
                                    </div>
                                @endif
                            </div>

                            {{-- 2. Detail Data Santri --}}
                            <div class="flex-1 min-w-0 space-y-0.5 text-left">
                                <div>
                                    <p class="text-[8px] uppercase tracking-wider text-emerald-300 font-bold">Nama Lengkap</p>
                                    <h5 class="font-extrabold text-[12px] text-white leading-tight truncate" title="{{ $s->nama }}">
                                        {{ $s->nama }}
                                    </h5>
                                </div>
                                <div>
                                    <p class="text-[8px] uppercase tracking-wider text-emerald-300 font-bold">Nomor Induk (NIS)</p>
                                    <p class="font-mono font-bold text-[11px] text-amber-300 tracking-wider">
                                        {{ $s->nis }}
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-1 pt-0.5 text-[9px] text-emerald-100">
                                    <div class="truncate">
                                        <span class="block text-[7.5px] text-emerald-400 font-semibold">Asrama / Kamar</span>
                                        <span class="font-medium text-white truncate block">{{ $s->asrama->nama_asrama ?? '-' }} {{ $s->kamar ? '('.$s->kamar.')' : '' }}</span>
                                    </div>
                                    <div class="truncate">
                                        <span class="block text-[7.5px] text-emerald-400 font-semibold">Kelas</span>
                                        <span class="font-medium text-white truncate block">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. QR Code Santri Resmi --}}
                            <div class="w-[23mm] bg-white p-1 rounded-lg border border-amber-400/80 shadow-md shrink-0 flex flex-col items-center justify-center">
                                <div class="w-[18mm] h-[18mm] flex items-center justify-center">
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(68)->margin(0)->generate($s->nis) !!}
                                </div>
                                <span class="text-[6.5px] font-black tracking-wider text-slate-800 uppercase mt-0.5">SCAN ABSENSI</span>
                                <a href="{{ route('santri.download-qr', $s->id) }}" class="no-print mt-1 inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-emerald-700 hover:bg-emerald-600 text-[7px] font-bold text-white shadow-xs transition" title="Unduh file QR {{ $s->nis }} - {{ $s->nama }}.svg">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh QR</span>
                                </a>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="pt-1 border-t border-emerald-800/80 flex items-center justify-between text-[7.5px] text-emerald-300/80 relative z-10 leading-tight">
                            <span>Scan pada setiap sesi absensi sholat & madrasah</span>
                            <span class="font-bold text-amber-300 uppercase tracking-wider">E-ABSENSI SANTRI</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Mode 2: Khusus QR Code & Label Nama (Grid Bersih untuk Desain Mandiri / Stiker) --}}
            <div x-show="viewMode === 'qr'" x-cloak class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($santris as $s)
                    <div class="kartu-item bg-white rounded-2xl border border-slate-200/90 shadow-sm p-3.5 flex flex-col items-center text-center justify-between hover:border-emerald-500 hover:shadow-md transition group">
                        <div class="w-28 h-28 p-2 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center mb-2 shadow-inner">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(96)->margin(0)->generate($s->nis) !!}
                        </div>
                        <div class="w-full">
                            <h5 class="text-xs font-bold text-slate-900 truncate leading-tight" title="{{ $s->nama }}">{{ $s->nama }}</h5>
                            <p class="font-mono text-[11px] font-bold text-emerald-700 mt-0.5">{{ $s->nis }}</p>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ $s->asrama->nama_asrama ?? '-' }} · {{ $s->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                        <div class="no-print mt-2.5 pt-2 border-t border-slate-100 w-full flex items-center justify-center gap-1.5">
                            <a href="{{ route('santri.download-qr', $s->id) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold shadow-xs transition active:scale-95" title="Unduh file SVG {{ $s->nis }} - {{ $s->nama }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh QR (SVG)</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>
