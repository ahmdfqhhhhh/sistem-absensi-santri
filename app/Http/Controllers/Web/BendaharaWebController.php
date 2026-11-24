<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BendaharaWebController extends Controller
{
    /**
     * Dashboard bendahara: ringkasan keuangan pondok.
     */
    public function index(): View
    {
        // Kas masuk bulan ini (termasuk tagihan lunas dan pembayaran cicilan yang tercatat bulan ini)
        $tagihansBayarBulanIni = Tagihan::whereNotNull('tanggal_bayar')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->get();

        $totalKasMasukBulanIni = 0;
        foreach ($tagihansBayarBulanIni as $tagihan) {
            if (!empty($tagihan->riwayat_cicilan)) {
                $hasMonthRecord = false;
                foreach ($tagihan->riwayat_cicilan as $item) {
                    $tgl = isset($item['tanggal']) ? \Carbon\Carbon::parse($item['tanggal']) : null;
                    if ($tgl && $tgl->month === now()->month && $tgl->year === now()->year) {
                        $totalKasMasukBulanIni += (int) ($item['nominal'] ?? 0);
                        $hasMonthRecord = true;
                    }
                }
                if (!$hasMonthRecord && $tagihan->nominal_dibayar > 0) {
                    $totalKasMasukBulanIni += (int) $tagihan->nominal_dibayar;
                }
            } else {
                $totalKasMasukBulanIni += (int) $tagihan->nominal_dibayar;
            }
        }

        // Jumlah anak (santri unik) yang masih menunggak tagihan (status belum_bayar dengan sisa tagihan > 0)
        $totalTagihanAktif = Tagihan::where('status', 'belum_bayar')
            ->whereRaw('nominal > nominal_dibayar')
            ->distinct('santri_id')
            ->count('santri_id');

        // Total nominal tunggakan yang belum dilunasi dari seluruh tagihan yang berstatus belum_bayar
        $totalTunggakan = Tagihan::where('status', 'belum_bayar')
            ->sum(DB::raw('nominal - nominal_dibayar'));

        // Antrean konfirmasi transfer dari wali santri
        $antreanKonfirmasi = Tagihan::menungguKonfirmasi()->count();
        $jumlahAntrean = $antreanKonfirmasi;
        $kasmasuk = $totalKasMasukBulanIni;

        $recentVerifikasi = Tagihan::menungguKonfirmasi()
            ->with(['santri.kelas', 'santri.asrama'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('bendahara.dashboard', compact(
            'totalKasMasukBulanIni',
            'kasmasuk',
            'totalTagihanAktif',
            'totalTunggakan',
            'antreanKonfirmasi',
            'jumlahAntrean',
            'recentVerifikasi'
        ));
    }

    /**
     * Rekap keuangan semua santri: total tagihan, dibayar, sisa tagihan & nunggak.
     */
    public function rekap(Request $request): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $asramaList = Asrama::orderBy('nama_asrama')->get();

        $query = Santri::with(['tagihans', 'kelas', 'asrama'])
            ->withCount('tagihans')
            ->withSum('tagihans', 'nominal')
            ->withSum(['tagihans as nominal_dibayar_sum' => function ($q) {
                $q->where('status', 'lunas');
            }], 'nominal_dibayar');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('asrama_id')) {
            $query->where('asrama_id', $request->asrama_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'nunggak') {
                $query->whereHas('tagihans', function ($q) {
                    $q->where('status', 'belum_bayar')->where('jatuh_tempo', '<', today());
                });
            } elseif ($request->status === 'belum_bayar') {
                $query->whereHas('tagihans', function ($q) {
                    $q->where('status', 'belum_bayar');
                });
            } elseif ($request->status === 'lunas') {
                $query->whereDoesntHave('tagihans', function ($q) {
                    $q->where('status', '!=', 'lunas');
                })->whereHas('tagihans');
            }
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nis', 'like', "%{$cari}%");
            });
        }

        $santris = $query->orderBy('nama')->paginate(25)->withQueryString();

        // Hitung rincian status per santri secara akurat
        $santris->getCollection()->transform(function (Santri $santri) {
            $santri->total_tagihan = (int) ($santri->tagihans_sum_nominal ?? 0);
            $santri->total_dibayar = (int) ($santri->nominal_dibayar_sum ?? 0);
            $santri->sisa_tagihan = max(0, $santri->total_tagihan - $santri->total_dibayar);
            $santri->sisa_nunggak = $santri->totalTunggakan(); // Tagihan yang melewati jatuh tempo

            // Tentukan status keuangan santri secara presisi
            if ($santri->total_tagihan === 0) {
                $santri->status_keuangan = 'tidak_ada';
                $santri->status_label = 'Bebas Tagihan';
            } elseif ($santri->sisa_tagihan === 0) {
                $santri->status_keuangan = 'lunas';
                $santri->status_label = 'Lunas';
            } elseif ($santri->sisa_nunggak > 0) {
                $santri->status_keuangan = 'nunggak';
                $santri->status_label = 'Nunggak';
            } else {
                $santri->status_keuangan = 'belum_bayar';
                $santri->status_label = 'Belum Bayar';
            }

            return $santri;
        });

        $totalSantriNunggak = Santri::whereHas('tagihans', function ($q) {
            $q->where('status', 'belum_bayar')->where('jatuh_tempo', '<', today());
        })->count();

        $grandTotalTunggakan = Tagihan::nunggak()->sum('nominal');

        $totalBelumLunas = Santri::whereHas('tagihans', function ($q) {
            $q->where('status', 'belum_bayar');
        })->count();

        return view('bendahara.rekap', [
            'santris'             => $santris,
            'kelasList'           => $kelasList,
            'kelas'               => $kelasList,
            'asramaList'          => $asramaList,
            'asramas'             => $asramaList,
            'totalSantriNunggak'  => $totalSantriNunggak,
            'grandTotalTunggakan' => $grandTotalTunggakan,
            'totalBelumLunas'     => $totalBelumLunas,
        ]);
    }

    /**
     * Detail history tagihan satu santri.
     */
    public function detailSantri(Santri $santri): View
    {
        $santri->load(['kelas', 'asrama']);

        $tagihans = Tagihan::where('santri_id', $santri->id)
            ->with('dikonfirmasiOleh')
            ->latest('jatuh_tempo')
            ->paginate(20);

        return view('bendahara.santri_detail', compact('santri', 'tagihans'));
    }

    /**
     * Daftar semua tagihan dengan filter.
     */
    public function tagihanIndex(Request $request): View
    {
        $query = Tagihan::with(['santri.kelas', 'santri.asrama', 'dikonfirmasiOleh'])
            ->latest('jatuh_tempo');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('santri', fn ($q) => $q->where('kelas_id', $request->kelas_id));
        }

        if ($request->filled('asrama_id')) {
            $query->whereHas('santri', fn ($q) => $q->where('asrama_id', $request->asrama_id));
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('judul_tagihan', 'like', "%{$cari}%")
                    ->orWhere('kode_tagihan', 'like', "%{$cari}%")
                    ->orWhereHas('santri', fn ($s) => $s->where('nama', 'like', "%{$cari}%"));
            });
        }

        $tagihans = $query->paginate(25)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $asramaList = Asrama::orderBy('nama_asrama')->get();
        $santriList = Santri::where('status', 'aktif')->with('kelas')->orderBy('nama')->get();

        return view('bendahara.tagihan_index', [
            'tagihans'    => $tagihans,
            'kelasList'   => $kelasList,
            'kelas'       => $kelasList,
            'asramaList'  => $asramaList,
            'asramas'     => $asramaList,
            'santris'     => $santriList,
        ]);
    }

    /**
     * Simpan tagihan baru: single santri, semua santri, per kelas, atau per asrama.
     */
    public function tagihanStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul_tagihan'    => ['required', 'string', 'max:255'],
            'kategori'         => ['required', 'string', 'max:100'],
            'nominal'          => ['required', 'integer', 'min:1000'],
            'jatuh_tempo'      => ['required', 'date', 'after_or_equal:today'],
            'target'           => ['required', 'in:single,santri,semua,kelas,asrama'],
            'santri_id'        => ['required_if:target,single,santri', 'nullable', 'exists:santris,id'],
            'kelas_id'         => ['required_if:target,kelas', 'nullable', 'exists:kelas,id'],
            'asrama_id'        => ['required_if:target,asrama', 'nullable', 'exists:asramas,id'],
        ]);

        $santriIds = match ($validated['target']) {
            'single', 'santri' => [$validated['santri_id']],
            'semua'            => Santri::where('status', 'aktif')->pluck('id')->toArray(),
            'kelas'            => Santri::where('kelas_id', $validated['kelas_id'])->where('status', 'aktif')->pluck('id')->toArray(),
            'asrama'           => Santri::where('asrama_id', $validated['asrama_id'])->where('status', 'aktif')->pluck('id')->toArray(),
        };

        if (empty($santriIds)) {
            return back()->with('error', 'Tidak ada santri yang sesuai dengan target yang dipilih.');
        }

        DB::transaction(function () use ($validated, $santriIds) {
            $now = now();
            foreach ($santriIds as $santriId) {
                // Cek apakah santri sudah memiliki tagihan aktif yang belum lunas
                $existing = Tagihan::where('santri_id', $santriId)
                    ->whereIn('status', ['belum_bayar', 'menunggu_konfirmasi'])
                    ->first();

                if ($existing) {
                    // Akumulasikan ke tagihan yang sudah ada (tidak membuat baris baru per anak)
                    $existing->nominal += $validated['nominal'];
                    if (! str_contains($existing->judul_tagihan, $validated['judul_tagihan'])) {
                        $existing->judul_tagihan .= ' + ' . $validated['judul_tagihan'];
                    }
                    if ($validated['jatuh_tempo'] > $existing->jatuh_tempo) {
                        $existing->jatuh_tempo = $validated['jatuh_tempo'];
                    }
                    $existing->save();
                } else {
                    Tagihan::create([
                        'santri_id'        => $santriId,
                        'kode_tagihan'     => Tagihan::generateKodeTagihan(),
                        'judul_tagihan'    => $validated['judul_tagihan'],
                        'kategori'         => $validated['kategori'],
                        'nominal'          => $validated['nominal'],
                        'nominal_dibayar'  => 0,
                        'jatuh_tempo'      => $validated['jatuh_tempo'],
                        'status'           => 'belum_bayar',
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }
            }
        });

        $jumlah = count($santriIds);

        return back()->with('success', "Alhamdulillah! Berhasil memproses {$jumlah} santri. Tagihan yang sudah ada otomatis diakumulasikan.");
    }

    /**
     * Antrean tagihan yang menunggu konfirmasi transfer dari wali.
     */
    public function verifikasiIndex(): View
    {
        $tagihans = Tagihan::menungguKonfirmasi()
            ->with(['santri.kelas', 'santri.asrama'])
            ->latest('updated_at')
            ->paginate(20);

        return view('bendahara.verifikasi', compact('tagihans'));
    }

    /**
     * Konfirmasi atau tolak pembayaran transfer dari wali.
     */
    public function konfirmasi(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $validated = $request->validate([
            'action'             => ['required', 'in:konfirmasi,tolak'],
            'nominal_konfirmasi' => ['nullable', 'integer', 'min:1000'],
            'catatan_bendahara'  => ['nullable', 'string', 'max:500'],
            'alasan'             => ['required_if:action,tolak', 'nullable', 'string', 'max:500'],
        ]);

        if ($tagihan->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Tagihan ini tidak sedang menunggu konfirmasi.');
        }

        if ($validated['action'] === 'konfirmasi') {
            $nominalBayar = $request->filled('nominal_konfirmasi')
                ? (int) $request->nominal_konfirmasi
                : ($tagihan->nominal_transfer ?: $tagihan->sisa_tagihan);

            $tagihan->catatPembayaran(
                nominal: $nominalBayar,
                metode: $tagihan->metode_pembayaran ?: 'transfer_bsi',
                catatan: $validated['catatan_bendahara'] ?? ($tagihan->catatan_wali ? 'Transfer: ' . $tagihan->catatan_wali : 'Konfirmasi transfer terverifikasi'),
                userId: Auth::id()
            );

            $msg = $tagihan->status === 'lunas'
                ? "Alhamdulillah! Pembayaran tagihan {$tagihan->kode_tagihan} sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " berhasil dikonfirmasi LUNAS."
                : "Alhamdulillah! Cicilan tagihan {$tagihan->kode_tagihan} sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " berhasil dikonfirmasi. Sisa tagihan: Rp " . number_format($tagihan->sisa_tagihan, 0, ',', '.');

            return back()->with('success', $msg);
        }

        // Tolak transfer
        $tagihan->update([
            'status'              => 'belum_bayar',
            'nominal_transfer'    => null,
            'bukti_transfer_path' => null,
            'catatan_bendahara'   => $validated['alasan'] ?? null,
        ]);

        return back()->with('success', "Pembayaran tagihan {$tagihan->kode_tagihan} ditolak. Status dikembalikan ke belum bayar.");
    }

    /**
     * Catat pembayaran tunai santri oleh bendahara (Bisa lunas atau cicil bertahap).
     */
    public function bayarTunai(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $request->validate([
            'nominal_bayar'     => ['nullable', 'integer', 'min:1000', 'max:' . $tagihan->sisa_tagihan],
            'catatan_bendahara' => ['nullable', 'string', 'max:500'],
        ]);

        if ($tagihan->status === 'lunas') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $nominalBayar = $request->filled('nominal_bayar')
            ? (int) $request->nominal_bayar
            : $tagihan->sisa_tagihan;

        $tagihan->catatPembayaran(
            nominal: $nominalBayar,
            metode: 'tunai',
            catatan: $request->catatan_bendahara ?: 'Pembayaran tunai langsung ke bendahara',
            userId: Auth::id()
        );

        $msg = $tagihan->status === 'lunas'
            ? "Alhamdulillah! Pembayaran tunai {$tagihan->kode_tagihan} sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " LUNAS."
            : "Alhamdulillah! Cicilan tunai {$tagihan->kode_tagihan} sebesar Rp " . number_format($nominalBayar, 0, ',', '.') . " berhasil dicatat. Sisa: Rp " . number_format($tagihan->sisa_tagihan, 0, ',', '.');

        return back()->with('success', $msg);
    }

    /**
     * Tampilkan kuitansi tagihan yang sudah lunas.
     */
    public function kuitansi(Tagihan $tagihan): View|RedirectResponse
    {
        if ($tagihan->status !== 'lunas') {
            return back()->with('error', 'Kuitansi hanya tersedia untuk tagihan yang sudah lunas.');
        }

        $tagihan->load(['santri.kelas', 'santri.asrama', 'dikonfirmasiOleh']);

        return view('bendahara.kuitansi', compact('tagihan'));
    }
}
