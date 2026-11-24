<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Menyimpan data Kelas Diniyah/Formal baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => 'required|string|max:50',
            'jurusan' => 'nullable|string|max:100',
            'wali_kelas' => 'nullable|string|max:100',
        ]);

        Kelas::create($validated);

        return redirect()->back()->with('success', "Kelas {$validated['nama_kelas']} berhasil ditambahkan!");
    }

    /**
     * Memperbarui data Kelas Sekolah.
     */
    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tingkat' => 'required|string|max:50',
            'jurusan' => 'nullable|string|max:100',
            'wali_kelas' => 'nullable|string|max:100',
        ]);

        $kelas->update($validated);

        return redirect()->back()->with('success', "Data kelas '{$kelas->nama_kelas}' berhasil diperbarui!");
    }

    /**
     * Menghapus data Kelas jika belum memiliki santri terdaftar.
     */
    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->santris()->exists()) {
            return redirect()->back()->with('error', "Kelas '{$kelas->nama_kelas}' tidak dapat dihapus karena masih memiliki santri terdaftar.");
        }

        $nama = $kelas->nama_kelas;
        $kelas->delete();

        return redirect()->back()->with('success', "Kelas '{$nama}' berhasil dihapus.");
    }
}
