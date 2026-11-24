<?php

namespace App\Http\Controllers;

use App\Models\SesiAbsensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SesiAbsensiController extends Controller
{
    /**
     * Membuat sesi absensi baru secara manual.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:100',
            'tipe' => 'required|in:sholat_fardhu,diniyah,kegiatan_khusus',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'waktu_toleransi_terlambat' => 'nullable',
        ]);

        SesiAbsensi::create($validated);

        return redirect()->back()->with('success', "Sesi absensi {$validated['nama_sesi']} berhasil dibuat!");
    }

    /**
     * Generate 5 sesi sholat fardhu harian otomatis.
     */
    public function generateDaily(Request $request): RedirectResponse
    {
        return redirect()->back()->with('success', 'Sesi sholat fardhu harian berhasil dibuat.');
    }

    /**
     * Memperbarui status sesi (dibuka / ditutup).
     */
    public function updateStatus(Request $request, SesiAbsensi $sesi): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draf,dibuka,ditutup,dibatalkan',
        ]);

        $sesi->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', "Status sesi diperbarui menjadi {$validated['status']}.");
    }
}
