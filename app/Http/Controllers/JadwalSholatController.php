<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JadwalSholatTemplate;
use App\Models\SesiAbsensi;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JadwalSholatController extends Controller
{
    /**
     * Halaman pengaturan jadwal sesi sholat & diniyah.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $templates = JadwalSholatTemplate::orderBy('urutan')->get();
        if ($templates->isEmpty()) {
            $this->seedDefaults();
            $templates = JadwalSholatTemplate::orderBy('urutan')->get();
        }

        $sesiHariIni = SesiAbsensi::ensureDailySessions();

        return view('jadwal.index', compact('sesiHariIni', 'templates'));
    }

    /**
     * Memperbarui pengaturan jam sesi sholat harian.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apply_to_today' => 'nullable',
            'jadwal' => 'required|array',
            'jadwal.*.id' => 'required|exists:jadwal_sholat_templates,id',
            'jadwal.*.nama_sesi' => 'required|string|max:100',
            'jadwal.*.jam_mulai' => 'required|string',
            'jadwal.*.jam_selesai' => 'required|string',
            'jadwal.*.batas_terlambat' => 'nullable|string',
            'jadwal.*.aktif' => 'nullable',
        ]);

        $applyToToday = !empty($validated['apply_to_today']);
        $todayCode = str_replace('-', '', today()->toDateString());

        foreach ($validated['jadwal'] as $item) {
            $template = JadwalSholatTemplate::find($item['id']);
            if (!$template) continue;

            $jamMulai = strlen($item['jam_mulai']) === 5 ? $item['jam_mulai'] . ':00' : $item['jam_mulai'];
            $jamSelesai = strlen($item['jam_selesai']) === 5 ? $item['jam_selesai'] . ':00' : $item['jam_selesai'];
            $batasTerlambat = !empty($item['batas_terlambat'])
                ? (strlen($item['batas_terlambat']) === 5 ? $item['batas_terlambat'] . ':00' : $item['batas_terlambat'])
                : null;

            $template->update([
                'nama_sesi' => $item['nama_sesi'],
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'batas_terlambat' => $batasTerlambat,
                'aktif' => !empty($item['aktif']),
            ]);

            if ($applyToToday) {
                $prefix = match($template->kode) {
                    'SUBUH' => 'SBH',
                    'KBM' => 'SCH',
                    'DZUHUR' => 'DZH',
                    'ASHAR' => 'ASH',
                    'MAGHRIB' => 'MGH',
                    'ISYA' => 'ISY',
                    default => $template->kode,
                };

                SesiAbsensi::where('tanggal', today()->toDateString())
                    ->where('kode_sesi', $prefix . '-' . $todayCode)
                    ->update([
                        'nama_sesi' => $item['nama_sesi'],
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'batas_terlambat' => $batasTerlambat,
                    ]);
            }
        }

        return redirect()->route('jadwal.index')->with('success', 'Jadwal waktu sesi berhasil diperbarui.');
    }

    /**
     * Reset jadwal ke standar default pesantren.
     */
    public function reset(): RedirectResponse
    {
        $this->seedDefaults();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal sesi berhasil di-reset ke nilai default.');
    }

    /**
     * Seeder jadwal standar pesantren Miftahul Huda Kroya.
     */
    public function seedDefaults(): void
    {
        $defaults = [
            [
                'kode' => 'SUBUH',
                'nama_sesi' => 'Sholat Subuh Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '04:15:00',
                'jam_selesai' => '05:30:00',
                'batas_terlambat' => '04:45:00',
                'urutan' => 1,
                'aktif' => true,
            ],
            [
                'kode' => 'KBM',
                'nama_sesi' => 'Presensi Masuk Sekolah / KBM Madrasah',
                'jenis' => 'kegiatan_belajar',
                'jam_mulai' => '06:30:00',
                'jam_selesai' => '07:30:00',
                'batas_terlambat' => '07:00:00',
                'urutan' => 2,
                'aktif' => true,
            ],
            [
                'kode' => 'DZUHUR',
                'nama_sesi' => 'Sholat Dzuhur Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '11:45:00',
                'jam_selesai' => '13:00:00',
                'batas_terlambat' => '12:15:00',
                'urutan' => 3,
                'aktif' => true,
            ],
            [
                'kode' => 'ASHAR',
                'nama_sesi' => 'Sholat Ashar Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '15:00:00',
                'jam_selesai' => '16:15:00',
                'batas_terlambat' => '15:30:00',
                'urutan' => 4,
                'aktif' => true,
            ],
            [
                'kode' => 'MAGHRIB',
                'nama_sesi' => 'Sholat Maghrib Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '17:45:00',
                'jam_selesai' => '19:00:00',
                'batas_terlambat' => '18:15:00',
                'urutan' => 5,
                'aktif' => true,
            ],
            [
                'kode' => 'ISYA',
                'nama_sesi' => 'Sholat Isya Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '19:15:00',
                'jam_selesai' => '20:30:00',
                'batas_terlambat' => '19:45:00',
                'urutan' => 6,
                'aktif' => true,
            ],
        ];

        foreach ($defaults as $d) {
            JadwalSholatTemplate::updateOrCreate(['kode' => $d['kode']], $d);
        }
    }
}
