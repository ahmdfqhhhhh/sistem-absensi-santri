<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\User;
use App\Services\PerizinanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PerizinanWebController extends Controller
{
    public function __construct(
        protected PerizinanService $perizinanService
    ) {}

    /**
     * Menampilkan daftar perizinan santri dan portal pengajuan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = [
            'status' => $request->query('status'),
            'jenis_izin' => $request->query('jenis_izin'),
            'search' => $request->query('search'),
        ];

        // Pembatasan hak akses berbasis gender
        $lockedGender = null;
        if ($user && $user->gender === 'putra') {
            $lockedGender = 'L';
            $filters['gender'] = 'L';
        } elseif ($user && $user->gender === 'putri') {
            $lockedGender = 'P';
            $filters['gender'] = 'P';
        } elseif ($request->filled('gender') && in_array($request->input('gender'), ['L', 'P'])) {
            $filters['gender'] = $request->input('gender');
        }

        // Jika user adalah Wali Santri, batasi data hanya untuk ananda terkait
        if ($user && $user->isWali()) {
            $mySantris = Santri::where('status', 'aktif')
                ->where('no_wa_wali', $user->no_hp)
                ->orderBy('nama')
                ->get();

            $santriIds = $mySantris->pluck('id')->toArray();
            $filters['santri_ids'] = $santriIds;
            $santris = $mySantris;

            $stats = [
                'pending' => Perizinan::whereIn('santri_id', $santriIds)->where('status', 'pending')->count(),
                'sedang_berlangsung' => Perizinan::whereIn('santri_id', $santriIds)->where('status', 'sedang_berlangsung')->count(),
                'disetujui' => Perizinan::whereIn('santri_id', $santriIds)->where('status', 'disetujui')->count(),
                'total' => Perizinan::whereIn('santri_id', $santriIds)->count(),
            ];
        } else {
            $santrisQuery = Santri::where('status', 'aktif');
            if (! empty($filters['gender'])) {
                $santrisQuery->where('jenis_kelamin', $filters['gender']);
            }
            $santris = $santrisQuery->orderBy('nama')->get();

            $basePerizinan = Perizinan::query();
            if (! empty($filters['gender'])) {
                $basePerizinan->whereHas('santri', fn ($q) => $q->where('jenis_kelamin', $filters['gender']));
            }

            $stats = [
                'pending' => (clone $basePerizinan)->where('status', 'pending')->count(),
                'sedang_berlangsung' => (clone $basePerizinan)->where('status', 'sedang_berlangsung')->count(),
                'disetujui' => (clone $basePerizinan)->where('status', 'disetujui')->count(),
                'total' => (clone $basePerizinan)->count(),
            ];
        }

        $perizinans = $this->perizinanService->getList($filters, 12);

        return view('perizinan.index', compact('perizinans', 'santris', 'stats', 'lockedGender'));
    }

    /**
     * Menyimpan permohonan surat izin baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'jenis_izin' => 'required|in:pulang,keluar_komplek,sakit,sambangan_keluarga,tugas_pondok',
            'keperluan' => 'required|string|min:5|max:1000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'penjemput_nama' => 'nullable|string|max:100',
            'penjemput_hubungan' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        if ($user && $user->isWali()) {
            $isOwnChild = $user->no_hp
                ? Santri::where('id', $validated['santri_id'])
                    ->where('no_wa_wali', $user->no_hp)
                    ->exists()
                : false;

            if (! $isOwnChild) {
                return back()->with('error', 'Akses ditolak. Anda hanya diizinkan mengajukan izin untuk santri ananda sendiri.');
            }
        }

        $this->perizinanService->ajukan($validated);

        return redirect()->route('perizinan.index')->with('success', 'Pengajuan izin santri berhasil dikirim. Menunggu persetujuan pengasuhan/musyrif.');
    }

    /**
     * Persetujuan surat perizinan oleh pengurus/musyrif.
     */
    public function approve(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pemberi_izin' => 'nullable|string|max:150',
            'catatan' => 'nullable|string|max:500',
        ]);

        $adminId = $request->user()?->id ?? User::first()?->id ?? 1;
        $namaPemberiIzin = !empty($validated['nama_pemberi_izin'])
            ? $validated['nama_pemberi_izin']
            : ($request->user()?->name ?? 'Pengurus Pondok');

        $this->perizinanService->approve(
            $id,
            $adminId,
            $validated['catatan'] ?? 'Disetujui oleh pengurus pondok.',
            $namaPemberiIzin
        );

        return redirect()->route('perizinan.index')->with('success', 'Surat izin berhasil disetujui oleh '.$namaPemberiIzin.' & Surat Izin Digital ber-QR Code telah diterbitkan!');
    }

    /**
     * Penolakan surat perizinan santri.
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'catatan' => 'required|string|min:5|max:500',
        ]);

        $adminId = $request->user()?->id ?? User::first()?->id ?? 1;

        $this->perizinanService->reject(
            $id,
            $adminId,
            $validated['catatan']
        );

        return redirect()->route('perizinan.index')->with('success', 'Pengajuan izin santri telah ditolak.');
    }

    /**
     * Menampilkan Surat Izin Digital Resmi dengan QR Code Verifikasi Kamtib.
     */
    public function surat(Request $request, int $id): View|RedirectResponse
    {
        $perizinan = Perizinan::with(['santri.asrama', 'santri.kelas', 'approvedBy'])->findOrFail($id);
        $user = $request->user();

        // Jika Wali Santri, batasi secara ketat hanya bisa melihat surat izin santri ananda sendiri
        if ($user && $user->isWali()) {
            $santriIds = $user->no_hp
                ? Santri::where('status', 'aktif')
                    ->where('no_wa_wali', $user->no_hp)
                    ->pluck('id')
                    ->toArray()
                : [];

            if (! in_array($perizinan->santri_id, $santriIds)) {
                return redirect()->route('wali.portal')->with('error', 'Akses ditolak. Anda hanya dapat melihat surat izin ananda sendiri.');
            }
        }

        return view('perizinan.surat', compact('perizinan'));
    }
}
