<?php

namespace App\Services;

use App\Exceptions\AbsensiException;
use App\Models\KamtibLog;
use App\Models\Perizinan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class KamtibService
{
    /**
     * Memproses pemindaian QR Code Surat Izin oleh petugas Kamtib/Keamanan Gerbang.
     * Mencatat log checkout/checkin dan memperbarui status perizinan santri.
     *
     * @param  string  $action  'auto' | 'checkout' | 'checkin'
     * @return array<string, mixed>
     *
     * @throws AbsensiException
     */
    public function scanSuratIzin(
        string $qrData,
        string $action = 'auto',
        ?int $petugasId = null,
        ?string $lokasi = null,
        ?string $catatan = null
    ): array {
        // 1. Ekstrak token QR
        $qrToken = $this->extractToken($qrData);

        // 2. Temukan data surat perizinan
        $perizinan = Perizinan::query()
            ->with(['santri.asrama', 'santri.kelas', 'approvedBy'])
            ->where('qr_token', $qrToken)
            ->first();

        if (! $perizinan) {
            throw new AbsensiException(
                message: 'QR Code Surat Izin Digital tidak valid atau tidak terdaftar di sistem.',
                code: Response::HTTP_NOT_FOUND
            );
        }

        // 3. Validasi status perizinan saat ini
        if ($perizinan->status === 'pending') {
            throw new AbsensiException(
                message: 'Surat perizinan santri belum disetujui oleh pengasuhan pesantren.',
                code: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($perizinan->status === 'ditolak') {
            throw new AbsensiException(
                message: 'Surat perizinan ini telah DITOLAK. Santri dilarang meninggalkan pondok.',
                code: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (in_array($perizinan->status, ['selesai', 'terlambat_kembali'])) {
            throw new AbsensiException(
                message: 'Surat perizinan ini sudah selesai digunakan dan santri telah tercatat kembali sebelumnya.',
                code: Response::HTTP_CONFLICT
            );
        }

        // 4. Tentukan aksi (checkout / checkin)
        $resolvedAction = $action;
        if ($resolvedAction === 'auto') {
            if ($perizinan->status === 'disetujui') {
                $resolvedAction = 'checkout';
            } elseif ($perizinan->status === 'sedang_berlangsung') {
                $resolvedAction = 'checkin';
            } else {
                $resolvedAction = 'checkout';
            }
        }

        // Validasi kesesuaian status terhadap aksi manual
        if ($resolvedAction === 'checkout' && $perizinan->status === 'sedang_berlangsung') {
            throw new AbsensiException(
                message: 'Santri sudah melakukan checkout sebelumnya pada surat izin ini (status: Sedang Berlangsung).',
                code: Response::HTTP_CONFLICT
            );
        }

        $now = now();
        $lokasiCheckpoint = $lokasi ?? 'Pos Gerbang Keamanan Utama';

        // 5. Eksekusi mutasi status & pencatatan Kamtib Log secara transaksional
        return DB::transaction(function () use (
            $perizinan,
            $resolvedAction,
            $now,
            $lokasiCheckpoint,
            $petugasId,
            $catatan
        ) {
            $isTerlambat = false;
            $kamtibLog = null;
            $statusPerizinanBaru = $perizinan->status;

            if ($resolvedAction === 'checkout') {
                $statusPerizinanBaru = 'sedang_berlangsung';

                $keteranganKeluar = $catatan ?? (
                    'Santri keluar lingkungan pesantren. Penjemput: '.
                    ($perizinan->penjemput_nama ? "{$perizinan->penjemput_nama} ({$perizinan->penjemput_hubungan})" : 'Mandiri')
                );

                $kamtibLog = KamtibLog::create([
                    'santri_id' => $perizinan->santri_id,
                    'perizinan_id' => $perizinan->id,
                    'tipe_log' => 'checkout',
                    'waktu_log' => $now,
                    'judul_pelanggaran' => "Checkout Gerbang (Keluar Pondok): {$perizinan->nomor_surat}",
                    'kategori' => 'ringan',
                    'poin_pelanggaran' => 0,
                    'tanggal_kejadian' => $now->toDateString(),
                    'lokasi_kejadian' => $lokasiCheckpoint,
                    'tindakan_sanksi' => null,
                    'status_penyelesaian' => 'selesai',
                    'recorded_by' => $petugasId,
                    'catatan' => $keteranganKeluar,
                ]);

                $perizinan->update([
                    'status' => $statusPerizinanBaru,
                ]);

            } else { // checkin
                $isTerlambat = $now > $perizinan->tanggal_selesai;
                $statusPerizinanBaru = $isTerlambat ? 'terlambat_kembali' : 'selesai';

                $judulLog = "Checkin Gerbang (Kembali ke Pondok): {$perizinan->nomor_surat}";
                $kategoriLog = 'ringan';
                $poinLog = 0;
                $sanksiLog = null;
                $statusPenyelesaianLog = 'selesai';
                $catatanLog = $catatan ?? 'Santri kembali tepat waktu ke lingkungan pondok pesantren.';

                if ($isTerlambat) {
                    $menitTerlambat = (int) $now->diffInMinutes($perizinan->tanggal_selesai);
                    $judulLog = "Terlambat Kembali ke Pondok: {$perizinan->nomor_surat} (Terlambat {$menitTerlambat} menit)";
                    $kategoriLog = $menitTerlambat > 120 ? 'berat' : 'sedang';
                    $poinLog = $menitTerlambat > 120 ? 25 : 10;
                    $sanksiLog = 'Pencatatan pelanggaran batas waktu izin, takzir kebersihan dan pembinaan wali asrama.';
                    $statusPenyelesaianLog = 'dalam_proses';
                    $catatanLog = $catatan ?? (
                        "Santri kembali pada {$now->format('Y-m-d H:i')}, melewati batas izin ".
                        $perizinan->tanggal_selesai->format('Y-m-d H:i')." (Selisih: {$menitTerlambat} menit)."
                    );
                }

                $kamtibLog = KamtibLog::create([
                    'santri_id' => $perizinan->santri_id,
                    'perizinan_id' => $perizinan->id,
                    'tipe_log' => 'checkin',
                    'waktu_log' => $now,
                    'judul_pelanggaran' => $judulLog,
                    'kategori' => $kategoriLog,
                    'poin_pelanggaran' => $poinLog,
                    'tanggal_kejadian' => $now->toDateString(),
                    'lokasi_kejadian' => $lokasiCheckpoint,
                    'tindakan_sanksi' => $sanksiLog,
                    'status_penyelesaian' => $statusPenyelesaianLog,
                    'recorded_by' => $petugasId,
                    'catatan' => $catatanLog,
                ]);

                $perizinan->update([
                    'tanggal_kembali_aktual' => $now,
                    'status' => $statusPerizinanBaru,
                ]);
            }

            // Muat relasi untuk response resource
            $kamtibLog->setRelation('santri', $perizinan->santri);
            $kamtibLog->setRelation('perizinan', $perizinan);

            return [
                'action' => $resolvedAction,
                'is_terlambat' => $isTerlambat,
                'kamtib_log' => $kamtibLog,
                'perizinan' => $perizinan->fresh(['santri.asrama', 'santri.kelas', 'approvedBy']),
            ];
        });
    }

    /**
     * Ekstrak token dari string QR mentah (baik format token biasa maupun JSON).
     */
    protected function extractToken(string $qrData): string
    {
        $trimmed = trim($qrData);

        if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded) && ! empty($decoded['token'])) {
                return (string) $decoded['token'];
            }
        }

        return $trimmed;
    }
}
