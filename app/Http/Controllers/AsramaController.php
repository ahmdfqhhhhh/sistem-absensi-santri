<?php

namespace App\Http\Controllers;

use App\Models\Asrama;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AsramaController extends Controller
{
    /**
     * Menyimpan data Asrama baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_asrama' => 'required|string|max:100|unique:asramas,nama_asrama',
            'gedung' => 'nullable|string|max:100',
            'gender' => 'required|in:putra,putri',
            'pembina' => 'nullable|string|max:100',
            'kapasitas' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nama_asrama.required' => 'Nama asrama wajib diisi.',
            'nama_asrama.unique' => 'Nama asrama sudah ada.',
            'gender.required' => 'Gender peruntukan wajib dipilih (Putra / Putri).',
        ]);

        Asrama::create($validated);

        return redirect()->back()->with('success', "Asrama '{$validated['nama_asrama']}' berhasil ditambahkan!");
    }

    /**
     * Memperbarui data Asrama.
     */
    public function update(Request $request, Asrama $asrama): RedirectResponse
    {
        $validated = $request->validate([
            'nama_asrama' => 'required|string|max:100|unique:asramas,nama_asrama,'.$asrama->id,
            'gedung' => 'nullable|string|max:100',
            'gender' => 'required|in:putra,putri',
            'pembina' => 'nullable|string|max:100',
            'kapasitas' => 'nullable|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $asrama->update($validated);

        return redirect()->back()->with('success', "Data asrama '{$asrama->nama_asrama}' berhasil diperbarui!");
    }

    /**
     * Menghapus data Asrama jika belum memiliki santri dan kamar.
     */
    public function destroy(Asrama $asrama): RedirectResponse
    {
        if ($asrama->santris()->exists()) {
            return redirect()->back()->with('error', "Asrama '{$asrama->nama_asrama}' tidak dapat dihapus karena masih dihuni oleh santri.");
        }

        if ($asrama->kamars()->exists()) {
            return redirect()->back()->with('error', "Asrama '{$asrama->nama_asrama}' tidak dapat dihapus karena masih memiliki kamar terdaftar. Silakan hapus data kamar terlebih dahulu.");
        }

        $nama = $asrama->nama_asrama;
        $asrama->delete();

        return redirect()->back()->with('success', "Asrama '{$nama}' berhasil dihapus.");
    }
}
