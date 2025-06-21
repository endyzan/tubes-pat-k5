<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\KegiatanController;

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

// Show New Forgotten Password
Route::get('forgot-password/submit/new-password/{verify_code}', [Authentication::class, 'showNewPasswordForm'])->name('password.reset.form');

// Confirm New Password
Route::patch('forgot-password/submit/confirm-new-password/{token}', [Authentication::class, 'submitNewPassword'])->name('confirm-password.reset.form');
