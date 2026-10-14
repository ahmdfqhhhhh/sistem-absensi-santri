<?php

namespace App\Services;

use App\Exceptions\AbsensiException;
use App\Models\Perizinan;
use App\Models\Santri;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;

class PerizinanService
{
    /**
     * Mengambil daftar perizinan dengan filter pencarian dan pagination.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<Perizinan>
     */
    public function getList(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Perizinan::query()
            ->with(['santri.asrama', 'santri.kelas', 'approvedBy'])
            ->latest('created_at');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['jenis_izin'])) {
            $query->where('jenis_izin', $filters['jenis_izin']);
        }

        if (! empty($filters['santri_id'])) {
            $query->where('santri_id', $filters['santri_id']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhereHas('santri', function ($sq) use ($search) {
                        $sq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Mengajukan surat perizinan santri baru.
     *
     * @param  array<string, mixed>  $data
     */
    public function ajukan(array $data): Perizinan
    {
        $nomorSurat = $this->generateNomorSurat();

        return Perizinan::create([
            'santri_id' => $data['santri_id'],
            'nomor_surat' => $nomorSurat,
            'jenis_izin' => $data['jenis_izin'],
            'keperluan' => $data['keperluan'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'status' => 'pending',
            'penjemput_nama' => $data['penjemput_nama'] ?? null,
            'penjemput_hubungan' => $data['penjemput_hubungan'] ?? null,
            'bukti_dokumen_path' => $data['bukti_dokumen_path'] ?? null,
        ]);
    }

    /**
     * Menyetujui perizinan santri dan meng-generate QR Code Digital unik.
     *
     *
     * @throws AbsensiException
     */
    public function approve(int|Perizinan $perizinan, ?int $approverId = null, ?string $catatanApproval = null): Perizinan
    {
        if (is_numeric($perizinan)) {
            $perizinan = Perizinan::with(['santri.asrama', 'santri.kelas'])->find($perizinan);
        }

        if (! $perizinan) {
            throw new AbsensiException(
                message: 'Data perizinan santri tidak ditemukan.',
                code: Response::HTTP_NOT_FOUND
            );
        }

        if ($perizinan->status === 'disetujui') {
            throw new AbsensiException(
                message: 'Perizinan ini sudah disetujui sebelumnya.',
                code: Response::HTTP_CONFLICT
            );
        }

        // 1. Generate token unik & payload QR Code
        $qrToken = 'IZN-'.strtoupper(Str::random(12)).'-'.time();
        $santri = $perizinan->santri;

        $qrPayloadData = [
            'type' => 'SURAT_IZIN_DIGITAL',
            'token' => $qrToken,
            'nomor_surat' => $perizinan->nomor_surat,
            'santri' => [
                'nis' => $santri?->nis,
                'nama' => $santri?->nama,
                'kelas' => $santri?->kelas?->nama_kelas,
                'asrama' => $santri?->asrama?->nama_asrama,
            ],
            'jenis_izin' => $perizinan->jenis_izin,
            'berlaku_mulai' => $perizinan->tanggal_mulai?->format('Y-m-d H:i:s'),
            'berlaku_sampai' => $perizinan->tanggal_selesai?->format('Y-m-d H:i:s'),
            'status' => 'APPROVED',
            'verify_url' => url('/api/perizinan/verify/'.$qrToken),
            'generated_at' => now()->toIso8601String(),
        ];

        $qrString = json_encode($qrPayloadData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // 2. Generate file QR Code menggunakan simplesoftwareio/simple-qrcode
        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrString);

        // 3. Simpan file QR ke storage disk public
        $fileName = 'qrcodes/perizinan/'.$qrToken.'.svg';
        Storage::disk('public')->put($fileName, $qrSvg);

        // 4. Update status perizinan menjadi disetujui (approved)
        $perizinan->update([
            'status' => 'disetujui',
            'approved_by' => $approverId,
            'catatan_approval' => $catatanApproval ?? 'Disetujui oleh pengasuhan pesantren.',
            'qr_code_path' => $fileName,
            'qr_token' => $qrToken,
        ]);

        return $perizinan->fresh(['santri.asrama', 'santri.kelas', 'approvedBy']);
    }

    /**
     * Menolak pengajuan perizinan santri.
     *
     *
     * @throws AbsensiException
     */
    public function reject(int|Perizinan $perizinan, ?int $approverId = null, ?string $alasanPenolakan = null): Perizinan
    {
        if (is_numeric($perizinan)) {
            $perizinan = Perizinan::find($perizinan);
        }

        if (! $perizinan) {
            throw new AbsensiException('Data perizinan santri tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        $perizinan->update([
            'status' => 'ditolak',
            'approved_by' => $approverId,
            'catatan_approval' => $alasanPenolakan ?? 'Pengajuan perizinan ditolak.',
        ]);

        return $perizinan->fresh(['santri.asrama', 'santri.kelas', 'approvedBy']);
    }

    /**
     * Memverifikasi validitas QR Code Surat Izin Digital santri (biasanya dipindai di pos satpam).
     *
     * @return array<string, mixed>
     *
     * @throws AbsensiException
     */
    public function verifyQrToken(string $qrToken): array
    {
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

        $now = now();
        $isExpired = $perizinan->tanggal_selesai < $now;
        $isBelumMulai = $perizinan->tanggal_mulai > $now;
        $isValidNow = in_array($perizinan->status, ['disetujui', 'sedang_berlangsung']) && ! $isExpired && ! $isBelumMulai;

        $catatanVerifikasi = 'Surat Izin Sah dan Berlaku Aktif.';
        if ($isExpired) {
            $catatanVerifikasi = 'PERINGATAN: Masa berlaku izin sudah kadaluarsa (melewati batas waktu kembali).';
        } elseif ($isBelumMulai) {
            $catatanVerifikasi = 'PERINGATAN: Waktu mulai izin belum tiba.';
        } elseif ($perizinan->status === 'ditolak') {
            $catatanVerifikasi = 'PERINGATAN: Pengajuan izin ini telah DITOLAK.';
        } elseif ($perizinan->status === 'selesai') {
            $catatanVerifikasi = 'Santri telah tercatat kembali ke pondok.';
        }

        return [
            'is_valid' => $isValidNow,
            'catatan_verifikasi' => $catatanVerifikasi,
            'perizinan' => $perizinan,
        ];
    }

    /**
     * Mencatat kepulangan santri kembali ke pondok pesantren.
     *
     *
     * @throws AbsensiException
     */
    public function catatKembali(int|Perizinan $perizinan): Perizinan
    {
        if (is_numeric($perizinan)) {
            $perizinan = Perizinan::find($perizinan);
        }

        if (! $perizinan) {
            throw new AbsensiException('Data perizinan santri tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        $now = now();
        $isTerlambat = $now > $perizinan->tanggal_selesai;
        $newStatus = $isTerlambat ? 'terlambat_kembali' : 'selesai';

        $perizinan->update([
            'tanggal_kembali_aktual' => $now,
            'status' => $newStatus,
        ]);

        return $perizinan->fresh(['santri.asrama', 'santri.kelas', 'approvedBy']);
    }

    /**
     * Generate format nomor surat izin resmi pesantren.
     */
    protected function generateNomorSurat(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "IZN/{$year}/{$month}/";

        $count = DB::table('perizinans')
            ->where('nomor_surat', 'like', "{$prefix}%")
            ->count() + 1;

        return $prefix.Str::padLeft((string) $count, 4, '0');
    }
}
