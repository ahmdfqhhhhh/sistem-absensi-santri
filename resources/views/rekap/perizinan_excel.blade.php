<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Perizinan Santri {{ $bulanNama }} {{ $tahun }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; }
        .subtitle { font-size: 11pt; text-align: center; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2563eb; color: #ffffff; font-weight: bold; padding: 8px; border: 1px solid #000000; text-align: center; }
        td { padding: 6px; border: 1px solid #000000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .bg-gray { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <div class="title">REKAPITULASI SURAT PERIZINAN SANTRI</div>
    <div class="title">PONDOK PESANTREN MIFTAHUL HUDA KROYA</div>
    <div class="subtitle">Periode: {{ $bulanNama }} {{ $tahun }} &bull; Kategori: Santri {{ ucfirst($gender) }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 130px;">No. Surat</th>
                <th style="width: 90px;">NIS</th>
                <th style="width: 200px;">Nama Santri</th>
                <th style="width: 70px;">Gender</th>
                <th style="width: 120px;">Asrama</th>
                <th style="width: 80px;">Kamar</th>
                <th style="width: 90px;">Kelas</th>
                <th style="width: 110px;">Jenis Izin</th>
                <th style="width: 200px;">Keperluan</th>
                <th style="width: 110px;">Waktu Mulai</th>
                <th style="width: 110px;">Batas Kembali</th>
                <th style="width: 110px;">Kembali Aktual</th>
                <th style="width: 150px;">Penjemput</th>
                <th style="width: 150px;">Pemberi Izin</th>
                <th style="width: 100px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $jenisLabels = [
                    'pulang' => 'Izin Pulang',
                    'keluar_komplek' => 'Keluar Komplek',
                    'sakit' => 'Sakit',
                    'sambangan_keluarga' => 'Sambangan',
                    'tugas_pondok' => 'Tugas Pondok',
                ];
                $statusLabels = [
                    'pending' => 'Menunggu Approval',
                    'disetujui' => 'Disetujui',
                    'sedang_berlangsung' => 'Sedang di Luar',
                    'selesai' => 'Selesai / Kembali',
                    'terlambat_kembali' => 'Terlambat Kembali',
                    'ditolak' => 'Ditolak',
                ];
            @endphp
            @forelse($perizinans as $idx => $izin)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center bold" style="mso-number-format:'\@';">{{ $izin->nomor_surat ?? '-' }}</td>
                    <td class="text-center" style="mso-number-format:'\@';">{{ $izin->santri->nis ?? '-' }}</td>
                    <td>{{ $izin->santri->nama ?? '-' }}</td>
                    <td class="text-center">{{ $izin->santri && $izin->santri->jenis_kelamin === 'L' ? 'Putra' : 'Putri' }}</td>
                    <td>{{ $izin->santri->asrama->nama_asrama ?? '-' }}</td>
                    <td class="text-center">{{ $izin->santri->kamar ?? '-' }}</td>
                    <td>{{ $izin->santri->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $jenisLabels[$izin->jenis_izin] ?? $izin->jenis_izin }}</td>
                    <td>{{ $izin->keperluan ?? '-' }}</td>
                    <td class="text-center">{{ $izin->tanggal_mulai ? $izin->tanggal_mulai->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">{{ $izin->tanggal_selesai ? $izin->tanggal_selesai->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-center">{{ $izin->tanggal_kembali_aktual ? $izin->tanggal_kembali_aktual->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $izin->penjemput_nama ? $izin->penjemput_nama . ' (' . ($izin->penjemput_hubungan ?? '-') . ')' : '-' }}</td>
                    <td class="bold">{{ $izin->nama_pemberi_izin ?? $izin->approvedBy->name ?? '-' }}</td>
                    <td class="text-center bold">{{ $statusLabels[$izin->status] ?? $izin->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="text-center" style="padding: 15px;">
                        Tidak ada data perizinan santri pada periode bulan {{ $bulanNama }} {{ $tahun }}.
                    </td>
                </tr>
            @endforelse
            <tr class="bg-gray bold">
                <td colspan="9" class="text-right">RINGKASAN REKAPITULASI:</td>
                <td colspan="7">
                    Total: {{ $stats['total'] }} &bull;
                    Disetujui: {{ $stats['disetujui'] }} &bull;
                    Sedang di Luar: {{ $stats['sedang_berlangsung'] }} &bull;
                    Selesai: {{ $stats['selesai'] }} &bull;
                    Terlambat: {{ $stats['terlambat_kembali'] }} &bull;
                    Ditolak: {{ $stats['ditolak'] }}
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
                    <p class="bold">Pengasuh / Pengurus Pondok Pesantren</p>
                    <br><br><br>
                    <p class="bold">( .................................................. )</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
