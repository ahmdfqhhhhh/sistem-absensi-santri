<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Perizinan Harian Santri {{ $tanggal }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; }
        .subtitle { font-size: 11pt; text-align: center; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #1e40af; color: #ffffff; font-weight: bold; padding: 8px; border: 1px solid #000000; text-align: center; }
        td { padding: 6px; border: 1px solid #000000; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="title">REKAPITULASI PERIZINAN HARIAN SANTRI</div>
    <div class="title">PONDOK PESANTREN MIFTAHUL HUDA KROYA</div>
    <div class="subtitle">Tanggal: {{ $tanggalFormatted }} &bull; Kategori: Santri {{ ucfirst($gender) }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 120px;">No. Surat</th>
                <th style="width: 100px;">NIS</th>
                <th style="width: 200px;">Nama Santri</th>
                <th style="width: 120px;">Asrama</th>
                <th style="width: 80px;">Kamar</th>
                <th style="width: 90px;">Kelas</th>
                <th style="width: 120px;">Jenis Izin</th>
                <th style="width: 250px;">Keperluan</th>
                <th style="width: 130px;">Mulai Izin</th>
                <th style="width: 130px;">Batas Kembali</th>
                <th style="width: 130px;">Kembali Aktual</th>
                <th style="width: 100px;">Status</th>
                <th style="width: 140px;">Pemberi Izin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perizinans as $idx => $izin)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $izin->nomor_surat ?? '-' }}</td>
                    <td class="text-center">{{ $izin->santri->nis ?? '-' }}</td>
                    <td class="bold">{{ $izin->santri->nama ?? '-' }}</td>
                    <td>{{ $izin->santri->asrama->nama_asrama ?? '-' }}</td>
                    <td class="text-center">{{ $izin->santri->kamarRelation->nama_kamar ?? ($izin->santri->kamar ?? '-') }}</td>
                    <td>{{ $izin->santri->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $izin->jenis_izin)) }}</td>
                    <td>{{ $izin->keperluan }}</td>
                    <td class="text-center">{{ $izin->tanggal_mulai ? $izin->tanggal_mulai->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">{{ $izin->tanggal_selesai ? $izin->tanggal_selesai->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">{{ $izin->tanggal_kembali_aktual ? $izin->tanggal_kembali_aktual->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center bold">{{ strtoupper(str_replace('_', ' ', $izin->status)) }}</td>
                    <td>{{ $izin->approvedBy->name ?? ($izin->nama_pemberi_izin ?? '-') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
