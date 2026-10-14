<?php

use App\Http\Controllers\Web\AbsensiWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Absensi Santri
|--------------------------------------------------------------------------
*/

// Akses utama dan antarmuka pemindai QR Code presensi santri
Route::get('/', [AbsensiWebController::class, 'index'])->name('home');
Route::get('/scan', [AbsensiWebController::class, 'index'])->name('absensi.scan');
Route::post('/scan', [AbsensiWebController::class, 'scan'])->name('absensi.scan.process');
