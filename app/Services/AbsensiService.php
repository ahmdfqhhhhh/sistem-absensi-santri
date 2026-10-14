<?php

namespace App\Services;

use App\Exceptions\AbsensiException;
use App\Models\Absensi;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AbsensiService
{
    /**
     * Memproses pemindaian QR Code presensi santri.
     *
     * @return array<string, mixed>
     *
     * @throws AbsensiException
     */
    public function scanQr(string $nis, int $sesiId, ?int $recordedById = null): array
    {
        $startTime = microtime(true);

        // 1. Cari Santri berdasarkan NIS (dengan eager loading kelas dan asrama untuk performa tinggi)
        $santri = Santri::query()
            ->with(['asrama:id,nama_asrama', 'kelas:id,nama_kelas'])
            ->where('nis', $nis)
            ->first();

        if (! $santri) {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            throw new AbsensiException(
                message: "Santri dengan NIS '{$nis}' tidak ditemukan.",
                code: Response::HTTP_NOT_FOUND,
                data: ['execution_time_ms' => $durationMs]
            );
        }

        // 2. Cari Sesi Absensi
        $sesi = SesiAbsensi::find($sesiId);

        if (! $sesi) {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            throw new AbsensiException(
                message: "Sesi absensi dengan ID {$sesiId} tidak ditemukan.",
                code: Response::HTTP_NOT_FOUND,
                data: ['execution_time_ms' => $durationMs]
            );
        }

        // 3. Validasi status sesi
        if ($sesi->status !== 'dibuka') {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            throw new AbsensiException(
                message: "Sesi absensi '{$sesi->nama_sesi}' saat ini tidak aktif (status: {$sesi->status}).",
                code: Response::HTTP_UNPROCESSABLE_ENTITY,
                data: ['execution_time_ms' => $durationMs]
            );
        }

        // 4. Periksa apakah santri sudah tercatat pada sesi ini
        $existingAbsensi = Absensi::query()
            ->where('santri_id', $santri->id)
            ->where('sesi_absensi_id', $sesi->id)
            ->first();

        if ($existingAbsensi) {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            $waktuFormatted = $existingAbsensi->waktu_absen ? $existingAbsensi->waktu_absen->format('H:i:s') : '-';

            throw new AbsensiException(
                message: "Santri {$santri->nama} ({$santri->nis}) sudah tercatat presensi pada sesi ini pada pukul {$waktuFormatted} (status: {$existingAbsensi->status}).",
                code: Response::HTTP_CONFLICT,
                data: [
                    'absensi_id' => $existingAbsensi->id,
                    'status' => $existingAbsensi->status,
                    'waktu_absen' => $waktuFormatted,
                    'execution_time_ms' => $durationMs,
                ]
            );
        }

        // 5. Cek apakah santri memiliki izin aktif di tabel perizinans
        $activeIzin = $this->findActivePerizinan($santri->id);

        $now = now();
        $isIzinAktif = false;
        $status = 'hadir';
        $keterangan = null;

        if ($activeIzin) {
            $isIzinAktif = true;
            // Jika izin sakit -> sakit, izin pulang / tugas / lainnya -> izin
            $status = ($activeIzin->jenis_izin === 'sakit') ? 'sakit' : 'izin';
            $jenisLabel = ucfirst(str_replace('_', ' ', $activeIzin->jenis_izin));
            $keterangan = "Otomatis dari Perizinan Aktif: {$jenisLabel} ({$activeIzin->nomor_surat}) - {$activeIzin->keperluan}";
        } else {
            // Evaluasi keterlambatan berdasarkan waktu sesi
            $status = $this->determineAttendanceStatus($sesi, $now);
            $keterangan = $status === 'terlambat'
                ? "Hadir terlambat pada pukul {$now->format('H:i:s')} (Batas: {$sesi->batas_terlambat})"
                : 'Hadir tepat waktu melalui pemindaian QR';
        }

        // 6. Simpan rekaman absensi ke database
        $absensi = DB::transaction(function () use ($santri, $sesi, $now, $status, $recordedById, $keterangan) {
            return Absensi::create([
                'santri_id' => $santri->id,
                'sesi_absensi_id' => $sesi->id,
                'waktu_absen' => $now,
                'status' => $status,
                'metode' => 'qr_code',
                'recorded_by' => $recordedById,
                'keterangan' => $keterangan,
            ]);
        });

        // Muat relasi untuk response resource
        $absensi->setRelation('santri', $santri);
        $absensi->setRelation('sesiAbsensi', $sesi);

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'absensi' => $absensi,
            'is_izin_aktif' => $isIzinAktif,
            'perizinan_aktif' => $activeIzin,
            'processing_time_ms' => $durationMs,
        ];
    }

    /**
     * Mencari surat perizinan yang sedang aktif berlaku untuk santri.
     */
    public function findActivePerizinan(int $santriId): ?Perizinan
    {
        $now = now();

        return Perizinan::query()
            ->where('santri_id', $santriId)
            ->whereIn('status', ['disetujui', 'sedang_berlangsung'])
            ->where('tanggal_mulai', '<=', $now)
            ->where('tanggal_selesai', '>=', $now)
            ->latest('tanggal_mulai')
            ->first();
    }

    /**
     * Menentukan apakah presensi berstatus 'hadir' atau 'terlambat'.
     */
    protected function determineAttendanceStatus(SesiAbsensi $sesi, Carbon $waktuScan): string
    {
        $currentTime = $waktuScan->format('H:i:s');

        // Jika batas_terlambat ditentukan, gunakan sebagai ambang batas
        if (! empty($sesi->batas_terlambat)) {
            return ($currentTime > $sesi->batas_terlambat) ? 'terlambat' : 'hadir';
        }

        // Jika batas_terlambat null namun jam_mulai ada
        if (! empty($sesi->jam_mulai)) {
            return ($currentTime > $sesi->jam_mulai) ? 'terlambat' : 'hadir';
        }

        return 'hadir';
    }
}
