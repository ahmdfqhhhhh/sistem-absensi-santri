<?php

namespace App\Http\Controllers;

use App\Models\Asrama;
use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SantriController extends Controller
{
    /**
     * Tampilkan Daftar Santri dengan pemisahan putra/putri, filter pencarian, asrama, kamar, dan kelas.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Santri::with(['asrama', 'kelas']);

        // Pembatasan hak akses berbasis gender user (Musyrif Putra vs Musyrif Putri)
        $lockedGender = null;
        if ($user && $user->gender === 'putra') {
            $lockedGender = 'L';
            $query->where('jenis_kelamin', 'L');
        } elseif ($user && $user->gender === 'putri') {
            $lockedGender = 'P';
            $query->where('jenis_kelamin', 'P');
        } elseif ($request->filled('gender') && in_array($request->input('gender'), ['L', 'P'])) {
            $query->where('jenis_kelamin', $request->input('gender'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('kamar', 'like', "%{$search}%");
            });
        }

        if ($request->filled('asrama_id')) {
            $query->where('asrama_id', $request->integer('asrama_id'));
        }

        if ($request->filled('kamar')) {
            $query->where('kamar', $request->input('kamar'));
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->integer('kelas_id'));
        }

        $santris = $query->latest('id')->paginate(15)->withQueryString();

        // Data Asrama & Kamar dipisahkan berdasarkan gender (Putra vs Putri)
        $asramasQuery = Asrama::with(['kamars'])->withCount(['santris', 'kamars'])->orderBy('nama_asrama');
        $kamarsQuery = Kamar::with('asrama')->orderBy('nama_kamar');

        if ($lockedGender === 'L') {
            $asramasQuery->where('gender', 'putra');
            $kamarsQuery->whereHas('asrama', fn ($q) => $q->where('gender', 'putra'));
        } elseif ($lockedGender === 'P') {
            $asramasQuery->where('gender', 'putri');
            $kamarsQuery->whereHas('asrama', fn ($q) => $q->where('gender', 'putri'));
        }

        $asramas = $asramasQuery->get();
        $asramasPutra = Asrama::where('gender', 'putra')->with(['kamars'])->withCount(['santris', 'kamars'])->orderBy('nama_asrama')->get();
        $asramasPutri = Asrama::where('gender', 'putri')->with(['kamars'])->withCount(['santris', 'kamars'])->orderBy('nama_asrama')->get();

        $kamars = $kamarsQuery->get();
        $kamarsPutra = Kamar::whereHas('asrama', fn ($q) => $q->where('gender', 'putra'))->with('asrama')->orderBy('nama_kamar')->get();
        $kamarsPutri = Kamar::whereHas('asrama', fn ($q) => $q->where('gender', 'putri'))->with('asrama')->orderBy('nama_kamar')->get();

        // Hitung santri per kamar untuk keperluan visualisasi kapasitas dan kelola kamar
        $kamarCounts = Santri::select('asrama_id', 'kamar', DB::raw('count(*) as aggregate'))
            ->whereNotNull('kamar')
            ->groupBy('asrama_id', 'kamar')
            ->get()
            ->keyBy(fn ($item) => $item->asrama_id . '_' . $item->kamar);

        $attachSantriCount = function ($kamarCollection) use ($kamarCounts) {
            foreach ($kamarCollection as $k) {
                $key = $k->asrama_id . '_' . $k->nama_kamar;
                $k->santris_count = $kamarCounts->has($key) ? $kamarCounts[$key]->aggregate : 0;
            }
        };

        $attachSantriCount($kamars);
        $attachSantriCount($kamarsPutra);
        $attachSantriCount($kamarsPutri);

        Kelas::ensureStandardClassesExist();
        $kelases = Kelas::withCount('santris')->orderBy('nama_kelas')->get();

        $stats = [
            'total_semua' => Santri::count(),
            'total_putra' => Santri::where('jenis_kelamin', 'L')->count(),
            'total_putri' => Santri::where('jenis_kelamin', 'P')->count(),
            'kamar_putra' => $kamarsPutra->count(),
            'kamar_putri' => $kamarsPutri->count(),
        ];

        return view('santri.index', compact(
            'santris',
            'asramas',
            'asramasPutra',
            'asramasPutri',
            'kamars',
            'kamarsPutra',
            'kamarsPutri',
            'kelases',
            'lockedGender',
            'stats'
        ));
    }

    /**
     * Cetak & Download Kartu Santri Ber-QR Massal (A4 Ready-to-Print / PDF).
     */
    public function cetakKartuMassal(Request $request): View
    {
        $user = $request->user();
        $query = Santri::with(['asrama', 'kelas']);

        // Pembatasan hak akses berbasis gender user
        $lockedGender = null;
        if ($user && $user->gender === 'putra') {
            $lockedGender = 'L';
            $query->where('jenis_kelamin', 'L');
        } elseif ($user && $user->gender === 'putri') {
            $lockedGender = 'P';
            $query->where('jenis_kelamin', 'P');
        } elseif ($request->filled('gender') && in_array($request->input('gender'), ['L', 'P'])) {
            $query->where('jenis_kelamin', $request->input('gender'));
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->integer('kelas_id'));
        }

        if ($request->filled('asrama_id')) {
            $query->where('asrama_id', $request->integer('asrama_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('kamar', 'like', "%{$search}%");
            });
        }

        $santris = $query->orderBy('nama')->get();

        $kelases = Kelas::orderBy('nama_kelas')->get();
        $asramasQuery = Asrama::orderBy('nama_asrama');
        if ($lockedGender === 'L') {
            $asramasQuery->where('gender', 'putra');
        } elseif ($lockedGender === 'P') {
            $asramasQuery->where('gender', 'putri');
        }
        $asramas = $asramasQuery->get();

        return view('santri.cetak_kartu', compact('santris', 'kelases', 'asramas', 'lockedGender'));
    }

    /**
     * Download Paket ZIP Berisi File QR Code Santri Terfilter.
     * Diberi nama format: "[NIS] - [Nama Santri].svg" untuk memudahkan proses desain mandiri.
     */
    public function downloadQrZip(Request $request): BinaryFileResponse|RedirectResponse
    {
        $user = $request->user();
        $query = Santri::with(['asrama', 'kelas']);

        // Pembatasan hak akses berbasis gender user
        $lockedGender = null;
        if ($user && $user->gender === 'putra') {
            $lockedGender = 'L';
            $query->where('jenis_kelamin', 'L');
        } elseif ($user && $user->gender === 'putri') {
            $lockedGender = 'P';
            $query->where('jenis_kelamin', 'P');
        } elseif ($request->filled('gender') && in_array($request->input('gender'), ['L', 'P'])) {
            $query->where('jenis_kelamin', $request->input('gender'));
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->integer('kelas_id'));
        }

        if ($request->filled('asrama_id')) {
            $query->where('asrama_id', $request->integer('asrama_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('kamar', 'like', "%{$search}%");
            });
        }

        $santris = $query->orderBy('nama')->get();

        if ($santris->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data santri yang cocok untuk diunduh QR Code-nya.');
        }

        $tempDir = sys_get_temp_dir();
        $timestamp = date('Ymd_His');
        $zipFilename = "QR_Code_Santri_{$timestamp}.zip";
        $zipPath = rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $zipFilename;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Gagal membuat file arsip ZIP.');
        }

        $readmeContent = "DAFTAR FILE QR CODE SANTRI RESMI\n";
        $readmeContent .= "PONDOK PESANTREN MIFTAHUL HUDA KROYA\n";
        $readmeContent .= "Tanggal Ekspor: " . now()->translatedFormat('l, d F Y H:i:s') . "\n";
        $readmeContent .= "Total Santri: " . $santris->count() . " santri\n";
        $readmeContent .= "Format File: SVG Vector (Bisa diskalakan tanpa pecah di CorelDraw, Illustrator, Photoshop, Canva, Figma)\n";
        $readmeContent .= "Format Penamaan File: [NIS] - [Nama Santri].svg\n";
        $readmeContent .= "----------------------------------------------------------------------------------------------------\n";
        $readmeContent .= sprintf("%-4s | %-16s | %-32s | %-20s | %-16s\n", "NO", "NIS", "NAMA SANTRI", "ASRAMA", "KELAS");
        $readmeContent .= "----------------------------------------------------------------------------------------------------\n";

        foreach ($santris as $idx => $s) {
            $safeNama = trim(preg_replace('/[\\/\\\\:*?"<>|]/', '_', $s->nama));
            $safeNis = trim(preg_replace('/[\\/\\\\:*?"<>|]/', '_', $s->nis));
            $filename = "{$safeNis} - {$safeNama}.svg";

            // Generate vector SVG QR code (lossless, high resolution)
            $svgContent = QrCode::format('svg')
                ->size(400)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($s->nis);

            $zip->addFromString($filename, $svgContent);

            $readmeContent .= sprintf("%-4d | %-16s | %-32s | %-20s | %-16s\n",
                $idx + 1,
                $s->nis,
                mb_strimwidth($s->nama, 0, 32, '...'),
                mb_strimwidth($s->asrama->nama_asrama ?? '-', 0, 20, '...'),
                mb_strimwidth($s->kelas->nama_kelas ?? '-', 0, 16, '...')
            );
        }

        $readmeContent .= "----------------------------------------------------------------------------------------------------\n";
        $readmeContent .= "Gunakan file SVG di atas untuk memasukkan QR Code ke desain template kartu ID santri Anda.\n";

        $zip->addFromString('_DAFTAR_SANTRI.txt', $readmeContent);
        $zip->close();

        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }

    /**
     * Download Satuan File QR Code Santri (Format SVG Vector).
     * Diberi nama format: "[NIS] - [Nama Santri].svg"
     */
    public function downloadSingleQr(Santri $santri): Response
    {
        $safeNama = trim(preg_replace('/[\\/\\\\:*?"<>|]/', '_', $santri->nama));
        $safeNis = trim(preg_replace('/[\\/\\\\:*?"<>|]/', '_', $santri->nis));
        $filename = "{$safeNis} - {$safeNama}.svg";

        $svgContent = QrCode::format('svg')
            ->size(500)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($santri->nis);

        return response($svgContent, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Simpan Santri Baru + Upload Foto + Auto Sinkronisasi Kamar + Auto Generate QR Code ID Card.
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalisasi kelas_id jika dikirim berupa tingkat/nama kelas (contoh '7 SMP')
        if ($request->filled('kelas_id') && !is_numeric($request->input('kelas_id'))) {
            $tingkatVal = trim($request->input('kelas_id'));
            $namaKelas = str_starts_with($tingkatVal, 'Kelas ') ? $tingkatVal : 'Kelas ' . $tingkatVal;
            $kelasObj = Kelas::firstOrCreate(
                ['nama_kelas' => $namaKelas],
                ['tingkat' => $tingkatVal]
            );
            $request->merge(['kelas_id' => $kelasObj->id]);
        }

        $validated = $request->validate([
            'nis' => 'required|string|max:50|unique:santris,nis',
            'nama' => 'required|string|max:255',
            'asrama_id' => 'required|exists:asramas,id',
            'kamar' => 'nullable|string|max:50',
            'kelas_id' => 'required|exists:kelas,id',
            'nama_wali' => 'nullable|string|max:100',
            'no_wa_wali' => 'nullable|string|max:25',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'nis.unique' => 'NIS ini sudah digunakan oleh santri lain.',
            'asrama_id.required' => 'Asrama santri wajib dipilih.',
            'kelas_id.required' => 'Kelas santri wajib dipilih.',
            'foto.image' => 'File foto harus berupa gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran file foto maksimal 5MB.',
        ]);

        // Upload file foto santri jika dilampirkan
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('santri-foto', 'public');
        }

        // Jika ada nama kamar yang dimasukkan, pastikan terdaftar pada tabel kamars
        if (! empty($validated['kamar'])) {
            Kamar::firstOrCreate(
                [
                    'asrama_id' => $validated['asrama_id'],
                    'nama_kamar' => trim($validated['kamar']),
                ],
                [
                    'kapasitas' => 10,
                    'keterangan' => 'Dibuat otomatis saat penambahan santri',
                ]
            );
        }

        $qrPath = 'qr-santri/'.$validated['nis'].'.svg';

        try {
            Storage::disk('public')->makeDirectory('qr-santri');
            QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->generate($validated['nis'], storage_path('app/public/'.$qrPath));
        } catch (\Throwable $e) {
            $qrPath = null;
        }

        Santri::create([
            'nis' => $validated['nis'],
            'nama' => $validated['nama'],
            'asrama_id' => $validated['asrama_id'],
            'kamar' => $validated['kamar'] ? trim($validated['kamar']) : null,
            'kelas_id' => $validated['kelas_id'],
            'nama_wali' => $validated['nama_wali'] ?? null,
            'no_wa_wali' => $validated['no_wa_wali'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'foto_path' => $fotoPath,
            'status' => 'aktif',
            'qr_code_path' => $qrPath,
        ]);

        $genderLabel = $validated['jenis_kelamin'] === 'L' ? 'Santri Putra' : 'Santri Putri';

        return redirect()->route('santri.index')
            ->with('success', "{$genderLabel} {$validated['nama']} ({$validated['nis']}) dengan foto dan QR Code ID Card berhasil ditambahkan!");
    }

    /**
     * Update Data Santri (Termasuk perbaikan NIS, Foto, & Jenis Kelamin).
     */
    public function update(Request $request, Santri $santri): RedirectResponse
    {
        // Normalisasi kelas_id jika dikirim berupa tingkat/nama kelas (contoh '7 SMP')
        if ($request->filled('kelas_id') && !is_numeric($request->input('kelas_id'))) {
            $tingkatVal = trim($request->input('kelas_id'));
            $namaKelas = str_starts_with($tingkatVal, 'Kelas ') ? $tingkatVal : 'Kelas ' . $tingkatVal;
            $kelasObj = Kelas::firstOrCreate(
                ['nama_kelas' => $namaKelas],
                ['tingkat' => $tingkatVal]
            );
            $request->merge(['kelas_id' => $kelasObj->id]);
        }

        $validated = $request->validate([
            'nis' => 'nullable|string|max:50|unique:santris,nis,'.$santri->id,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'asrama_id' => 'required|exists:asramas,id',
            'kamar' => 'nullable|string|max:50',
            'kelas_id' => 'required|exists:kelas,id',
            'nama_wali' => 'nullable|string|max:100',
            'no_wa_wali' => 'nullable|string|max:25',
            'status' => 'required|in:aktif,izin_pulang,skorsing,alumni,mutasi',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'foto.image' => 'File foto harus berupa gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran file foto maksimal 5MB.',
        ]);

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            if ($santri->foto_path && Storage::disk('public')->exists($santri->foto_path)) {
                Storage::disk('public')->delete($santri->foto_path);
            }
            $validated['foto_path'] = $request->file('foto')->store('santri-foto', 'public');
        }

        // Sinkronisasi kamar jika baru
        if (! empty($validated['kamar'])) {
            Kamar::firstOrCreate(
                [
                    'asrama_id' => $validated['asrama_id'],
                    'nama_kamar' => trim($validated['kamar']),
                ],
                [
                    'kapasitas' => 10,
                    'keterangan' => 'Didaftarkan saat perbaruan data santri',
                ]
            );
        }

        // Jika NIS berubah, regenerasi QR Code
        if (! empty($validated['nis']) && $validated['nis'] !== $santri->nis) {
            $qrPath = 'qr-santri/'.$validated['nis'].'.svg';
            try {
                Storage::disk('public')->makeDirectory('qr-santri');
                QrCode::format('svg')
                    ->size(300)
                    ->margin(1)
                    ->generate($validated['nis'], storage_path('app/public/'.$qrPath));
                $validated['qr_code_path'] = $qrPath;
            } catch (\Throwable $e) {
                // Abaikan jika error filesystem
            }
        }

        $santri->update($validated);

        return redirect()->route('santri.index')->with('success', "Data santri {$santri->nama} berhasil diperbarui!");
    }

    /**
     * Hapus Santri, File Foto, & File QR-nya.
     */
    public function destroy(Santri $santri): RedirectResponse
    {
        if ($santri->qr_code_path && Storage::disk('public')->exists($santri->qr_code_path)) {
            Storage::disk('public')->delete($santri->qr_code_path);
        }

        if ($santri->foto_path && Storage::disk('public')->exists($santri->foto_path)) {
            Storage::disk('public')->delete($santri->foto_path);
        }

        $nama = $santri->nama;
        $santri->delete();

        return redirect()->route('santri.index')->with('success', "Data santri {$nama} berhasil dihapus!");
    }

    /**
     * Hapus Santri Secara Massal / Jumlah Tertentu Berdasarkan ID Terpilih.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:santris,id',
        ], [
            'ids.required' => 'Pilih minimal satu data santri yang ingin dihapus.',
            'ids.min' => 'Pilih minimal satu data santri yang ingin dihapus.',
        ]);

        $ids = $validated['ids'];
        $user = $request->user();

        $query = Santri::whereIn('id', $ids);

        // Pertahankan batasan gender bila musyrif/musyrifah
        if ($user && $user->gender === 'putra') {
            $query->where('jenis_kelamin', 'L');
        } elseif ($user && $user->gender === 'putri') {
            $query->where('jenis_kelamin', 'P');
        }

        $santris = $query->get();
        $count = $santris->count();

        if ($count === 0) {
            return redirect()->route('santri.index')->with('error', 'Tidak ada data santri yang memenuhi syarat untuk dihapus.');
        }

        DB::transaction(function () use ($santris) {
            foreach ($santris as $santri) {
                if ($santri->qr_code_path && Storage::disk('public')->exists($santri->qr_code_path)) {
                    Storage::disk('public')->delete($santri->qr_code_path);
                }

                if ($santri->foto_path && Storage::disk('public')->exists($santri->foto_path)) {
                    Storage::disk('public')->delete($santri->foto_path);
                }

                $santri->delete();
            }
        });

        return redirect()->route('santri.index')->with('success', "Berhasil menghapus {$count} data santri terpilih!");
    }

    /**
     * Menyimpan data vektor deskriptor wajah santri hasil perekaman kamera/foto.
     */
    public function saveFaceDescriptor(Request $request, Santri $santri): JsonResponse
    {
        $validated = $request->validate([
            'face_descriptor' => ['required', 'array', 'size:128'],
            'face_descriptor.*' => ['required', 'numeric'],
        ], [
            'face_descriptor.required' => 'Data vektor fitur wajah wajib disertakan.',
            'face_descriptor.size' => 'Data vektor wajah harus memiliki 128 dimensi koordinat.',
        ]);

        $santri->update([
            'face_descriptor' => $validated['face_descriptor'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Vektor wajah untuk santri {$santri->nama} ({$santri->nis}) berhasil disimpan!",
            'santri'  => [
                'id'                  => $santri->id,
                'nis'                 => $santri->nis,
                'nama'                => $santri->nama,
                'has_face_descriptor' => true,
            ],
        ]);
    }

    /**
     * Menghapus data vektor wajah santri.
     */
    public function deleteFaceDescriptor(Santri $santri): JsonResponse
    {
        $santri->update(['face_descriptor' => null]);

        return response()->json([
            'success' => true,
            'message' => "Data wajah santri {$santri->nama} berhasil dihapus.",
        ]);
    }
}
