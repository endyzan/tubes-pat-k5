<?php

use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Support\Facades\Route;


//Web Routes



// This route is for testing purposes only
// Route::get('/', [Crypto::class, 'index']);


Route::get('/', function () {
    return view('index');
});
