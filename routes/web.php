<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\TestingPurpose\Crypto;


//Web Routes


/* ================================================================================= /*
 * ============================== Landing Page Routes ============================== //
 * ================================================================================= */

// Admin or Volunteer Dashboard
Route::get('/admin', function () {
    return view('admin.index');
})->name('admin-dashboard')->middleware('role-checker');;

// Home Page
Route::get('/', function () {
    return view('index');
})->name('home');

/* ================================================================================= /*
 * ============================== Authentication Routes ============================ //
 * ================================================================================= */

//  Register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

//  Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Submit Registration
Route::post('/register/submit', [Authentication::class, 'register'])->name('register.submit');

// Submit Login
Route::post('/login/submit', [Authentication::class, 'login'])->name('login.submit');

// Logout
Route::get('/logout', [Authentication::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');

// Submit Forgot Password
Route::post('/forgot-password/submit', [Authentication::class, 'forgotPassword'])->name('forgot-password.submit');

/* ================================================================================= /*
 * ============================== User Management Routes =========================== //
 * ================================================================================= */

// Profile
Route::get('/profile', function () {
    return view('settings');
})->name('profile');

// Update Profile
Route::patch('/profile/update', [Authentication::class, 'updateProfile'])->name('profile.update')->middleware('verify-token');


/* Tambahkan middleware untuk memastikan token valid sebelum mengakses rute berikutnya */
/* Tambah line code ini pada belakang route untuk menambahkan verifikasi token */

// ->middleware('verify-token');

/* Contoh: Route::get('/some-route', [SomeController::class, 'someMethod'])->middleware('verify-token'); */


/* Other Middleware Routes */
// ->middleware('role-checker');  => Middleware untuk memeriksa peran pengguna (admin atau volunteer) sebelum mengakses rute tertentu, jika tidak sesuai terhadap hak akses maka tidak bisa akses.
