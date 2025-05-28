<?php

use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Support\Facades\Route;


//Web Routes



// This route is for testing purposes only
Route::get('/admin', function () {
    return view('admin.index');
});


Route::get('/', function () {
    return view('index');
});


// AUTH ROUTES
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
