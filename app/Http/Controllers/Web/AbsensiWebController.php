<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AbsensiResource;
use App\Models\Absensi;
use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Services\AbsensiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AbsensiWebController extends Controller
{
    public function __construct(
        protected AbsensiService $absensiService
    ) {}

    /**
     * Menampilkan antarmuka pemindai QR Code presensi.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $today = today();
        $nowTime = now()->format('H:i:s');

        // Pastikan sesi harian (sholat 5 waktu & sekolah) otomatis dibuat untuk hari ini
        $daftarSesi = SesiAbsensi::ensureDailySessions();

        // Tentukan sesi aktif:
        // 1. Jika ada query parameter ?sesi_id=, gunakan itu
        // 2. Jika tidak, otomatis pilih sesi yang jamnya SESUAI waktu saat ini (antara jam_mulai dan jam_selesai)
        // 3. Jika di luar rentang, pilih sesi mendatang terdekat atau sesi terakhir hari ini
        $sesiAktif = null;
        if ($request->filled('sesi_id')) {
            $sesiAktif = SesiAbsensi::find($request->query('sesi_id'));
        }

        if (!$sesiAktif && $daftarSesi->isNotEmpty()) {
            // Sesi yang sedang berjalan tepat waktu sekarang
            $sesiAktif = $daftarSesi->first(function ($s) use ($nowTime) {
                return $s->jam_mulai <= $nowTime && $s->jam_selesai >= $nowTime;
            });

            // Sesi mendatang terdekat hari ini
            if (!$sesiAktif) {
                $sesiAktif = $daftarSesi->first(function ($s) use ($nowTime) {
                    return $s->jam_mulai >= $nowTime;
                });
            }

            // Jika semua sesi hari ini sudah lewat, ambil sesi terakhir
            if (!$sesiAktif) {
                $sesiAktif = $daftarSesi->last();
            }
        }

        $totalSantri = Santri::where('status', 'aktif')->count();
        $stats = [
            'hadir'        => 0,
            'terlambat'    => 0,
            'izin'         => 0,
            'sakit'        => 0,
            'alpa'         => $totalSantri,
            'total_santri' => $totalSantri,
            'persentase'   => 0,
        ];

        $recentAbsensis = collect();
        $rekapPerAsrama = collect();

        if ($sesiAktif) {
            $absensis = \App\Models\Absensi::with(['santri.asrama', 'santri.kelas'])
                ->where('sesi_absensi_id', $sesiAktif->id)
                ->latest('waktu_absen')
                ->get();

            $hadir = $absensis->where('status', 'hadir')->count();
            $terlambat = $absensis->where('status', 'terlambat')->count();
            $izin = $absensis->where('status', 'izin')->count();
            $sakit = $absensis->where('status', 'sakit')->count();
            $tercatat = $hadir + $terlambat + $izin + $sakit;
            $alpa = max(0, $totalSantri - $tercatat);

            $stats['hadir'] = $hadir;
            $stats['terlambat'] = $terlambat;
            $stats['izin'] = $izin;
            $stats['sakit'] = $sakit;
            $stats['alpa'] = $alpa;
            $stats['total_santri'] = $totalSantri;
            $stats['persentase'] = $totalSantri > 0 ? round((($hadir + $terlambat) / $totalSantri) * 100, 1) : 0;

            $recentAbsensis = $absensis->take(15);

            // Rekap kehadiran per asrama untuk sesi ini
            $asramas = Asrama::orderBy('nama_asrama')->get();
            $rekapPerAsrama = $asramas->map(function ($asrama) use ($absensis) {
                $santriAsramaCount = Santri::where('asrama_id', $asrama->id)->where('status', 'aktif')->count();
                $hadirAsrama = $absensis->where('santri.asrama_id', $asrama->id)->whereIn('status', ['hadir', 'terlambat'])->count();
                return [
                    'nama_asrama' => $asrama->nama_asrama,
                    'gender'      => $asrama->gender,
                    'total'       => $santriAsramaCount,
                    'hadir'       => $hadirAsrama,
                    'persen'      => $santriAsramaCount > 0 ? round(($hadirAsrama / $santriAsramaCount) * 100) : 0,
                ];
            });
        }

        if (view()->exists('absensi.scan')) {
            return view('absensi.scan', compact('sesiAktif', 'daftarSesi', 'stats', 'recentAbsensis', 'rekapPerAsrama'));
        }

        return redirect()->route('santri.index');
    }

    /**
     * Memproses scan QR Code dari kamera pemindai atau form input NIS.
     */
    public function scan(Request $request): JsonResponse|RedirectResponse
    {
        $startTime = microtime(true);

        $rawNis = (string) $request->input('nis');
        $cleanNis = $this->cleanNis($rawNis);

        // Merge cleaned nis back into request for validation
        $request->merge(['nis' => $cleanNis]);

        $validator = Validator::make($request->all(), [
            'nis'     => ['required', 'string', 'max:50'],
            'sesi_id' => ['required', 'integer', 'exists:sesi_absensis,id'],
        ], [
            'nis.required' => 'Nomor Induk Santri (NIS) wajib disertakan.',
            'sesi_id.required' => 'ID Sesi Absensi wajib dipilih.',
            'sesi_id.exists' => 'Sesi absensi yang dipilih tidak valid atau sudah dihapus.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                    'meta' => [
                        'waktu_eksekusi_ms' => round((microtime(true) - $startTime) * 1000, 2),
                    ],
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sesiId = (int) $request->input('sesi_id');
        $recordedById = $request->user()?->id;

        try {
            $result = $this->absensiService->scanQr($cleanNis, $sesiId, $recordedById);

            $absensi = $result['absensi'];
            $isIzinAktif = $result['is_izin_aktif'];
            $activeIzin = $result['perizinan_aktif'];
            $processingTimeMs = $result['processing_time_ms'];

            $namaSantri = $absensi->santri?->nama ?? 'Santri';
            $statusLabel = match ($absensi->status) {
                'hadir' => 'Hadir',
                'terlambat' => 'Terlambat',
                'izin' => 'Izin',
                'sakit' => 'Sakit',
                default => ucfirst((string) $absensi->status),
            };

            $message = $isIzinAktif
                ? "Presensi {$namaSantri} otomatis dialihkan menjadi status '{$statusLabel}' karena santri memiliki izin aktif ({$activeIzin?->nomor_surat})."
                : "Presensi {$namaSantri} berhasil dicatat dengan status '{$statusLabel}'!";

            if ($request->expectsJson() || $request->ajax()) {
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
            }

            return redirect()->back()->with('success', $message);
        } catch (AbsensiException $e) {
            $totalDurationMs = round((microtime(true) - $startTime) * 1000, 2);

            if ($request->expectsJson() || $request->ajax()) {
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

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bersihkan input NIS dari format JSON atau prefix label barcode/QR.
     */
    protected function cleanNis(string $input): string
    {
        $input = trim($input);

        // Jika berbentuk format JSON, ekstrak field nis
        if (str_starts_with($input, '{') && str_ends_with($input, '}')) {
            $json = json_decode($input, true);
            if (isset($json['nis'])) {
                return trim((string) $json['nis']);
            }
        }

        // Jika diawali prefix "NIS:" atau sejenisnya
        if (preg_match('/^nis\s*[:=]\s*(.+)$/i', $input, $matches)) {
            return trim($matches[1]);
        }

        return $input;
    }

    /**
     * Tampilan checklist presensi batch / kelompok.
     */
    public function batchIndex(Request $request): View|RedirectResponse
    {
        $user    = $request->user();
        $asramas = Asrama::orderBy('nama_asrama')->get();
        $kelases = Kelas::orderBy('nama_kelas')->get();

        $daftarSesi = SesiAbsensi::ensureDailySessions();
        $sesiAktif = null;
        if ($request->filled('sesi_id')) {
            $sesiAktif = SesiAbsensi::find($request->query('sesi_id'));
        }
        if (!$sesiAktif) {
            $sesiAktif = $daftarSesi->first();
        }

        // Existing absensis placeholder – can be populated later
        $existingAbsensis = collect();

        $santriQuery = Santri::with(['asrama', 'kelas'])->where('status', 'aktif');

        if ($user && $user->gender === 'putra') {
            $santriQuery->where('jenis_kelamin', 'L');
        } elseif ($user && $user->gender === 'putri') {
            $santriQuery->where('jenis_kelamin', 'P');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $santriQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('asrama_id')) {
            $santriQuery->where('asrama_id', $request->integer('asrama_id'));
        }

        if ($request->filled('kelas_id')) {
            $santriQuery->where('kelas_id', $request->integer('kelas_id'));
        }

        $santris = $santriQuery->orderBy('nama')->paginate(50)->withQueryString();

        if (view()->exists('absensi.batch')) {
            return view('absensi.batch', compact('santris', 'asramas', 'kelases', 'sesiAktif', 'daftarSesi', 'existingAbsensis'));
        }

        return redirect()->route('santri.index');
    }

    /**
     * Menyimpan hasil checklist presensi batch.
     */
    public function batchStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sesi_id' => 'required|exists:sesi_absensis,id',
            'statuses' => 'required|array',
            'statuses.*' => 'required|in:hadir,terlambat,izin,sakit,alpa',
        ]);

        $sesiId = (int) $validated['sesi_id'];
        $now = now();
        $recordedById = $request->user()?->id;

        DB::transaction(function () use ($validated, $sesiId, $now, $recordedById) {
            foreach ($validated['statuses'] as $santriId => $status) {
                Absensi::updateOrCreate(
                    [
                        'santri_id' => (int) $santriId,
                        'sesi_absensi_id' => $sesiId,
                    ],
                    [
                        'status' => $status,
                        'waktu_absen' => $now,
                        'metode' => 'manual',
                        'recorded_by' => $recordedById,
                        'keterangan' => 'Dicatat massal via Presensi Cepat / Batch Checklist',
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Presensi batch ' . count($validated['statuses']) . ' santri berhasil disimpan.');
    }

    /**
     * Mengambil seluruh data deskriptor wajah santri aktif untuk dicocokkan oleh scanner kamera AI.
     */
    public function getFaceDescriptors(): JsonResponse
    {
        $santris = Santri::query()
            ->select(['id', 'nis', 'nama', 'kelas_id', 'asrama_id', 'foto_path', 'face_descriptor'])
            ->with(['asrama:id,nama_asrama', 'kelas:id,nama_kelas'])
            ->where('status', 'aktif')
            ->whereNotNull('face_descriptor')
            ->get()
            ->map(function ($santri) {
                return [
                    'id'         => $santri->id,
                    'nis'        => $santri->nis,
                    'nama'       => $santri->nama,
                    'kelas'      => $santri->kelas->nama_kelas ?? '-',
                    'asrama'     => $santri->asrama->nama_asrama ?? '-',
                    'foto_url'   => $santri->foto_url,
                    'descriptor' => $santri->face_descriptor,
                ];
            });

        return response()->json([
            'success' => true,
            'total'   => $santris->count(),
            'data'    => $santris,
        ]);
    }
}
