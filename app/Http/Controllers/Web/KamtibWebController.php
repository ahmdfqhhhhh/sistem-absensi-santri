<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Controller;
use App\Models\KamtibLog;
use App\Models\Perizinan;
use App\Models\User;
use App\Services\KamtibService;
use App\Services\WhatsAppGatewayService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KamtibWebController extends Controller
{
    public function __construct(
        protected KamtibService $kamtibService,
        protected WhatsAppGatewayService $waService
    ) {}

    /**
     * Menampilkan antarmuka Pos Keamanan Kamtib Pesantren.
     */
    public function index(Request $request): View
    {
        $recentLogs = KamtibLog::with(['santri.asrama', 'santri.kelas', 'perizinan', 'recordedBy'])
            ->latest('waktu_log')
            ->latest('id')
            ->paginate(15);

        // Santri yang sedang berada di luar komplek saat ini
        $santriDiluar = Perizinan::with(['santri.asrama', 'santri.kelas'])
            ->where('status', 'sedang_berlangsung')
            ->orderBy('tanggal_selesai')
            ->get();

        $stats = [
            'total_keluar_hari_ini' => KamtibLog::where('tipe_log', 'checkout')->whereDate('waktu_log', today())->count(),
            'total_kembali_hari_ini' => KamtibLog::where('tipe_log', 'checkin')->whereDate('waktu_log', today())->count(),
            'santri_diluar_sekarang' => $santriDiluar->count(),
        ];

        return view('kamtib.index', compact('recentLogs', 'santriDiluar', 'stats'));
    }

    /**
     * Memproses verifikasi scan QR Code Surat Izin di Pos Gerbang Keamanan.
     */
    public function scanProcess(Request $request): RedirectResponse
    {
        $request->validate([
            'qr_token' => 'required|string|min:3',
            'action' => 'required|in:auto,checkout,checkin',
            'nama_petugas' => 'nullable|string|max:150',
            'catatan' => 'nullable|string|max:500',
        ]);

        $petugasId = $request->user()?->id ?? User::first()?->id ?? 1;
        $namaPetugas = $request->filled('nama_petugas') ? trim($request->input('nama_petugas')) : null;

        try {
            $result = $this->kamtibService->scanSuratIzin(
                qrData: $request->input('qr_token'),
                action: $request->input('action'),
                petugasId: $petugasId,
                lokasi: 'Gerbang Utama Pesantren',
                catatan: $request->input('catatan'),
                namaPetugas: $namaPetugas
            );

            $log = $result['kamtib_log'];
            $perizinan = $result['perizinan'];
            $santri = $perizinan->santri;
            $actionLabel = $result['action'] === 'checkout' ? 'Check-Out (Keluar Komplek)' : 'Check-In Kembali (Bukti Kepulangan Sah)';

            // Pemicu Notifikasi WA otomatis ke Wali Santri (FR-WAG-002)
            $this->waService->notifikasiKamtib($santri, $log, $perizinan);

            return redirect()->route('kamtib.index')->with('success', "Berhasil memverifikasi {$actionLabel} untuk santri {$santri->nama} ({$santri->nis}). Waktu kembali telah tercatat sah dan notifikasi WhatsApp terkirim ke wali santri.");

        } catch (AbsensiException $e) {
            return redirect()->route('kamtib.index')->with('error', $e->getMessage());
        }
    }
}
