<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Absensi - {{ $bulanNama }} {{ $tahun }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.2cm 1.5cm 1.2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            line-height: 1.3;
        }
        .no-print {
            background: #0f172a;
            color: #ffffff;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: Arial, sans-serif;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .btn-print {
            background: #059669;
            color: white;
            border: none;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-close {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid #475569;
            padding: 8px 16px;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 9pt; }
        }

        /* Kop Surat Resmi Pesantren */
        .kop-container {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px double #000000;
            padding-bottom: 8px;
            margin-bottom: 15px;
            position: relative;
        }
        .kop-logo {
            position: absolute;
            left: 0;
            top: 2px;
            width: 70px;
            height: 70px;
        }
        .kop-text {
            text-align: center;
            margin: 0 auto;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 14pt;
            letter-spacing: 1px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text h1 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: 900;
            letter-spacing: 1.5px;
        }
        .kop-text p {
            margin: 0;
            font-size: 8.5pt;
            font-family: Arial, sans-serif;
            color: #374151;
        }

        .judul-surat {
            text-align: center;
            margin-bottom: 15px;
        }
        .judul-surat h3 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .judul-surat p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            font-family: Arial, sans-serif;
            color: #4b5563;
        }

        /* Tabel Presensi */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8.5pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #374151;
            padding: 5px 6px;
        }
        table.data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Tanda Tangan */
        .ttd-container {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 42%;
            text-align: center;
            font-size: 9.5pt;
        }
        .ttd-space {
            height: 70px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div>
            <strong>Preview Cetak PDF:</strong> Rekapitulasi Presensi {{ $bulanNama }} {{ $tahun }}
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" onclick="window.close()" class="btn-close">Tutup</button>
            <button type="button" onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    <div style="padding: 10px 15px;">
        <!-- Kop Surat -->
        <div class="kop-container">
            <img src="{{ asset('images/logo-pesantren.png') }}" class="kop-logo" alt="Logo PPMH" onerror="this.style.display='none'">
            <div class="kop-text">
                <h2>YAYASAN PONDOK PESANTREN</h2>
                <h1>MIFTAHUL HUDA KROYA</h1>
                <p>Jl. Raya Kroya, Bajing Kulon, Kec. Kroya, Kab. Cilacap, Jawa Tengah 53282</p>
                <p>Email: sekretariat@ppmh-kroya.ponpes.id &bull; Telp/WA: 0812-3456-7890</p>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="judul-surat">
            <h3>REKAPITULASI PRESENSI SANTRI BULANAN</h3>
            <p>Periode: <strong>{{ $bulanNama }} {{ $tahun }}</strong> | Kategori: <strong>Santri {{ ucfirst($gender) }}</strong> | Total: <strong>{{ count($rekapList) }} Santri</strong></p>
        </div>

        <!-- Ringkasan Singkat -->
        <div style="margin-bottom: 10px; font-size: 8.5pt; font-family: Arial, sans-serif;">
            <strong>Ringkasan Total Sesi Presensi:</strong>
            Hadir: <strong>{{ $summary['hadir'] }}</strong> &bull;
            Terlambat: <strong>{{ $summary['terlambat'] }}</strong> &bull;
            Izin: <strong>{{ $summary['izin'] }}</strong> &bull;
            Sakit: <strong>{{ $summary['sakit'] }}</strong> &bull;
            Alpa: <strong>{{ $summary['alpa'] }}</strong> &bull;
            Total Sesi: <strong>{{ $summary['total_sesi'] }}</strong>
        </div>

        <!-- Tabel Data -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 65px;">NIS</th>
                    <th>Nama Lengkap Santri</th>
                    <th style="width: 45px;">Gender</th>
                    <th style="width: 85px;">Asrama</th>
                    <th style="width: 45px;">Kamar</th>
                    <th style="width: 65px;">Kelas</th>
                    <th style="width: 35px;">H</th>
                    <th style="width: 35px;">T</th>
                    <th style="width: 35px;">I</th>
                    <th style="width: 35px;">S</th>
                    <th style="width: 35px;">A</th>
                    <th style="width: 45px;">Total</th>
                    <th style="width: 50px;">% Hadir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapList as $idx => $r)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center font-bold" style="font-family: monospace;">{{ $r['santri']->nis }}</td>
                        <td>{{ $r['santri']->nama }}</td>
                        <td class="text-center">{{ $r['santri']->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                        <td>{{ $r['santri']->asrama->nama_asrama ?? '-' }}</td>
                        <td class="text-center">{{ $r['santri']->kamar ?? '-' }}</td>
                        <td>{{ $r['santri']->kelas->nama_kelas ?? '-' }}</td>
                        <td class="text-center">{{ $r['hadir'] }}</td>
                        <td class="text-center">{{ $r['terlambat'] }}</td>
                        <td class="text-center">{{ $r['izin'] }}</td>
                        <td class="text-center">{{ $r['sakit'] }}</td>
                        <td class="text-center">{{ $r['alpa'] }}</td>
                        <td class="text-center font-bold">{{ $r['total'] }}</td>
                        <td class="text-center font-bold">{{ $r['persentase'] ?? 0 }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center" style="padding: 20px;">
                            Tidak ada data absensi untuk periode bulan {{ $bulanNama }} {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="font-size: 7.5pt; font-style: italic; margin-top: 5px; color: #4b5563;">
            * Keterangan Singkatan: H = Hadir Tepat Waktu, T = Terlambat, I = Izin, S = Sakit, A = Alpa/Tanpa Keterangan.
        </div>

        <!-- Bagian Pengesahan Tanda Tangan -->
        <div class="ttd-container">
            <div class="ttd-box">
                <p>Mengetahui,</p>
                <p class="font-bold">Ustadz Musyrif / Pembina Presensi</p>
                <div class="ttd-space"></div>
                <p class="font-bold"><u>( .................................................. )</u></p>
                <p style="font-size: 8pt; color: #6b7280;">NIP / ID Musyrif</p>
            </div>
            <div class="ttd-box">
                <p>Kroya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold">Pengasuh Pondok Pesantren</p>
                <div class="ttd-space"></div>
                <p class="font-bold"><u>( KH. Pengasuh PPMH Kroya )</u></p>
                <p style="font-size: 8pt; color: #6b7280;">Pengasuh PP. Miftahul Huda Kroya</p>
            </div>
        </div>
    </div>
</body>
</html>