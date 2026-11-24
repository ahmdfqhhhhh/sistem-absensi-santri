<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KamtibLog;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Mengarahkan pengguna ke beranda dashboard sesuai dengan role masing-masing.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isKeamanan()) {
            return redirect()->route('kamtib.index');
        }

        if ($user->isWali()) {
            return redirect()->route('wali.portal');
        }

        if ($user->isBendahara()) {
            return redirect()->route('bendahara.index');
        }

        // Pastikan sesi harian siap
        $sesiHariIni = SesiAbsensi::ensureDailySessions();

        $totalSantri = Santri::where('status', 'aktif')->count();
        $totalPutra  = Santri::where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
        $totalPutri  = Santri::where('status', 'aktif')->where('jenis_kelamin', 'P')->count();

        // Absensi hari ini
        $todayAbsensis = Absensi::whereDate('waktu_absen', today())->get();
        $hadirCount     = $todayAbsensis->where('status', 'hadir')->count();
        $terlambatCount = $todayAbsensis->where('status', 'terlambat')->count();
        $izinCount      = $todayAbsensis->where('status', 'izin')->count();
        $sakitCount     = $todayAbsensis->where('status', 'sakit')->count();
        $alpaCount      = $todayAbsensis->where('status', 'alpa')->count();
        $totalPresensi  = $todayAbsensis->count();
        $persenHadir    = $totalPresensi > 0 ? round(($hadirCount / $totalPresensi) * 100) : 0;

        // Santri di luar pondok (surat izin disetujui / berlangsung)
        $santriDiLuar = Perizinan::whereIn('status', ['disetujui', 'sedang_berlangsung'])
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_selesai', '>=', now())
            ->count();

        $stats = [
            'total_santri'     => $totalSantri,
            'total_putra'      => $totalPutra,
            'total_putri'      => $totalPutri,
            'hadir'            => $hadirCount,
            'terlambat'        => $terlambatCount,
            'izin'             => $izinCount,
            'sakit'            => $sakitCount,
            'alpa'             => $alpaCount,
            'total_presensi'   => $totalPresensi,
            'persentase_hadir' => $persenHadir,
            'santri_diluar'    => $santriDiLuar,
        ];

        $totalUsers   = User::count();
        $totalMusyrif = User::where('role', User::ROLE_MUSYRIF)->count();
        $totalKamtib  = User::where('role', User::ROLE_KEAMANAN)->count();

        $recentAbsensi = Absensi::with(['santri', 'sesiAbsensi'])
            ->latest('waktu_absen')
            ->take(6)
            ->get();

        $recentKamtib = KamtibLog::with(['santri', 'perizinan'])
            ->latest('waktu_log')
            ->take(6)
            ->get();

        // Early Warning System: santri dengan akumulasi alpa >= 2 dalam 30 hari terakhir
        $santriEws = Santri::with(['asrama', 'kelas'])
            ->withCount(['absensis as alpa_count' => function ($q) {
                $q->where('status', 'alpa')
                  ->where('waktu_absen', '>=', now()->subDays(30));
            }])
            ->having('alpa_count', '>=', 2)
            ->orderByDesc('alpa_count')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'sesiHariIni',
            'recentAbsensi',
            'recentKamtib',
            'santriEws',
            'totalUsers',
            'totalMusyrif',
            'totalKamtib'
        ));
    }
}
