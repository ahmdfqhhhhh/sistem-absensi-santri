<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class KeuanganSuperAdminController extends Controller
{
    /**
     * Daftar santri yang memiliki tunggakan / tagihan belum lunas.
     * Terhubung dan selaras dengan data rekapan di Bendahara.
     */
    public function tunggakan(Request $request): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $asramaList = Asrama::orderBy('nama_asrama')->get();

        $query = Santri::with(['kelas', 'asrama', 'tagihans' => function ($q) {
            $q->where('status', 'belum_bayar')->orderBy('jatuh_tempo', 'asc');
        }])
        ->whereHas('tagihans', function ($q) {
            $q->where('status', 'belum_bayar');
        })
        ->withCount(['tagihans as jumlah_nunggak' => function ($q) {
            $q->where('status', 'belum_bayar');
        }])
        ->withSum(['tagihans as total_nunggak' => function ($q) {
            $q->where('status', 'belum_bayar');
        }], 'nominal');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('asrama_id')) {
            $query->where('asrama_id', $request->asrama_id);
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        // Filter status tempo: lewat (kritis) vs belum_lewat vs semua
        if ($request->filled('status_tempo')) {
            if ($request->status_tempo === 'lewat') {
                $query->whereHas('tagihans', function ($q) {
                    $q->where('status', 'belum_bayar')->where('jatuh_tempo', '<', today());
                });
            } elseif ($request->status_tempo === 'belum_lewat') {
                $query->whereDoesntHave('tagihans', function ($q) {
                    $q->where('status', 'belum_bayar')->where('jatuh_tempo', '<', today());
                });
            }
        }

        $sortBy = $request->get('sort', 'total_nunggak');
        $sortDir = $request->get('dir', 'desc');

        $allowedSorts = ['total_nunggak', 'jumlah_nunggak', 'nama'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'total_nunggak';
        }

        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        $santris = $query->orderBy($sortBy, $sortDir)->paginate(25)->withQueryString();

        // Rincian status jatuh tempo per santri
        $santris->getCollection()->transform(function (Santri $santri) {
            $santri->lewat_tempo_count = $santri->tagihans->where('jatuh_tempo', '<', today())->count();
            $santri->total_lewat_tempo = $santri->tagihans->where('jatuh_tempo', '<', today())->sum('nominal');
            $santri->is_kritis = $santri->lewat_tempo_count > 0;

            return $santri;
        });

        // Metrik ringkasan yang selaras dengan data di Bendahara
        $grandTotalNunggak = Tagihan::where('status', 'belum_bayar')->sum('nominal');
        $grandTotalLewatTempo = Tagihan::where('status', 'belum_bayar')->where('jatuh_tempo', '<', today())->sum('nominal');
        $totalSantriLewatTempo = Santri::whereHas('tagihans', function ($q) {
            $q->where('status', 'belum_bayar')->where('jatuh_tempo', '<', today());
        })->count();
        $totalSantriBelumLunas = Santri::whereHas('tagihans', function ($q) {
            $q->where('status', 'belum_bayar');
        })->count();

        return view('keuangan.tunggakan', compact(
            'santris',
            'kelasList',
            'asramaList',
            'grandTotalNunggak',
            'grandTotalLewatTempo',
            'totalSantriLewatTempo',
            'totalSantriBelumLunas'
        ));
    }
}
