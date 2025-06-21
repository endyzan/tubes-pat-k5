<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\KegiatanController;


/* =============================================================================================================================================== /*
 * =============================================================== Tutorial Routing ============================================================== //
 * =============================================================================================================================================== */

/* Tambahkan middleware untuk memastikan token valid sebelum mengakses rute berikutnya /*
 * Tambah line code ini pada belakang route untuk menambahkan verifikasi token /*

/* Contoh: Route::get('/some-route', [SomeController::class, 'someMethod'])->middleware('some_middleware'); */

/* Middleware yang tersedia */

/* - verify-token: Memastikan token yang digunakan valid */
/* - admin-level: Memastikan pengguna memiliki level admin ( puncak level akses ) */
/* - volunteer-level: Memastikan pengguna memiliki level relawan keatas */
/* - user-level: Memastikan pengguna memiliki level pengguna biasa keatas */

/* NB : Middleware "-level" merupakan middleware group, jadi ia sudah include cek token + cek role */

/* =============================================================================================================================================== /*
 * ============================================================== All Route Section ============================================================== //
 * =============================================================================================================================================== */


// =============================== Landing Page Route ============================ //

// Home Page
Route::get('/', function () {
    return view('index');
})->name('home');

// User Dashboard
Route::get('/user', function () {
    return view('user.index');
})->name('user-dashboard')->middleware('user-level');

// Admin or Volunteer Dashboard
Route::get('/admin', function () {
    return view('admin.index');
})->name('admin-dashboard')->middleware('volunteer-level');

// ============================== Authentication Routes =========================== //
include 'authentication.php';

// ============================= User Management Routes =========================== //
include 'user-management.php';

// =================================== Proxy Routes =============================== //
include 'proxy.php';
