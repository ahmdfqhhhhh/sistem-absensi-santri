<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliPortalController extends Controller
{
    /**
     * Menampilkan Portal Khusus Wali Santri (PRD 2 & SRS 2.2).
     */
    public function portal(Request $request): View
    {
        $user = Auth::user();

        // Cari santri yang diasuh oleh wali ini (berdasarkan nomor WA wali atau nama wali terdaftar)
        $santri = null;
        if ($user) {
            if ($user->no_hp) {
                $santri = Santri::with(['asrama', 'kelas'])->where('no_wa_wali', $user->no_hp)->first();
            }
            if (! $santri && $user->name) {
                $santri = Santri::with(['asrama', 'kelas'])->where('nama_wali', $user->name)->first();
            }
        }

        $riwayatAbsensi = collect();
        $riwayatIzin = collect();
        $stats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'total' => 0,
        ];

        if ($santri) {
            $riwayatAbsensi = Absensi::with('sesiAbsensi')
                ->where('santri_id', $santri->id)
                ->latest('created_at')
                ->take(15)
                ->get();

            $riwayatIzin = Perizinan::with('approvedBy')
                ->where('santri_id', $santri->id)
                ->latest('created_at')
                ->take(5)
                ->get();

            $counts = Absensi::where('santri_id', $santri->id)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $stats['hadir'] = $counts['hadir'] ?? 0;
            $stats['terlambat'] = $counts['terlambat'] ?? 0;
            $stats['izin'] = $counts['izin'] ?? 0;
            $stats['sakit'] = $counts['sakit'] ?? 0;
            $stats['alpa'] = $counts['alpa'] ?? 0;
            $stats['total'] = array_sum($counts);
        }

        return view('wali.portal', compact('santri', 'riwayatAbsensi', 'riwayatIzin', 'stats'));
    }

    /**
     * Tampilkan daftar tagihan anak wali yang sedang login.
     */
    public function pembayaran(Request $request): View
    {
        $user = Auth::user();

        $santri = $this->getSantriWali($user);

        $tagihans = collect();
        if ($santri) {
            $query = Tagihan::where('santri_id', $santri->id)
                ->with('dikonfirmasiOleh')
                ->latest('jatuh_tempo');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $tagihans = $query->paginate(15)->withQueryString();
        }

        return view('wali.pembayaran', compact('santri', 'tagihans'));
    }

    /**
     * Upload bukti transfer untuk tagihan santri milik wali.
     */
    public function prosesBayar(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $user = Auth::user();

        // Pastikan tagihan ini milik santri wali yang login
        $santri = $tagihan->santri;
        $isOwner = ($santri->no_wa_wali && $santri->no_wa_wali === $user->no_hp)
            || ($santri->nama_wali && $santri->nama_wali === $user->name);

        if (! $isOwner) {
            abort(403, 'Anda tidak berhak mengakses tagihan ini.');
        }

        if ($tagihan->status !== 'belum_bayar') {
            return back()->with('error', 'Tagihan ini tidak dapat diproses (status: ' . $tagihan->status_label . ').');
        }

        $validated = $request->validate([
            'bukti_transfer'    => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'],
            'metode_pembayaran' => ['required', 'string', 'max:50'],
            'nominal_bayar'     => ['nullable', 'integer', 'min:1000', 'max:' . $tagihan->sisa_tagihan],
            'catatan_wali'      => ['nullable', 'string', 'max:500'],
        ]);

        $nominalBayar = $validated['nominal_bayar'] ?? $tagihan->sisa_tagihan;
        $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');

        $tagihan->update([
            'status'              => 'menunggu_konfirmasi',
            'bukti_transfer_path' => $path,
            'metode_pembayaran'   => $validated['metode_pembayaran'],
            'nominal_transfer'    => $nominalBayar,
            'catatan_wali'        => $validated['catatan_wali'] ?? null,
        ]);

        $infoCicil = ($nominalBayar < $tagihan->sisa_tagihan)
            ? ' (Pengajuan cicilan Rp ' . number_format($nominalBayar, 0, ',', '.') . ')'
            : '';

        return redirect()->route('wali.pembayaran')
            ->with('success', 'Bukti transfer berhasil dikirim' . $infoCicil . '. Menunggu konfirmasi bendahara.');
    }

    /**
     * Tampilkan kuitansi tagihan yang sudah lunas milik santri wali yang login.
     */
    public function kuitansi(Tagihan $tagihan): View|RedirectResponse
    {
        $user = Auth::user();

        $santri = $tagihan->santri;
        $isOwner = ($santri->no_wa_wali && $santri->no_wa_wali === $user->no_hp)
            || ($santri->nama_wali && $santri->nama_wali === $user->name);

        if (! $isOwner) {
            abort(403, 'Anda tidak berhak mengakses tagihan ini.');
        }

        if ($tagihan->status !== 'lunas') {
            return back()->with('error', 'Kuitansi hanya tersedia untuk tagihan yang sudah lunas.');
        }

        $tagihan->load(['santri.kelas', 'santri.asrama', 'dikonfirmasiOleh']);

        return view('bendahara.kuitansi', compact('tagihan'));
    }

    /**
     * Helper: cari santri berdasarkan user wali yang sedang login.
     */
    private function getSantriWali(?\App\Models\User $user): ?Santri
    {
        if (! $user) {
            return null;
        }

        if ($user->no_hp) {
            $santri = Santri::where('no_wa_wali', $user->no_hp)->first();
            if ($santri) {
                return $santri;
            }
        }

        if ($user->name) {
            return Santri::where('nama_wali', $user->name)->first();
        }

        return null;
    }
}
