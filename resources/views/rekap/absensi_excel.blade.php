<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Santri {{ $bulanNama }} {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; }
        .subtitle { font-size: 11pt; text-align: center; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #059669; color: #ffffff; font-weight: bold; padding: 8px; border: 1px solid #000000; text-align: center; }
        td { padding: 6px; border: 1px solid #000000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .bg-gray { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <div class="title">REKAPITULASI PRESENSI SANTRI</div>
    <div class="title">PONDOK PESANTREN MIFTAHUL HUDA KROYA</div>
    <div class="subtitle">Periode: {{ $bulanNama }} {{ $tahun }} &bull; Kategori: Santri {{ ucfirst($gender) }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 100px;">NIS</th>
                <th style="width: 220px;">Nama Santri</th>
                <th style="width: 80px;">Gender</th>
                <th style="width: 140px;">Asrama</th>
                <th style="width: 80px;">Kamar</th>
                <th style="width: 100px;">Kelas</th>
                <th style="width: 60px;">Hadir</th>
                <th style="width: 60px;">Terlambat</th>
                <th style="width: 60px;">Izin</th>
                <th style="width: 60px;">Sakit</th>
                <th style="width: 60px;">Alpa</th>
                <th style="width: 80px;">Total Sesi</th>
                <th style="width: 90px;">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapList as $idx => $r)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center" style="mso-number-format:'\@';">{{ $r['santri']->nis }}</td>
                    <td>{{ $r['santri']->nama }}</td>
                    <td class="text-center">{{ $r['santri']->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}</td>
                    <td>{{ $r['santri']->asrama->nama_asrama ?? '-' }}</td>
                    <td>{{ $r['santri']->kamar ?? '-' }}</td>
                    <td>{{ $r['santri']->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $r['hadir'] }}</td>
                    <td class="text-center">{{ $r['terlambat'] }}</td>
                    <td class="text-center">{{ $r['izin'] }}</td>
                    <td class="text-center">{{ $r['sakit'] }}</td>
                    <td class="text-center">{{ $r['alpa'] }}</td>
                    <td class="text-center bold">{{ $r['total'] }}</td>
                    <td class="text-center bold">{{ $r['persentase'] ?? 0 }}%</td>
                </tr>
            @endforeach
            <tr class="bg-gray bold">
                <td colspan="7" class="text-right">TOTAL KESELURUHAN SESI:</td>
                <td class="text-center">{{ $summary['hadir'] }}</td>
                <td class="text-center">{{ $summary['terlambat'] }}</td>
                <td class="text-center">{{ $summary['izin'] }}</td>
                <td class="text-center">{{ $summary['sakit'] }}</td>
                <td class="text-center">{{ $summary['alpa'] }}</td>
                <td class="text-center">{{ $summary['total_sesi'] }}</td>
                <td class="text-center">
                    @php
                        $grandKehadiran = ($summary['total_sesi'] > 0)
                            ? round((($summary['hadir'] + $summary['terlambat']) / $summary['total_sesi']) * 100, 1)
                            : 0;
                    @endphp
                    {{ $grandKehadiran }}%
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; width: 60%;"></td>
                <td style="border: none; width: 40%; text-align: center;">
                    <p>Kroya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p>Mengetahui,</p>
                    <p class="bold">Pengasuh Pondok Pesantren</p>
                    <br><br><br>
                    <p class="bold">( .................................................. )</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>