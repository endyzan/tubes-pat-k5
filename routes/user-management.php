<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\KegiatanController;


/* ================================================================================= /*
* ============================== User Management Routes =========================== //
* ================================================================================= */

Route::middleware('user-level')->group(function () {
    // Halaman profil
    Route::get('/profile', function () {
        return view('settings');
    })->name('profile');

    // Update profil
    Route::patch('/profile/update', [Authentication::class, 'updateProfile'])->name('profile.update');

    // Update password
    Route::patch('/profile/update/password', [Authentication::class, 'updatePassword'])->name('profile.update.password');
});
