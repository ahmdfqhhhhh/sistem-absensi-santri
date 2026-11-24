<?php

use App\Http\Controllers\AsramaController;
use App\Http\Controllers\JadwalSholatController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\SesiAbsensiController;
use App\Http\Controllers\Web\AbsensiWebController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BendaharaWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\KamtibWebController;
use App\Http\Controllers\Web\KeuanganSuperAdminController;
use App\Http\Controllers\Web\PerizinanWebController;
use App\Http\Controllers\Web\RekapWebController;
use App\Http\Controllers\Web\WaliPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Absensi Santri (Role-Based Access Control)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTE AUTENTIKASI (PUBLIC)
// ==========================================
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register-wali', [AuthController::class, 'registerWaliForm'])->name('register.wali');
Route::post('/register-wali', [AuthController::class, 'registerWali'])->name('register.wali.process');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 2. RUTE TERPROTEKSI (MEMERLUKAN LOGIN)
// ==========================================
Route::middleware('auth')->group(function () {

    // --- DASHBOARD ---
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    // --- FITUR OPERASIONAL PONDOK (Super Admin & Musyrif) ---
    Route::middleware('role:super_admin,musyrif')->group(function () {

        // Modul Pemindai Presensi QR Code & Face Recognition AI
        Route::get('/scan', [AbsensiWebController::class, 'index'])->name('absensi.scan');
        Route::post('/scan', [AbsensiWebController::class, 'scan'])->name('absensi.scan.process');
        Route::get('/absensi/face-descriptors', [AbsensiWebController::class, 'getFaceDescriptors'])->name('absensi.face-descriptors');

        // Modul Presensi Checklist Batch
        Route::get('/absensi/batch', [AbsensiWebController::class, 'batchIndex'])->name('absensi.batch');
        Route::post('/absensi/batch', [AbsensiWebController::class, 'batchStore'])->name('absensi.batch.process');

        // Modul Master Data Santri & ID Card (Pemisahan Putra & Putri)
        Route::post('/santri/{santri}/face-descriptor', [SantriController::class, 'saveFaceDescriptor'])->name('santri.face-descriptor.save');
        Route::delete('/santri/{santri}/face-descriptor', [SantriController::class, 'deleteFaceDescriptor'])->name('santri.face-descriptor.delete');
        Route::get('/santri/cetak-kartu', [SantriController::class, 'cetakKartuMassal'])->name('santri.cetak-kartu');
        Route::get('/santri/download-qr-zip', [SantriController::class, 'downloadQrZip'])->name('santri.download-qr-zip');
        Route::get('/santri/{santri}/download-qr', [SantriController::class, 'downloadSingleQr'])->name('santri.download-qr');
        Route::post('/santri/bulk-delete', [SantriController::class, 'bulkDestroy'])->name('santri.bulk-delete');
        Route::resource('santri', SantriController::class);

        // Modul CRUD Kamar
        Route::post('/kamar', [KamarController::class, 'store'])->name('kamar.store');
        Route::put('/kamar/{kamar}', [KamarController::class, 'update'])->name('kamar.update');
        Route::delete('/kamar/{kamar}', [KamarController::class, 'destroy'])->name('kamar.destroy');

        // Modul CRUD Asrama
        Route::post('/asrama', [AsramaController::class, 'store'])->name('asrama.store');
        Route::put('/asrama/{asrama}', [AsramaController::class, 'update'])->name('asrama.update');
        Route::delete('/asrama/{asrama}', [AsramaController::class, 'destroy'])->name('asrama.destroy');

        // Modul CRUD Kelas
        Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
        Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
        Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

        // Modul Manajemen Sesi Absensi
        Route::post('/sesi-absensi', [SesiAbsensiController::class, 'store'])->name('sesi.store');
        Route::post('/sesi-absensi/generate-harian', [SesiAbsensiController::class, 'generateDaily'])->name('sesi.generate');
        Route::put('/sesi-absensi/{sesi}/status', [SesiAbsensiController::class, 'updateStatus'])->name('sesi.status');

        // Modul Pengaturan Jadwal Sholat & Sesi Harian
        Route::get('/pengaturan/jadwal', [JadwalSholatController::class, 'index'])->name('jadwal.index');
        Route::put('/pengaturan/jadwal', [JadwalSholatController::class, 'update'])->name('jadwal.update');
        Route::post('/pengaturan/jadwal/reset', [JadwalSholatController::class, 'reset'])->name('jadwal.reset');
    });

    // --- MODUL REKAPITULASI (ABSENSI & PERIZINAN KE EXCEL & PDF) ---
    Route::middleware('role:super_admin,musyrif,keamanan')->prefix('rekap')->name('rekap.')->group(function () {
        // Rekap Absensi (Bulanan & Harian)
        Route::get('/absensi', [RekapWebController::class, 'indexAbsensi'])->name('absensi');
        Route::get('/absensi/excel', [RekapWebController::class, 'exportAbsensiExcel'])->name('absensi.excel');
        Route::get('/absensi/pdf', [RekapWebController::class, 'exportAbsensiPdf'])->name('absensi.pdf');
        Route::get('/absensi/harian', [RekapWebController::class, 'indexAbsensiHarian'])->name('absensi.harian');
        Route::get('/absensi/harian/excel', [RekapWebController::class, 'exportAbsensiHarianExcel'])->name('absensi.harian.excel');
        Route::get('/absensi/harian/pdf', [RekapWebController::class, 'exportAbsensiHarianPdf'])->name('absensi.harian.pdf');

        // Rekap Perizinan (Bulanan & Harian)
        Route::get('/perizinan', [RekapWebController::class, 'indexPerizinan'])->name('perizinan');
        Route::get('/perizinan/excel', [RekapWebController::class, 'exportPerizinanExcel'])->name('perizinan.excel');
        Route::get('/perizinan/pdf', [RekapWebController::class, 'exportPerizinanPdf'])->name('perizinan.pdf');
        Route::get('/perizinan/harian', [RekapWebController::class, 'indexPerizinanHarian'])->name('perizinan.harian');
        Route::get('/perizinan/harian/excel', [RekapWebController::class, 'exportPerizinanHarianExcel'])->name('perizinan.harian.excel');
        Route::get('/perizinan/harian/pdf', [RekapWebController::class, 'exportPerizinanHarianPdf'])->name('perizinan.harian.pdf');
    });

    // --- MODUL PERIZINAN SANTRI ---
    Route::prefix('perizinan')->name('perizinan.')->group(function () {
        Route::get('/', [PerizinanWebController::class, 'index'])->name('index');
        Route::post('/', [PerizinanWebController::class, 'store'])->name('store');
        Route::get('/{id}/surat', [PerizinanWebController::class, 'surat'])
            ->name('surat')
            ->whereNumber('id');

        // Approval & Rejection hanya untuk Super Admin & Musyrif
        Route::middleware('role:super_admin,musyrif')->group(function () {
            Route::post('/{id}/approve', [PerizinanWebController::class, 'approve'])
                ->name('approve')
                ->whereNumber('id');

            Route::post('/{id}/reject', [PerizinanWebController::class, 'reject'])
                ->name('reject')
                ->whereNumber('id');
        });
    });

    // --- MODUL POS KEAMANAN GERBANG (Dahulu Pos Kamtib) ---
    Route::middleware('role:keamanan,super_admin')
        ->prefix('kamtib')
        ->name('kamtib.')
        ->group(function () {
            Route::get('/', [KamtibWebController::class, 'index'])->name('index');
            Route::post('/scan', [KamtibWebController::class, 'scanProcess'])->name('scan.process');
        });

    // Alias Rute Keamanan
    Route::middleware('role:keamanan,super_admin')->get('/keamanan', [KamtibWebController::class, 'index'])->name('keamanan.index');

    // --- PORTAL KHUSUS WALI SANTRI ---
    Route::middleware('role:wali_santri,super_admin')->group(function () {
        Route::get('/wali', [WaliPortalController::class, 'portal'])->name('wali.portal');
    });

    // --- FITUR KEUANGAN BENDAHARA ---
    Route::middleware('role:bendahara')->prefix('bendahara')->name('bendahara.')->group(function () {
        Route::get('/', [BendaharaWebController::class, 'index'])->name('index');
        Route::get('/rekap', [BendaharaWebController::class, 'rekap'])->name('rekap');
        Route::get('/santri/{santri}', [BendaharaWebController::class, 'detailSantri'])->name('santri.detail');
        Route::get('/tagihan', [BendaharaWebController::class, 'tagihanIndex'])->name('tagihan.index');
        Route::post('/tagihan', [BendaharaWebController::class, 'tagihanStore'])->name('tagihan.store');
        Route::get('/verifikasi', [BendaharaWebController::class, 'verifikasiIndex'])->name('verifikasi');
        Route::post('/verifikasi/{tagihan}', [BendaharaWebController::class, 'konfirmasi'])->name('konfirmasi');
        Route::post('/tagihan/{tagihan}/tunai', [BendaharaWebController::class, 'bayarTunai'])->name('bayar.tunai');
        Route::get('/kuitansi/{tagihan}', [BendaharaWebController::class, 'kuitansi'])->name('kuitansi');
    });

    // --- KEUANGAN SUPER ADMIN (view only tunggakan) ---
    Route::middleware('role:super_admin')->get('/keuangan/tunggakan', [KeuanganSuperAdminController::class, 'tunggakan'])->name('keuangan.tunggakan');

    // --- PEMBAYARAN WALI SANTRI ---
    Route::middleware('role:wali_santri')->group(function () {
        Route::get('/wali/pembayaran', [WaliPortalController::class, 'pembayaran'])->name('wali.pembayaran');
        Route::post('/wali/pembayaran/{tagihan}', [WaliPortalController::class, 'prosesBayar'])->name('wali.bayar');
        Route::get('/wali/kuitansi/{tagihan}', [WaliPortalController::class, 'kuitansi'])->name('wali.kuitansi');
    });
});
