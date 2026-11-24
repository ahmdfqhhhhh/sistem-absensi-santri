<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Harian Santri {{ $tanggal }}</title>
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
    <div class="title">REKAPITULASI PRESENSI HARIAN SANTRI</div>
    <div class="title">PONDOK PESANTREN MIFTAHUL HUDA KROYA</div>
    <div class="subtitle">Tanggal: {{ $tanggalFormatted }} &bull; Kategori: Santri {{ ucfirst($gender) }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 100px;">NIS</th>
                <th style="width: 220px;">Nama Santri</th>
                <th style="width: 80px;">Gender</th>
                <th style="width: 140px;">Asrama</th>
                <th style="width: 90px;">Kamar</th>
                <th style="width: 100px;">Kelas</th>
                @if($sesiId)
                    <th style="width: 100px;">Status</th>
                    <th style="width: 90px;">Waktu</th>
                    <th style="width: 120px;">Pencatat</th>
                @else
                    @foreach($allSesis as $sesi)
                        <th style="width: 70px;">{{ str_replace(['Sholat ', ' Berjamaah', 'Presensi '], '', $sesi->nama_sesi) }}</th>
                    @endforeach
                    <th style="width: 60px;">Total H</th>
                    <th style="width: 60px;">Total A</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($rekapList as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $item['santri']->nis ?? '-' }}</td>
                    <td class="bold">{{ $item['santri']->nama }}</td>
                    <td class="text-center">{{ $item['santri']->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}</td>
                    <td>{{ $item['santri']->asrama->nama_asrama ?? '-' }}</td>
                    <td class="text-center">{{ $item['santri']->kamarRelation->nama_kamar ?? ($item['santri']->kamar ?? '-') }}</td>
                    <td>{{ $item['santri']->kelas->nama_kelas ?? '-' }}</td>

                    @if($sesiId)
                        @php
                            $rec = $item['latest_record'];
                        @endphp
                        <td class="text-center bold">{{ $rec ? strtoupper($rec->status) : 'BELUM ABSEN' }}</td>
                        <td class="text-center">{{ $rec && $rec->waktu_absen ? $rec->waktu_absen->format('H:i:s') : '-' }}</td>
                        <td>{{ $rec ? ($rec->recordedBy->name ?? 'Sistem') : '-' }}</td>
                    @else
                        @foreach($allSesis as $sesi)
                            @php
                                $st = $item['sesi_status'][$sesi->id] ?? null;
                            @endphp
                            <td class="text-center bold">
                                @if($st === 'hadir') H
                                @elseif($st === 'terlambat') T
                                @elseif($st === 'izin') I
                                @elseif($st === 'sakit') S
                                @elseif($st === 'alpa') A
                                @else -
                                @endif
                            </td>
                        @endforeach
                        <td class="text-center bold">{{ $item['hadir'] }}</td>
                        <td class="text-center bold">{{ $item['alpa'] }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
