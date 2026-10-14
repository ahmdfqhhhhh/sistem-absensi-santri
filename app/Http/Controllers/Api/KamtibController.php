<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ScanSuratIzinRequest;
use App\Http\Resources\KamtibLogResource;
use App\Http\Resources\PerizinanResource;
use App\Services\KamtibService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class KamtibController extends Controller
{
    /**
     * Dependency injection untuk KamtibService.
     */
    public function __construct(
        protected KamtibService $kamtibService
    ) {}

    /**
     * Memvalidasi QR Surat Izin santri, mencatat checkpoint log (checkout / checkin),
     * dan memperbarui status perizinan santri.
     */
    public function scanSuratIzin(ScanSuratIzinRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => ['required', 'string'],
            'action' => ['nullable', 'string', 'in:auto,checkout,checkin'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'petugas_id' => ['nullable', 'integer'],
        ], [
            'qr_data.required' => 'Data QR Code Surat Izin (qr_data) wajib disertakan.',
            'action.in' => 'Action harus salah satu dari: auto, checkout, atau checkin.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi pemindaian QR Surat Izin gagal.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $qrData = (string) $request->input('qr_data');
        $action = (string) $request->input('action', 'auto');
        $lokasi = $request->input('lokasi');
        $catatan = $request->input('catatan');
        $petugasId = $request->user()?->id ?? $request->input('petugas_id');

        try {
            $result = $this->kamtibService->scanSuratIzin(
                qrData: $qrData,
                action: $action,
                petugasId: $petugasId ? (int) $petugasId : null,
                lokasi: $lokasi ? (string) $lokasi : null,
                catatan: $catatan ? (string) $catatan : null
            );

            $resolvedAction = $result['action'];
            $isTerlambat = $result['is_terlambat'];
            $kamtibLog = $result['kamtib_log'];
            $perizinan = $result['perizinan'];

            $actionLabel = $resolvedAction === 'checkout' ? 'Checkout (Keluar Pondok)' : 'Checkin (Kembali ke Pondok)';

            $message = $isTerlambat
                ? "Peringatan: {$actionLabel} berhasil dicatat, namun santri TERLAMBAT kembali melewati batas izin."
                : "{$actionLabel} berhasil divalidasi dan dicatat di buku log keamanan & ketertiban.";

            return response()->json([
                'success' => true,
                'action' => $resolvedAction,
                'message' => $message,
                'data' => [
                    'kamtib_log' => new KamtibLogResource($kamtibLog),
                    'perizinan' => new PerizinanResource($perizinan),
                ],
                'meta' => [
                    'is_terlambat' => $isTerlambat,
                    'timestamp' => now()->toIso8601String(),
                ],
            ], Response::HTTP_CREATED);

        } catch (AbsensiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
