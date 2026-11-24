<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Perizinan Harian - {{ $tanggalFormatted }}</title>
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
            font-size: 9pt;
            line-height: 1.3;
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
            background: #1e40af;
            color: white;
            border: none;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-close {
            background: transparent;
            color: #cbd5e1;
            border: 1px solid #475569;
            padding: 6px 14px;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        @media print {
            .no-print { display: none !important; }
        }
        .kop-surat {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .kop-title { font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; }
        .kop-pesantren { font-size: 16pt; font-weight: bold; margin: 2px 0; color: #1e40af; }
        .kop-address { font-size: 8pt; margin: 0; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background: #f3f4f6; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .summary-box {
            display: flex;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }
        .summary-item {
            padding: 4px 8px;
            border: 1px solid #94a3b8;
            border-radius: 4px;
        }
        .ttd-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .ttd-box { width: 220px; text-align: center; font-size: 9pt; }
        .ttd-space { height: 50px; }
    </style>
</head>
<body>
    <div class="no-print">
        <div><strong>Dokumen Resmi:</strong> Rekap Perizinan Harian — {{ $tanggalFormatted }}</div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
            <a href="javascript:window.close()" class="btn-close">Tutup</a>
        </div>
    </div>

    <div style="padding: 10px 15px;">
        <div class="kop-surat">
            <h2 class="kop-title">Laporan Perizinan Santri Harian</h2>
            <h1 class="kop-pesantren">PONDOK PESANTREN MIFTAHUL HUDA KROYA</h1>
            <p class="kop-address">Jl. Mujur No. 05 Kroya, Cilacap, Jawa Tengah &bull; Telp: (0282) 492123 &bull; Website: www.miftahulhudakroya.sch.id</p>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 6px;">
            <div>
                <strong>Tanggal:</strong> {{ $tanggalFormatted }} &bull; 
                <strong>Kategori:</strong> Santri {{ ucfirst($gender) }}
            </div>
            <div class="summary-box">
                <span class="summary-item">Total Izin: <strong>{{ $stats['total'] }}</strong></span>
                <span class="summary-item">Di Luar: <strong>{{ $stats['sedang_berlangsung'] }}</strong></span>
                <span class="summary-item">Terlambat: <strong>{{ $stats['terlambat_kembali'] }}</strong></span>
                <span class="summary-item">Pulang: <strong>{{ $stats['pulang'] }}</strong></span>
                <span class="summary-item">Selesai: <strong>{{ $stats['selesai'] }}</strong></span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 100px;">No. Surat</th>
                    <th>Nama Santri</th>
                    <th style="width: 75px;">Asrama</th>
                    <th style="width: 50px;">Kamar</th>
                    <th style="width: 60px;">Kelas</th>
                    <th style="width: 80px;">Jenis</th>
                    <th>Keperluan</th>
                    <th style="width: 85px;">Mulai</th>
                    <th style="width: 85px;">Batas</th>
                    <th style="width: 70px;">Status</th>
                    <th style="width: 90px;">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perizinans as $idx => $izin)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center" style="font-family: monospace;">{{ $izin->nomor_surat ?? '-' }}</td>
                        <td class="bold">{{ $izin->santri->nama ?? '-' }}</td>
                        <td>{{ $izin->santri->asrama->nama_asrama ?? '-' }}</td>
                        <td class="text-center">{{ $izin->santri->kamarRelation->nama_kamar ?? ($izin->santri->kamar ?? '-') }}</td>
                        <td>{{ $izin->santri->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $izin->jenis_izin)) }}</td>
                        <td>{{ $izin->keperluan }}</td>
                        <td class="text-center">{{ $izin->tanggal_mulai ? $izin->tanggal_mulai->format('d/m H:i') : '-' }}</td>
                        <td class="text-center">{{ $izin->tanggal_selesai ? $izin->tanggal_selesai->format('d/m H:i') : '-' }}</td>
                        <td class="text-center bold">{{ strtoupper(str_replace('_', ' ', $izin->status)) }}</td>
                        <td>{{ $izin->approvedBy->name ?? ($izin->nama_pemberi_izin ?? '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ttd-section">
            <div class="ttd-box">
                <p>Mengetahui,<br><strong>Pengasuh / Keamanan Gerbang</strong></p>
                <div class="ttd-space"></div>
                <p><u>( Ustadz Pengasuhan )</u></p>
            </div>
            <div class="ttd-box">
                <p>Kroya, {{ $tanggalFormatted }}<br><strong>Petugas Pos Keamanan</strong></p>
                <div class="ttd-space"></div>
                <p><u>( {{ Auth::user()->name ?? 'Petugas Kamtib' }} )</u></p>
            </div>
        </div>
    </div>
</body>
</html>
