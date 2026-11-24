<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Santri;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    /**
     * Menyimpan data Kamar baru pada Asrama tertentu.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asrama_id' => 'required|exists:asramas,id',
            'nama_kamar' => 'required|string|max:100',
            'kapasitas' => 'nullable|integer|min:1|max:100',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'asrama_id.required' => 'Asrama wajib dipilih.',
            'asrama_id.exists' => 'Asrama yang dipilih tidak valid.',
            'nama_kamar.required' => 'Nama atau nomor kamar wajib diisi.',
            'kapasitas.min' => 'Kapasitas minimal 1 orang santri.',
        ]);

        $exists = Kamar::where('asrama_id', $validated['asrama_id'])
            ->where('nama_kamar', $validated['nama_kamar'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Kamar '{$validated['nama_kamar']}' sudah terdaftar pada asrama tersebut.");
        }

        Kamar::create([
            'asrama_id' => $validated['asrama_id'],
            'nama_kamar' => $validated['nama_kamar'],
            'kapasitas' => $validated['kapasitas'] ?? 10,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->back()->with('success', "Kamar '{$validated['nama_kamar']}' berhasil ditambahkan!");
    }

    /**
     * Memperbarui informasi Kamar.
     */
    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $validated = $request->validate([
            'asrama_id' => 'required|exists:asramas,id',
            'nama_kamar' => 'required|string|max:100',
            'kapasitas' => 'nullable|integer|min:1|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $namaLama = $kamar->nama_kamar;
        $asramaLamaId = $kamar->asrama_id;

        $kamar->update([
            'asrama_id' => $validated['asrama_id'],
            'nama_kamar' => $validated['nama_kamar'],
            'kapasitas' => $validated['kapasitas'] ?? 10,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        // Jika nama kamar berubah, sinkronkan data santri yang menempati kamar lama
        if ($namaLama !== $validated['nama_kamar'] || $asramaLamaId !== (int) $validated['asrama_id']) {
            Santri::where('asrama_id', $asramaLamaId)
                ->where('kamar', $namaLama)
                ->update([
                    'asrama_id' => $validated['asrama_id'],
                    'kamar' => $validated['nama_kamar'],
                ]);
        }

        return redirect()->back()->with('success', "Data kamar '{$validated['nama_kamar']}' berhasil diperbarui!");
    }

    /**
     * Menghapus Kamar jika tidak dihuni oleh santri.
     */
    public function destroy(Kamar $kamar): RedirectResponse
    {
        $santriCount = Santri::where('asrama_id', $kamar->asrama_id)
            ->where('kamar', $kamar->nama_kamar)
            ->count();

        if ($santriCount > 0) {
            return redirect()->back()->with('error', "Kamar '{$kamar->nama_kamar}' tidak dapat dihapus karena masih dihuni oleh {$santriCount} santri.");
        }

        $nama = $kamar->nama_kamar;
        $kamar->delete();

        return redirect()->back()->with('success', "Kamar '{$nama}' berhasil dihapus.");
    }
}
