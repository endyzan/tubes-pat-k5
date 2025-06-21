<?php

use App\Http\Controllers\ModulTiga\ManajemenPenerimaDonasi;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\ModulTiga\ManajemenDistribusiDonasi;

// ===============================================================================================================================================
// =============================================================== Tutorial Routing ==============================================================
// ===============================================================================================================================================

// Tambahkan middleware untuk memastikan token valid sebelum mengakses rute berikutnya
// Tambah line code ini pada belakang route untuk menambahkan verifikasi token

// Contoh: Route::get('/some-route', [SomeController::class, 'someMethod'])->middleware('some_middleware');

// Middleware yang tersedia

// - verify-token: Memastikan token yang digunakan valid
// - admin-level: Memastikan pengguna memiliki level admin ( puncak level akses )
// - volunteer-level: Memastikan pengguna memiliki level relawan keatas
// - user-level: Memastikan pengguna memiliki level pengguna biasa keatas

// NB : Middleware "-level" merupakan middleware group, jadi ia sudah include cek token + cek role

// ===============================================================================================================================================
// ============================================================== All Route Section ==============================================================
// ===============================================================================================================================================


// =============================== Landing Page Route ============================ //

// Home Page
Route::view('/', 'index')->name('home');

// User Dashboard
Route::view('/user', 'user.index')->name('user-dashboard')->middleware('user-level');

// Admin or Volunteer Dashboard
Route::view('/admin', 'admin.index')->name('admin-dashboard')->middleware('volunteer-level');

// ============================== Authentication Routes =========================== //
include 'authentication.php';

// ============================= User Management Routes =========================== //
include 'user-management.php';

// =================================== Proxy Routes =============================== //
include 'proxy.php';

// ================================ Integrasi Modul 4 Routes ======================== //
include 'integrasi-modul-4.php';

// ================================ Integrasi Modul 3 Routes ======================== //
// Manajemen Penerima Donasi
Route::middleware('volunteer-level')->group(function () {
    Route::get('/recipients', [ManajemenPenerimaDonasi::class, 'index'])->name('recipients.index');
    Route::post('/recipients', [ManajemenPenerimaDonasi::class, 'store'])->name('recipients.store');
    Route::put('/recipients/{id}', [ManajemenPenerimaDonasi::class, 'update'])->name('recipients.update');
    Route::delete('/recipients/{id}', [ManajemenPenerimaDonasi::class, 'destroy'])->name('recipients.destroy');
    Route::patch('/recipients/{id}/activate', [ManajemenPenerimaDonasi::class, 'activate'])->name('recipients.activate');
});


// Manajemen Distribusi Donasi
Route::middleware('volunteer-level')->group(function () {
    Route::get('/dist/donasi', [ManajemenDistribusiDonasi::class, 'index'])->name('dist.donasi.index');
    Route::get('/dist/donasi/distribution/{id}', [ManajemenDistribusiDonasi::class, 'getDistribution'])->name('dist.donasi.getDistribution');
    Route::post('/dist/donasi/distribution', [ManajemenDistribusiDonasi::class, 'store'])->name('dist.donasi.storeDistribution');
    Route::put('/distributions/status', [ManajemenDistribusiDonasi::class, 'updateStatusDistribusi'])->name('distributions.update-status');
});
