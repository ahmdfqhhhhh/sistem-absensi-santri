<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AbsensiResource;
use App\Models\Absensi;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Services\AbsensiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AbsensiWebController extends Controller
{
    /**
     * Dependency injection untuk AbsensiService.
     */
    public function __construct(
        protected AbsensiService $absensiService
    ) {}

    /**
     * Menampilkan antarmuka pemindai QR Code presensi santri.
     */
    public function index(Request $request): View
    {
        // 1. Ambil seluruh sesi absensi yang berstatus 'dibuka'
        $daftarSesi = SesiAbsensi::query()
            ->dibuka()
            ->orderByDesc('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        // 2. Tentukan sesi aktif (berdasarkan parameter query atau sesi aktif hari ini / terkini)
        $sesiAktif = null;
        if ($request->filled('sesi_id')) {
            $sesiAktif = $daftarSesi->firstWhere('id', (int) $request->integer('sesi_id'));
        }

        if (! $sesiAktif) {
            $sesiAktif = $daftarSesi->where('tanggal', today()->toDateString())->first()
                ?? $daftarSesi->first();
        }

        // 3. Hitung statistik presensi untuk sesi aktif yang terpilih
        $stats = [
            'total_santri' => Santri::where('status', 'aktif')->count(),
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'total_tercatat' => 0,
        ];

        $recentAbsensis = collect();

        if ($sesiAktif) {
            $absensiCounts = Absensi::query()
                ->where('sesi_absensi_id', $sesiAktif->id)
                ->selectRaw('status, COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status')
                ->toArray();

            $stats['hadir'] = $absensiCounts['hadir'] ?? 0;
            $stats['terlambat'] = $absensiCounts['terlambat'] ?? 0;
            $stats['izin'] = $absensiCounts['izin'] ?? 0;
            $stats['sakit'] = $absensiCounts['sakit'] ?? 0;
            $stats['total_tercatat'] = array_sum($absensiCounts);

            // 4. Riwayat presensi terbaru (10 santri terakhir yang scan)
            $recentAbsensis = Absensi::query()
                ->with(['santri.kelas', 'santri.asrama'])
                ->where('sesi_absensi_id', $sesiAktif->id)
                ->latest('waktu_absen')
                ->latest('id')
                ->take(10)
                ->get();
        }

        return view('absensi.scan', [
            'sesiAktif' => $sesiAktif,
            'sesi' => $sesiAktif, // alias kompatibilitas
            'daftarSesi' => $daftarSesi,
            'stats' => $stats,
            'recentAbsensis' => $recentAbsensis,
        ]);
    }

    /**
     * Endpoint pemindaian QR Code santri (mendukung request AJAX dari antarmuka Web).
     */
    public function scan(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        $validator = Validator::make($request->all(), [
            'nis' => ['required', 'string', 'max:50'],
            'sesi_id' => ['required', 'integer', 'min:1'],
            'recorded_by' => ['nullable', 'integer', 'exists:users,id'],
        ], [
            'nis.required' => 'Nomor Induk Santri (NIS) wajib disertakan.',
            'sesi_id.required' => 'ID Sesi Absensi wajib ditentukan.',
            'sesi_id.integer' => 'ID Sesi harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak valid.',
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
                ? "Presensi dialihkan ke status '".ucfirst($absensi->status)."' (Izin Aktif: {$activeIzin?->nomor_surat})."
                : "Presensi berhasil dicatat! Status: '".ucfirst($absensi->status)."'.";

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
