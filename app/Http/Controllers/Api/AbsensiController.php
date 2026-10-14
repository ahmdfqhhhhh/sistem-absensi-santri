<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AbsensiResource;
use App\Services\AbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AbsensiController extends Controller
{
    /**
     * Dependency injection untuk AbsensiService.
     */
    public function __construct(
        protected AbsensiService $absensiService
    ) {}

    /**
     * Menangani pemindaian QR Code santri untuk presensi kegiatan/sesi.
     */
    public function scanQr(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        // Validasi input nis dan sesi_id
        $validator = Validator::make($request->all(), [
            'nis' => ['required', 'string', 'max:50'],
            'sesi_id' => ['required', 'integer', 'min:1'],
            'recorded_by' => ['nullable', 'integer', 'exists:users,id'],
        ], [
            'nis.required' => 'Nomor Induk Santri (NIS) wajib disertakan.',
            'sesi_id.required' => 'ID Sesi Absensi (sesi_id) wajib disertakan.',
            'sesi_id.integer' => 'sesi_id harus bernilai angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: parameter tidak lengkap atau format salah.',
                'errors' => $validator->errors(),
                'meta' => [
                    'waktu_eksekusi_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $nis = (string) $request->input('nis');
        $sesiId = (int) $request->input('sesi_id');
        $recordedById = $request->user()?->id ?? $request->input('recorded_by');

        try {
            $result = $this->absensiService->scanQr($nis, $sesiId, $recordedById ? (int) $recordedById : null);

            $absensi = $result['absensi'];
            $isIzinAktif = $result['is_izin_aktif'];
            $activeIzin = $result['perizinan_aktif'];
            $processingTimeMs = $result['processing_time_ms'];

            $message = $isIzinAktif
                ? "Presensi otomatis dialihkan menjadi status '".ucfirst($absensi->status)."' karena santri memiliki izin aktif ({$activeIzin?->nomor_surat})."
                : "Presensi berhasil dicatat dengan status '".ucfirst($absensi->status)."'.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => new AbsensiResource($absensi),
                'meta' => [
                    'status_otomatis_izin' => $isIzinAktif,
                    'perizinan_aktif' => $activeIzin ? [
                        'nomor_surat' => $activeIzin->nomor_surat,
                        'jenis_izin' => $activeIzin->jenis_izin,
                        'keperluan' => $activeIzin->keperluan,
                        'tanggal_mulai' => $activeIzin->tanggal_mulai?->format('Y-m-d H:i:s'),
                        'tanggal_selesai' => $activeIzin->tanggal_selesai?->format('Y-m-d H:i:s'),
                    ] : null,
                    'waktu_eksekusi_ms' => $processingTimeMs,
                    'target_performa' => '< 500ms',
                    'timestamp' => now()->toIso8601String(),
                ],
            ], Response::HTTP_CREATED);

        } catch (AbsensiException $e) {
            $totalDurationMs = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => $e->getData() ?: null,
                'meta' => [
                    'waktu_eksekusi_ms' => $totalDurationMs,
                    'target_performa' => '< 500ms',
                    'timestamp' => now()->toIso8601String(),
                ],
            ], $e->getCode() ?: Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
