<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\KegiatanController;



/* ================================================================================= /*
 * ============================== integrasi modul 4 ================================ //
 * ================================================================================= */

Route::prefix('kegiatan')->name('kegiatan.')->middleware(['role-checker'])->group(function () {
    Route::get('/', [KegiatanController::class, 'index'])->name('index');
    Route::get('/create', [KegiatanController::class, 'create'])->name('create');
    Route::post('/', [KegiatanController::class, 'store'])->name('store');
    Route::get('/{id}', [KegiatanController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [KegiatanController::class, 'edit'])->name('edit');
    Route::patch('/{id}', [KegiatanController::class, 'update'])->name('update');
    Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy');
});
