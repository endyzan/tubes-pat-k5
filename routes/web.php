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
})->name('admin-dashboard');

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
