<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapWebController extends Controller
{
    /**
     * Nama-nama bulan dalam Bahasa Indonesia.
     *
     * @var array<int, string>
     */
    protected array $bulanNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Menampilkan Halaman Rekapitulasi Absensi (Harian atau Bulanan).
     */
    public function indexAbsensi(Request $request): View
    {
        $tipe = $request->input('tipe');
        if (! $tipe) {
            $tipe = $request->has('tanggal') ? 'harian' : 'bulanan';
        }

        if ($tipe === 'harian') {
            $data = $this->prepareAbsensiHarianData($request);
            $data['tipe'] = 'harian';

            return view('rekap.absensi', $data);
        }

        $data = $this->prepareAbsensiData($request);
        $data['tipe'] = 'bulanan';

        return view('rekap.absensi', $data);
    }

    /**
     * Ekspor Rekapitulasi Absensi ke Excel (.xls Spreadsheet).
     * Mendukung tipe harian maupun bulanan berdasarkan query param `tipe`.
     */
    public function exportAbsensiExcel(Request $request): StreamedResponse
    {
        $tipe = $request->input('tipe', 'bulanan');

        if ($tipe === 'harian') {
            $data = $this->prepareAbsensiHarianData($request);
            $data['tipe'] = 'harian';
            $fileName = 'Rekap_Absensi_Harian_' . $data['tanggal'] . '.xls';
        } else {
            $data = $this->prepareAbsensiData($request);
            $data['tipe'] = 'bulanan';
            $fileName = 'Rekap_Absensi_Santri_' . $data['bulanNama'] . '_' . $data['tahun'] . '.xls';
        }

        return response()->streamDownload(function () use ($data, $tipe) {
            echo view($tipe === 'harian' ? 'rekap.absensi_harian_excel' : 'rekap.absensi_excel', $data)->render();
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Tampilan Cetak PDF Rekapitulasi Absensi (Print-Ready Layout).
     * Mendukung tipe harian maupun bulanan berdasarkan query param `tipe`.
     */
    public function exportAbsensiPdf(Request $request): View
    {
        $tipe = $request->input('tipe', 'bulanan');

        if ($tipe === 'harian') {
            $data = $this->prepareAbsensiHarianData($request);
            $data['tipe'] = 'harian';

            return view('rekap.absensi_harian_pdf', $data);
        }

        $data = $this->prepareAbsensiData($request);
        $data['tipe'] = 'bulanan';

        return view('rekap.absensi_pdf', $data);
    }

    /**
     * Menampilkan Halaman Rekapitulasi Absensi Harian (direct route).
     */
    public function indexAbsensiHarian(Request $request): View
    {
        $data = $this->prepareAbsensiHarianData($request);
        $data['tipe'] = 'harian';

        return view('rekap.absensi', $data);
    }

    /**
     * Ekspor Rekapitulasi Absensi Harian ke Excel (.xls Spreadsheet) (direct route).
     */
    public function exportAbsensiHarianExcel(Request $request): StreamedResponse
    {
        $data = $this->prepareAbsensiHarianData($request);
        $data['tipe'] = 'harian';
        $fileName = 'Rekap_Absensi_Harian_' . $data['tanggal'] . '.xls';

        return response()->streamDownload(function () use ($data) {
            echo view('rekap.absensi_harian_excel', $data)->render();
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Tampilan Cetak PDF Rekapitulasi Absensi Harian (direct route).
     */
    public function exportAbsensiHarianPdf(Request $request): View
    {
        $data = $this->prepareAbsensiHarianData($request);
        $data['tipe'] = 'harian';

        return view('rekap.absensi_harian_pdf', $data);
    }

    /**
     * Menampilkan Halaman Rekapitulasi Perizinan Santri (Harian atau Bulanan).
     */
    public function indexPerizinan(Request $request): View
    {
        $tipe = $request->input('tipe');
        if (! $tipe) {
            $tipe = ($request->has('bulan') && ! $request->has('tanggal')) ? 'bulanan' : 'harian';
        }

        if ($tipe === 'bulanan') {
            $data = $this->preparePerizinanData($request);
            $data['tipe'] = 'bulanan';

            return view('rekap.perizinan', $data);
        }

        $data = $this->preparePerizinanHarianData($request);
        $data['tipe'] = 'harian';

        return view('rekap.perizinan', $data);
    }

    /**
     * Ekspor Rekapitulasi Perizinan Santri ke Excel (.xls Spreadsheet).
     * Mendukung tipe harian maupun bulanan berdasarkan query param `tipe`.
     */
    public function exportPerizinanExcel(Request $request): StreamedResponse
    {
        $tipe = $request->input('tipe', 'bulanan');

        if ($tipe === 'harian') {
            $data = $this->preparePerizinanHarianData($request);
            $data['tipe'] = 'harian';
            $fileName = 'Rekap_Perizinan_Harian_' . $data['tanggal'] . '.xls';
        } else {
            $data = $this->preparePerizinanData($request);
            $data['tipe'] = 'bulanan';
            $fileName = 'Rekap_Perizinan_Santri_' . $data['bulanNama'] . '_' . $data['tahun'] . '.xls';
        }

        return response()->streamDownload(function () use ($data, $tipe) {
            echo view($tipe === 'harian' ? 'rekap.perizinan_harian_excel' : 'rekap.perizinan_excel', $data)->render();
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Tampilan Cetak PDF Rekapitulasi Perizinan (Print-Ready Layout).
     * Mendukung tipe harian maupun bulanan berdasarkan query param `tipe`.
     */
    public function exportPerizinanPdf(Request $request): View
    {
        $tipe = $request->input('tipe', 'bulanan');

        if ($tipe === 'harian') {
            $data = $this->preparePerizinanHarianData($request);
            $data['tipe'] = 'harian';

            return view('rekap.perizinan_harian_pdf', $data);
        }

        $data = $this->preparePerizinanData($request);
        $data['tipe'] = 'bulanan';

        return view('rekap.perizinan_pdf', $data);
    }

    /**
     * Menampilkan Halaman Rekapitulasi Perizinan Santri Harian (direct route).
     */
    public function indexPerizinanHarian(Request $request): View
    {
        $data = $this->preparePerizinanHarianData($request);
        $data['tipe'] = 'harian';

        return view('rekap.perizinan', $data);
    }

    /**
     * Ekspor Rekapitulasi Perizinan Harian ke Excel (.xls Spreadsheet) (direct route).
     */
    public function exportPerizinanHarianExcel(Request $request): StreamedResponse
    {
        $data = $this->preparePerizinanHarianData($request);
        $data['tipe'] = 'harian';
        $fileName = 'Rekap_Perizinan_Harian_' . $data['tanggal'] . '.xls';

        return response()->streamDownload(function () use ($data) {
            echo view('rekap.perizinan_harian_excel', $data)->render();
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Tampilan Cetak PDF Rekapitulasi Perizinan Harian (direct route).
     */
    public function exportPerizinanHarianPdf(Request $request): View
    {
        $data = $this->preparePerizinanHarianData($request);
        $data['tipe'] = 'harian';

        return view('rekap.perizinan_harian_pdf', $data);
    }

    /**
     * Menyiapkan data agregasi absensi santri per bulan.
     *
     * @return array<string, mixed>
     */
    protected function prepareAbsensiData(Request $request): array
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $gender = $request->input('gender', 'semua');
        $asramaId = $request->filled('asrama_id') ? (int) $request->input('asrama_id') : null;
        $kelasId = $request->filled('kelas_id') ? (int) $request->input('kelas_id') : null;

        $user = $request->user();
        if ($user && $user->gender === 'putra') {
            $gender = 'putra';
        } elseif ($user && $user->gender === 'putri') {
            $gender = 'putri';
        }

        $santriQuery = Santri::with(['asrama', 'kelas'])->orderBy('nama');

        if ($gender === 'putra') {
            $santriQuery->where('jenis_kelamin', 'L');
        } elseif ($gender === 'putri') {
            $santriQuery->where('jenis_kelamin', 'P');
        }

        if ($asramaId) {
            $santriQuery->where('asrama_id', $asramaId);
        }

        if ($kelasId) {
            $santriQuery->where('kelas_id', $kelasId);
        }

        $santris = $santriQuery->get();

        // Ambil data absensi pada bulan & tahun tersebut
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $absensis = Absensi::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('santri_id', $santris->pluck('id'))
            ->get()
            ->groupBy('santri_id');

        $rekapList = [];
        $totalHadir = 0;
        $totalTerlambat = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;

        foreach ($santris as $santri) {
            $records = $absensis->get($santri->id, collect());
            $hadir = $records->where('status', 'hadir')->count();
            $terlambat = $records->where('status', 'terlambat')->count();
            $izin = $records->where('status', 'izin')->count();
            $sakit = $records->where('status', 'sakit')->count();
            $alpa = $records->where('status', 'alpa')->count();
            $totalSesi = $records->count();

            $persentase = $totalSesi > 0 ? round((($hadir + $terlambat) / $totalSesi) * 100, 1) : 0;

            $totalHadir += $hadir;
            $totalTerlambat += $terlambat;
            $totalIzin += $izin;
            $totalSakit += $sakit;
            $totalAlpa += $alpa;

            $rekapList[] = [
                'santri' => $santri,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total' => $totalSesi,
                'persentase' => $persentase,
                'sesi_status' => [],
                'latest_record' => null,
            ];
        }

        $asramas = Asrama::orderBy('nama_asrama')->get();
        $kelases = Kelas::orderBy('nama_kelas')->get();

        return [
            'rekapList' => $rekapList,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulanNama' => $this->bulanNames[$bulan] ?? 'Bulan',
            'gender' => $gender,
            'asramaId' => $asramaId,
            'kelasId' => $kelasId,
            'asramas' => $asramas,
            'kelases' => $kelases,
            'bulanNames' => $this->bulanNames,
            // Variabel fallback agar view unified tidak error saat tipe bulanan
            'tanggal' => now()->toDateString(),
            'tanggalFormatted' => now()->translatedFormat('l, d F Y'),
            'allSesis' => collect(),
            'sesiId' => null,
            'sesiBreakdown' => [],
            'santriIzin' => [],
            'santriTerlambat' => [],
            'santriSakit' => [],
            'santriAlpa' => [],
            'waText' => '',
            'summary' => [
                'santri_count' => count($santris),
                'hadir' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'izin' => $totalIzin,
                'sakit' => $totalSakit,
                'alpa' => $totalAlpa,
                'total_sesi' => $totalHadir + $totalTerlambat + $totalIzin + $totalSakit + $totalAlpa,
            ],
        ];
    }

    /**
     * Menyiapkan data rekapan surat perizinan santri per bulan.
     *
     * @return array<string, mixed>
     */
    protected function preparePerizinanData(Request $request): array
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $gender = $request->input('gender', 'semua');
        $status = $request->input('status', 'semua');
        $jenisIzin = $request->input('jenis_izin', 'semua');

        $user = $request->user();
        if ($user && $user->gender === 'putra') {
            $gender = 'putra';
        } elseif ($user && $user->gender === 'putri') {
            $gender = 'putri';
        }

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        $query = Perizinan::with(['santri.asrama', 'santri.kelas', 'approvedBy'])
            ->whereBetween('tanggal_mulai', [$startDate, $endDate])
            ->latest('tanggal_mulai');

        if ($gender === 'putra') {
            $query->whereHas('santri', fn ($q) => $q->where('jenis_kelamin', 'L'));
        } elseif ($gender === 'putri') {
            $query->whereHas('santri', fn ($q) => $q->where('jenis_kelamin', 'P'));
        }

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        if ($jenisIzin !== 'semua') {
            $query->where('jenis_izin', $jenisIzin);
        }

        $perizinans = $query->get();

        return [
            'perizinans' => $perizinans,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulanNama' => $this->bulanNames[$bulan] ?? 'Bulan',
            'gender' => $gender,
            'status' => $status,
            'jenisIzin' => $jenisIzin,
            'bulanNames' => $this->bulanNames,
            // Variabel fallback agar view unified tidak error saat tipe bulanan
            'tanggal' => now()->toDateString(),
            'tanggalFormatted' => now()->translatedFormat('l, d F Y'),
            'stats' => [
                'total' => $perizinans->count(),
                'disetujui' => $perizinans->where('status', 'disetujui')->count(),
                'sedang_berlangsung' => $perizinans->where('status', 'sedang_berlangsung')->count(),
                'selesai' => $perizinans->where('status', 'selesai')->count(),
                'terlambat_kembali' => $perizinans->where('status', 'terlambat_kembali')->count(),
                'pending' => $perizinans->where('status', 'pending')->count(),
                'ditolak' => $perizinans->where('status', 'ditolak')->count(),
            ],
        ];
    }

    /**
     * Menyiapkan data agregasi absensi santri harian (per tanggal).
     *
     * @return array<string, mixed>
     */
    protected function prepareAbsensiHarianData(Request $request): array
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $gender = $request->input('gender', 'semua');
        $asramaId = $request->filled('asrama_id') ? (int) $request->input('asrama_id') : null;
        $kelasId = $request->filled('kelas_id') ? (int) $request->input('kelas_id') : null;
        $sesiId = $request->filled('sesi_id') ? (int) $request->input('sesi_id') : null;

        $user = $request->user();
        if ($user && $user->gender === 'putra') {
            $gender = 'putra';
        } elseif ($user && $user->gender === 'putri') {
            $gender = 'putri';
        }

        // Jika tanggal hari ini, pastikan default sesi sholat sudah ter-generate
        if ($tanggal === now()->toDateString()) {
            SesiAbsensi::ensureDailySessions();
        }

        // Ambil sesi absensi pada tanggal tersebut
        $allSesis = SesiAbsensi::whereDate('tanggal', $tanggal)->orderBy('jam_mulai')->get();

        $santriQuery = Santri::with(['asrama', 'kelas'])->orderBy('nama');

        if ($gender === 'putra') {
            $santriQuery->where('jenis_kelamin', 'L');
        } elseif ($gender === 'putri') {
            $santriQuery->where('jenis_kelamin', 'P');
        }

        if ($asramaId) {
            $santriQuery->where('asrama_id', $asramaId);
        }

        if ($kelasId) {
            $santriQuery->where('kelas_id', $kelasId);
        }

        $santris = $santriQuery->get();

        // Ambil semua absensi pada tanggal/sesi tersebut
        $absensiQuery = Absensi::with(['sesiAbsensi', 'recordedBy'])
            ->whereIn('santri_id', $santris->pluck('id'));

        if ($sesiId) {
            $absensiQuery->where('sesi_absensi_id', $sesiId);
        } elseif ($allSesis->isNotEmpty()) {
            $absensiQuery->whereIn('sesi_absensi_id', $allSesis->pluck('id'));
        } else {
            $absensiQuery->whereDate('waktu_absen', $tanggal);
        }

        $absensis = $absensiQuery->get()->groupBy('santri_id');

        $rekapList = [];
        $totalHadir = 0;
        $totalTerlambat = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;

        $santriIzin = [];
        $santriTerlambat = [];
        $santriSakit = [];
        $santriAlpa = [];

        foreach ($santris as $santri) {
            $records = $absensis->get($santri->id, collect());

            $sesiStatus = [];
            foreach ($allSesis as $s) {
                $rec = $records->firstWhere('sesi_absensi_id', $s->id);
                $sesiStatus[$s->id] = $rec ? $rec->status : null;
            }

            $hadir = $records->where('status', 'hadir')->count();
            $terlambat = $records->where('status', 'terlambat')->count();
            $izin = $records->where('status', 'izin')->count();
            $sakit = $records->where('status', 'sakit')->count();
            $alpa = $records->where('status', 'alpa')->count();
            $totalRecorded = $records->count();

            $totalHadir += $hadir;
            $totalTerlambat += $terlambat;
            $totalIzin += $izin;
            $totalSakit += $sakit;
            $totalAlpa += $alpa;

            $persentase = $totalRecorded > 0 ? round((($hadir + $terlambat) / $totalRecorded) * 100, 1) : 0;

            $asramaName = $santri->asrama->nama_asrama ?? '-';
            $kelasName = $santri->kelas->nama_kelas ?? '-';

            // Catat data santri yang berhalangan/terlambat untuk laporan WhatsApp
            foreach ($records as $r) {
                $sesiNama = $r->sesiAbsensi->nama_sesi ?? 'Presensi';
                $sesiSingkat = str_replace(['Sholat ', ' Berjamaah', 'Presensi '], '', $sesiNama);

                if ($r->status === 'izin') {
                    $santriIzin[] = [
                        'nama' => $santri->nama,
                        'nis' => $santri->nis,
                        'asrama' => $asramaName,
                        'kelas' => $kelasName,
                        'sesi' => $sesiSingkat,
                        'keterangan' => $r->keterangan ?? 'Izin',
                    ];
                } elseif ($r->status === 'terlambat') {
                    $santriTerlambat[] = [
                        'nama' => $santri->nama,
                        'nis' => $santri->nis,
                        'asrama' => $asramaName,
                        'kelas' => $kelasName,
                        'sesi' => $sesiSingkat,
                        'waktu' => $r->waktu_absen ? $r->waktu_absen->format('H:i') : '-',
                    ];
                } elseif ($r->status === 'sakit') {
                    $santriSakit[] = [
                        'nama' => $santri->nama,
                        'nis' => $santri->nis,
                        'asrama' => $asramaName,
                        'kelas' => $kelasName,
                        'sesi' => $sesiSingkat,
                        'keterangan' => $r->keterangan ?? 'Sakit',
                    ];
                } elseif ($r->status === 'alpa') {
                    $santriAlpa[] = [
                        'nama' => $santri->nama,
                        'nis' => $santri->nis,
                        'asrama' => $asramaName,
                        'kelas' => $kelasName,
                        'sesi' => $sesiSingkat,
                    ];
                }
            }

            $rekapList[] = [
                'santri' => $santri,
                'sesi_status' => $sesiStatus,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total' => $totalRecorded,
                'persentase' => $persentase,
                'latest_record' => $records->sortByDesc('waktu_absen')->first(),
            ];
        }

        // Ambil perizinan resmi yang disetujui / aktif pada tanggal tersebut
        $activePerizinans = Perizinan::with(['santri.asrama', 'santri.kelas'])
            ->whereIn('status', ['disetujui', 'sedang_berlangsung'])
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->whereIn('santri_id', $santris->pluck('id'))
            ->get();

        foreach ($activePerizinans as $pz) {
            $snt = $pz->santri;
            if ($snt && ! collect($santriIzin)->contains('nama', $snt->nama)) {
                $santriIzin[] = [
                    'nama' => $snt->nama,
                    'nis' => $snt->nis,
                    'asrama' => $snt->asrama->nama_asrama ?? '-',
                    'kelas' => $snt->kelas->nama_kelas ?? '-',
                    'sesi' => 'Izin Keluar / Pulang',
                    'keterangan' => $pz->keperluan ?? ucfirst($pz->jenis_izin),
                ];
            }
        }

        // Breakdown per sesi
        $sesiBreakdown = [];
        foreach ($allSesis as $s) {
            $sesiAbs = Absensi::where('sesi_absensi_id', $s->id)
                ->whereIn('santri_id', $santris->pluck('id'))
                ->get();

            $sesiBreakdown[] = [
                'sesi' => $s,
                'hadir' => $sesiAbs->where('status', 'hadir')->count(),
                'terlambat' => $sesiAbs->where('status', 'terlambat')->count(),
                'izin' => $sesiAbs->where('status', 'izin')->count(),
                'sakit' => $sesiAbs->where('status', 'sakit')->count(),
                'alpa' => $sesiAbs->where('status', 'alpa')->count(),
                'total' => $sesiAbs->count(),
            ];
        }

        $asramas = Asrama::orderBy('nama_asrama')->get();
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $carbonDate = Carbon::parse($tanggal);
        $bulan = (int) $carbonDate->month;
        $tahun = (int) $carbonDate->year;

        // Susun teks format WhatsApp yang ringkas, rapi, dan siap dikirim ke grup
        $tanggalFormatted = $carbonDate->translatedFormat('l, d F Y');
        $namaFilterAsrama = $asramaId ? ($asramas->firstWhere('id', $asramaId)->nama_asrama ?? 'Semua Asrama') : 'Semua Asrama';
        $namaFilterKelas = $kelasId ? ($kelases->firstWhere('id', $kelasId)->nama_kelas ?? 'Semua Kelas') : 'Semua Kelas';
        $namaFilterSesi = $sesiId ? ($allSesis->firstWhere('id', $sesiId)->nama_sesi ?? 'Seluruh Sesi') : 'Seluruh Sesi';

        $waLines = [];
        $waLines[] = "*LAPORAN PRESENSI HARIAN SANTRI*";
        $waLines[] = "*PP. MIFTAHUL HUDA KROYA*";
        $waLines[] = "";
        $waLines[] = "📅 *Hari, Tanggal:* " . $tanggalFormatted;
        if ($asramaId) {
            $waLines[] = "🏢 *Asrama:* " . $namaFilterAsrama;
        }
        if ($kelasId) {
            $waLines[] = "📚 *Kelas:* " . $namaFilterKelas;
        }
        if ($sesiId) {
            $waLines[] = "⏱️ *Sesi:* " . $namaFilterSesi;
        }
        $waLines[] = "";
        $waLines[] = "══════════════════════";
        $waLines[] = "📋 *DAFTAR SANTRI BERHALANGAN / CATATAN:*";
        $waLines[] = "";

        // IZIN
        $waLines[] = "📝 *IZIN (" . count($santriIzin) . " Santri):*";
        if (count($santriIzin) > 0) {
            $no = 1;
            foreach ($santriIzin as $it) {
                $waLines[] = "{$no}. *{$it['nama']}* - Asrama {$it['asrama']} | {$it['kelas']} ({$it['keterangan']})";
                $no++;
            }
        } else {
            $waLines[] = "- Nihil";
        }
        $waLines[] = "";

        // TERLAMBAT
        $waLines[] = "⏰ *TERLAMBAT (" . count($santriTerlambat) . " Santri):*";
        if (count($santriTerlambat) > 0) {
            $no = 1;
            foreach ($santriTerlambat as $it) {
                $waLines[] = "{$no}. *{$it['nama']}* - Asrama {$it['asrama']} | {$it['kelas']} (Sesi: {$it['sesi']})";
                $no++;
            }
        } else {
            $waLines[] = "- Nihil";
        }
        $waLines[] = "";

        // SAKIT
        $waLines[] = "🏥 *SAKIT (" . count($santriSakit) . " Santri):*";
        if (count($santriSakit) > 0) {
            $no = 1;
            foreach ($santriSakit as $it) {
                $waLines[] = "{$no}. *{$it['nama']}* - Asrama {$it['asrama']} | {$it['kelas']}";
                $no++;
            }
        } else {
            $waLines[] = "- Nihil";
        }
        $waLines[] = "";

        // ALPA
        $waLines[] = "❌ *ALPA / TANPA KETERANGAN (" . count($santriAlpa) . " Santri):*";
        if (count($santriAlpa) > 0) {
            $no = 1;
            foreach ($santriAlpa as $it) {
                $waLines[] = "{$no}. *{$it['nama']}* - Asrama {$it['asrama']} | {$it['kelas']} (Sesi: {$it['sesi']})";
                $no++;
            }
        } else {
            $waLines[] = "- Nihil";
        }

        $waLines[] = "";
        $waLines[] = "══════════════════════";
        $waLines[] = "_Laporan otomatis Sistem E-Absensi Santri_";

        $waText = implode("\n", $waLines);

        return [
            'rekapList' => $rekapList,
            'tanggal' => $tanggal,
            'tanggalFormatted' => $tanggalFormatted,
            'gender' => $gender,
            'asramaId' => $asramaId,
            'kelasId' => $kelasId,
            'sesiId' => $sesiId,
            'asramas' => $asramas,
            'kelases' => $kelases,
            'allSesis' => $allSesis,
            'sesiBreakdown' => $sesiBreakdown,
            'santriIzin' => $santriIzin,
            'santriTerlambat' => $santriTerlambat,
            'santriSakit' => $santriSakit,
            'santriAlpa' => $santriAlpa,
            'waText' => $waText,
            // Variabel fallback agar view unified tidak error saat tipe harian
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulanNama' => $this->bulanNames[$bulan] ?? 'Bulan',
            'bulanNames' => $this->bulanNames,
            'summary' => [
                'santri_count' => count($santris),
                'hadir' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'izin' => $totalIzin,
                'sakit' => $totalSakit,
                'alpa' => $totalAlpa,
                'total_presensi' => $totalHadir + $totalTerlambat + $totalIzin + $totalSakit + $totalAlpa,
            ],
        ];
    }

    /**
     * Menyiapkan data rekapan surat perizinan santri harian (per tanggal).
     *
     * @return array<string, mixed>
     */
    protected function preparePerizinanHarianData(Request $request): array
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $gender = $request->input('gender', 'semua');
        $status = $request->input('status', 'semua');
        $jenisIzin = $request->input('jenis_izin', 'semua');

        $user = $request->user();
        if ($user && $user->gender === 'putra') {
            $gender = 'putra';
        } elseif ($user && $user->gender === 'putri') {
            $gender = 'putri';
        }

        $query = Perizinan::with(['santri.asrama', 'santri.kelas', 'approvedBy', 'kamtibLogs'])
            ->where(function ($q) use ($tanggal) {
                $q->where(function ($sub) use ($tanggal) {
                    $sub->whereDate('tanggal_mulai', '<=', $tanggal)
                        ->whereDate('tanggal_selesai', '>=', $tanggal);
                })
                ->orWhereDate('tanggal_mulai', $tanggal)
                ->orWhereDate('tanggal_kembali_aktual', $tanggal);
            })
            ->latest('tanggal_mulai');

        if ($gender === 'putra') {
            $query->whereHas('santri', fn ($q) => $q->where('jenis_kelamin', 'L'));
        } elseif ($gender === 'putri') {
            $query->whereHas('santri', fn ($q) => $q->where('jenis_kelamin', 'P'));
        }

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        if ($jenisIzin !== 'semua') {
            $query->where('jenis_izin', $jenisIzin);
        }

        $perizinans = $query->get();
        $carbonDate = Carbon::parse($tanggal);
        $bulan = (int) $carbonDate->month;
        $tahun = (int) $carbonDate->year;

        return [
            'perizinans' => $perizinans,
            'tanggal' => $tanggal,
            'tanggalFormatted' => $carbonDate->translatedFormat('l, d F Y'),
            'gender' => $gender,
            'status' => $status,
            'jenisIzin' => $jenisIzin,
            // Variabel fallback agar view unified tidak error saat tipe harian
            'bulan' => $bulan,
            'tahun' => $tahun,
            'bulanNama' => $this->bulanNames[$bulan] ?? 'Bulan',
            'bulanNames' => $this->bulanNames,
            'asramas' => collect(),
            'kelases' => collect(),
            'stats' => [
                'total' => $perizinans->count(),
                'sedang_berlangsung' => $perizinans->where('status', 'sedang_berlangsung')->count(),
                'disetujui' => $perizinans->where('status', 'disetujui')->count(),
                'selesai' => $perizinans->where('status', 'selesai')->count(),
                'terlambat_kembali' => $perizinans->where('status', 'terlambat_kembali')->count(),
                'pending' => $perizinans->where('status', 'pending')->count(),
                'ditolak' => $perizinans->where('status', 'ditolak')->count(),
                'pulang' => $perizinans->where('jenis_izin', 'pulang')->count(),
                'keluar_komplek' => $perizinans->where('jenis_izin', 'keluar_komplek')->count(),
                'sakit' => $perizinans->where('jenis_izin', 'sakit')->count(),
            ],
        ];
    }
}
