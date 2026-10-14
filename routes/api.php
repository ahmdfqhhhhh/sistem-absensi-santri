<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\KamtibController;
use App\Http\Controllers\Api\PerizinanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - E-Absensi Santri
|--------------------------------------------------------------------------
*/

// Presensi / Absensi Routes
Route::prefix('absensi')->name('api.absensi.')->group(function () {
    Route::post('/scan-qr', [AbsensiController::class, 'scanQr'])->name('scan-qr');
});
Route::post('/scan-qr', [AbsensiController::class, 'scanQr'])->name('api.scan-qr');

// Perizinan Santri Routes
Route::prefix('perizinan')->name('api.perizinan.')->group(function () {
    Route::get('/', [PerizinanController::class, 'index'])->name('index');
    Route::post('/', [PerizinanController::class, 'store'])->name('store');
    Route::get('/{id}', [PerizinanController::class, 'show'])->name('show')->whereNumber('id');
    Route::post('/{id}/approve', [PerizinanController::class, 'approve'])->name('approve')->whereNumber('id');
    Route::post('/{id}/reject', [PerizinanController::class, 'reject'])->name('reject')->whereNumber('id');
    Route::get('/verify/{token}', [PerizinanController::class, 'verify'])->name('verify');
    Route::post('/{id}/catat-kembali', [PerizinanController::class, 'catatKembali'])->name('catat-kembali')->whereNumber('id');
    Route::get('/{id}/qrcode', [PerizinanController::class, 'qrCodeSvg'])->name('qrcode')->whereNumber('id');
});

// Kamtib (Keamanan & Ketertiban) Routes
Route::prefix('kamtib')->name('api.kamtib.')->group(function () {
    Route::post('/scan-surat-izin', [KamtibController::class, 'scanSuratIzin'])->name('scan-surat-izin');
});
