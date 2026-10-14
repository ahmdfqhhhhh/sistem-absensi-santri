<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AjukanPerizinanRequest;
use App\Http\Requests\Api\ApprovePerizinanRequest;
use App\Http\Resources\PerizinanResource;
use App\Models\Perizinan;
use App\Services\PerizinanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PerizinanController extends Controller
{
    /**
     * Dependency injection untuk PerizinanService.
     */
    public function __construct(
        protected PerizinanService $perizinanService
    ) {}

    /**
     * Menampilkan daftar perizinan santri.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'jenis_izin', 'santri_id', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        $perizinans = $this->perizinanService->getList($filters, $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar perizinan santri berhasil diambil.',
            'data' => PerizinanResource::collection($perizinans),
            'pagination' => [
                'current_page' => $perizinans->currentPage(),
                'last_page' => $perizinans->lastPage(),
                'per_page' => $perizinans->perPage(),
                'total' => $perizinans->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Menampilkan detail satu data perizinan santri.
     */
    public function show(int $id): JsonResponse
    {
        $perizinan = Perizinan::with(['santri.asrama', 'santri.kelas', 'approvedBy'])->find($id);

        if (! $perizinan) {
            return response()->json([
                'success' => false,
                'message' => "Perizinan santri dengan ID {$id} tidak ditemukan.",
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail perizinan santri berhasil diambil.',
            'data' => new PerizinanResource($perizinan),
        ], Response::HTTP_OK);
    }

    /**
     * Mengajukan surat perizinan santri baru.
     */
    public function store(AjukanPerizinanRequest $request): JsonResponse
    {
        $perizinan = $this->perizinanService->ajukan($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan perizinan santri berhasil dibuat (status: Menunggu Persetujuan).',
            'data' => new PerizinanResource($perizinan),
        ], Response::HTTP_CREATED);
    }

    /**
     * Menyetujui perizinan santri dan otomatis generate QR Code Surat Izin Digital ke storage.
     */
    public function approve(int $id, ApprovePerizinanRequest $request): JsonResponse
    {
        $approverId = $request->user()?->id ?? $request->input('approved_by');
        $catatan = $request->input('catatan_approval');

        try {
            $perizinan = $this->perizinanService->approve($id, $approverId ? (int) $approverId : null, $catatan);

            return response()->json([
                'success' => true,
                'message' => 'Perizinan santri berhasil disetujui. QR Code Surat Izin Digital telah dibuat dan disimpan.',
                'data' => new PerizinanResource($perizinan),
                'meta' => [
                    'qr_token' => $perizinan->qr_token,
                    'qr_code_path' => $perizinan->qr_code_path,
                ],
            ], Response::HTTP_OK);
        } catch (AbsensiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Menolak pengajuan perizinan santri.
     */
    public function reject(int $id, Request $request): JsonResponse
    {
        $approverId = $request->user()?->id ?? $request->input('approved_by');
        $alasan = $request->input('catatan_approval', 'Pengajuan perizinan ditolak.');

        try {
            $perizinan = $this->perizinanService->reject($id, $approverId ? (int) $approverId : null, $alasan);

            return response()->json([
                'success' => true,
                'message' => 'Perizinan santri berhasil ditolak.',
                'data' => new PerizinanResource($perizinan),
            ], Response::HTTP_OK);
        } catch (AbsensiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Memverifikasi Surat Izin Digital melalui pemindaian QR Code (untuk petugas pos jaga/satpam).
     */
    public function verify(string $token): JsonResponse
    {
        try {
            $result = $this->perizinanService->verifyQrToken($token);

            return response()->json([
                'success' => true,
                'is_valid' => $result['is_valid'],
                'message' => $result['catatan_verifikasi'],
                'data' => new PerizinanResource($result['perizinan']),
            ], Response::HTTP_OK);
        } catch (AbsensiException $e) {
            return response()->json([
                'success' => false,
                'is_valid' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Mencatat kepulangan santri kembali ke pondok pesantren.
     */
    public function catatKembali(int $id): JsonResponse
    {
        try {
            $perizinan = $this->perizinanService->catatKembali($id);

            $pesan = $perizinan->status === 'terlambat_kembali'
                ? 'Santri tercatat kembali, namun terlambat dari batas izin yang ditentukan.'
                : 'Santri tercatat kembali tepat waktu. Perizinan selesai.';

            return response()->json([
                'success' => true,
                'message' => $pesan,
                'data' => new PerizinanResource($perizinan),
            ], Response::HTTP_OK);
        } catch (AbsensiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Mengunduh atau menampilkan file SVG QR Code Surat Izin Digital.
     */
    public function qrCodeSvg(int $id): HttpResponse|JsonResponse
    {
        $perizinan = Perizinan::find($id);

        if (! $perizinan || ! $perizinan->qr_code_path) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code untuk perizinan ini belum digenerate.',
            ], Response::HTTP_NOT_FOUND);
        }

        if (! Storage::disk('public')->exists($perizinan->qr_code_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas QR Code tidak ditemukan di media penyimpanan.',
            ], Response::HTTP_NOT_FOUND);
        }

        $svgContent = Storage::disk('public')->get($perizinan->qr_code_path);

        return response($svgContent, Response::HTTP_OK, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="qr-'.$perizinan->qr_token.'.svg"',
        ]);
    }
}
