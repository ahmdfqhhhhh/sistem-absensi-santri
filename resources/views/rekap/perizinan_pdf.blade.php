<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Perizinan - {{ $bulanNama }} {{ $tahun }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1cm 1.2cm 1cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 9.5pt;
            line-height: 1.25;
        }
        .no-print {
            background: #0f172a;
            color: #ffffff;
            padding: 10px 20px;
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
            padding: 7px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-close {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid #475569;
            padding: 7px 14px;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 8.5pt; }
        }

        /* Kop Surat Resmi Pesantren */
        .kop-container {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px double #000000;
            padding-bottom: 8px;
            margin-bottom: 12px;
            position: relative;
        }
        .kop-logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 65px;
            height: 65px;
        }
        .kop-text {
            text-align: center;
            margin: 0 auto;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 13pt;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #065f46;
        }
        .kop-text h1 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 1px 0;
            font-size: 8pt;
            color: #374151;
        }

        .document-title {
            text-align: center;
            margin-bottom: 12px;
        }
        .document-title h3 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .document-title p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            font-style: italic;
            color: #4b5563;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #111827;
            padding: 4px 5px;
            font-size: 8pt;
        }
        table.data-table th {
            background-color: #f3f4f6;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5pt;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .ttd-container {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 40%;
            text-align: center;
            font-size: 8.5pt;
        }
        .ttd-space {
            height: 55px;
        }
    </style>
</head>
<body>
    <!-- Bar Navigasi Aksi (Hanya di Layar Browser) -->
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-weight: bold; font-size: 13px;">Laporan Rekapitulasi Perizinan Santri Bulanan</span>
            <span style="font-size: 11px; color: #94a3b8;">&bull; Periode {{ $bulanNama }} {{ $tahun }}</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-print">Cetak / Simpan PDF</button>
            <button onclick="window.close()" class="btn-close">Tutup</button>
        </div>
    </div>

    <div style="padding: 10px 15px;">
        <!-- Kop Surat Pesantren -->
        <div class="kop-container">
            <div class="kop-logo">
                <svg viewBox="0 0 100 100" style="width: 100%; height: 100%; fill: #047857;">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#047857" stroke-width="4"/>
                    <path d="M50 15 L80 80 L50 65 L20 80 Z"/>
                </svg>
            </div>
            <div class="kop-text">
                <h2>Yayasan Pondok Pesantren Miftahul Huda Kroya</h2>
                <h1>PONDOK PESANTREN MIFTAHUL HUDA</h1>
                <p>Jalan Kyai Haji Miftah No. 01, Kroya, Cilacap, Jawa Tengah 53282</p>
                <p>Website: www.ppmh-kroya.ac.id &bull; Telp / WA: 0812-3456-7890 &bull; Email: info@ppmh-kroya.ac.id</p>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="document-title">
            <h3>REKAPITULASI SURAT PERIZINAN SANTRI BULANAN</h3>
            <p>Periode: Bulan {{ $bulanNama }} Tahun {{ $tahun }} &bull; Santri {{ ucfirst($gender) }}</p>
        </div>

        <!-- Info Singkat -->
        <table class="meta-table">
            <tr>
                <td style="width: 15%;"><strong>Total Permohonan</strong></td>
                <td style="width: 35%;">: {{ $stats['total'] }} Izin</td>
                <td style="width: 15%;"><strong>Disetujui / Selesai</strong></td>
                <td style="width: 35%;">: {{ $stats['disetujui'] }} disetujui / {{ $stats['selesai'] }} selesai</td>
            </tr>
            <tr>
                <td><strong>Sedang di Luar</strong></td>
                <td>: {{ $stats['sedang_berlangsung'] }} Santri</td>
                <td><strong>Terlambat Kembali</strong></td>
                <td>: {{ $stats['terlambat_kembali'] }} Santri &bull; Ditolak: {{ $stats['ditolak'] }}</td>
            </tr>
        </table>

        <!-- Tabel Data Perizinan -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 90px;">No. Surat</th>
                    <th style="width: 130px;">Nama Santri</th>
                    <th style="width: 65px;">NIS</th>
                    <th style="width: 25px;">L/P</th>
                    <th style="width: 85px;">Asrama / Kamar</th>
                    <th style="width: 75px;">Jenis</th>
                    <th>Keperluan</th>
                    <th style="width: 85px;">Waktu Mulai</th>
                    <th style="width: 85px;">Batas Kembali</th>
                    <th style="width: 95px;">Pemberi Izin</th>
                    <th style="width: 75px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jenisLabels = [
                        'pulang' => 'Pulang',
                        'keluar_komplek' => 'Keluar',
                        'sakit' => 'Sakit',
                        'sambangan_keluarga' => 'Sambangan',
                        'tugas_pondok' => 'Tugas',
                    ];
                    $statusLabels = [
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'sedang_berlangsung' => 'Di Luar',
                        'selesai' => 'Selesai',
                        'terlambat_kembali' => 'Terlambat',
                        'ditolak' => 'Ditolak',
                    ];
                @endphp
                @forelse($perizinans as $idx => $izin)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center font-bold" style="font-family: monospace;">{{ $izin->nomor_surat ?? '-' }}</td>
                        <td>{{ $izin->santri->nama ?? '-' }}</td>
                        <td class="text-center" style="font-family: monospace;">{{ $izin->santri->nis ?? '-' }}</td>
                        <td class="text-center">{{ $izin->santri && $izin->santri->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                        <td>{{ $izin->santri->asrama->nama_asrama ?? '-' }} {{ $izin->santri->kamar ? '(' . $izin->santri->kamar . ')' : '' }}</td>
                        <td class="text-center">{{ $jenisLabels[$izin->jenis_izin] ?? $izin->jenis_izin }}</td>
                        <td>{{ $izin->keperluan ?? '-' }}</td>
                        <td class="text-center">{{ $izin->tanggal_mulai ? $izin->tanggal_mulai->format('d/m/y H:i') : '-' }}</td>
                        <td class="text-center {{ $izin->status === 'terlambat_kembali' ? 'font-bold' : '' }}">{{ $izin->tanggal_selesai ? $izin->tanggal_selesai->format('d/m/y H:i') : '-' }}</td>
                        <td>{{ $izin->nama_pemberi_izin ?? $izin->approvedBy->name ?? '-' }}</td>
                        <td class="text-center font-bold">{{ $statusLabels[$izin->status] ?? $izin->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center" style="padding: 20px;">
                            Tidak ada data perizinan untuk periode bulan {{ $bulanNama }} {{ $tahun }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Bagian Pengesahan Tanda Tangan -->
        <div class="ttd-container">
            <div class="ttd-box">
                <p>Mengetahui,</p>
                <p class="font-bold">Musyrif / Pengurus Pengasuhan Santri</p>
                <div class="ttd-space"></div>
                <p class="font-bold"><u>( .................................................. )</u></p>
                <p style="font-size: 8pt; color: #6b7280;">ID / NIP Musyrif Pengasuhan</p>
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
