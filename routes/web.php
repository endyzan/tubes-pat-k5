<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/api-proxy/donasi', function (Request $request) {
    $authHeader = $request->header('Authorization'); // Ambil header Authorization dari browser

    if (!$authHeader) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader); // Buang kata 'Bearer '

    $response = Http::withToken($token)
        ->get('https://api-mdonasi-core.vercel.app/api/donasi');

    return response()->json($response->json(), $response->status());
});

Route::get('/api-proxy/donasi/status/{status}', function ($status) {
    $response = Http::get("https://api-mdonasi-core.vercel.app/api/donasi/status/{$status}"); 
    return response()->json($response->json());
});

Route::delete('/api-proxy/donasi/{id}', function (Request $request, $id) {
    $authHeader = $request->header('Authorization');

    if (!$authHeader) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);

    $response = Http::withToken($token)
        ->delete("https://api-mdonasi-core.vercel.app/api/donasi/{$id}");

    return response()->json($response->json(), $response->status());
});

Route::put('/api-proxy/donasi/{id}', function (Request $request, $id) {
    $authHeader = $request->header('Authorization');

    if (!$authHeader) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);

    $response = Http::withToken($token)
        ->put("https://api-mdonasi-core.vercel.app/api/donasi/{$id}", $request->all());

    return response()->json($response->json(), $response->status());
});

Route::post('/api-proxy/donasi', function (Request $request) {
    $authHeader = $request->header('Authorization');

    if (!$authHeader) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);

    $response = Http::withToken($token)
        ->post("https://api-mdonasi-core.vercel.app/api/donasi", $request->all());

    return response()->json($response->json(), $response->status());
});

// --- BARU: ROUTE UNTUK VALIDASI DONASI ADMIN ---
Route::put('/api-proxy/validasi-donasi/admin/validasibyadmin', function (Request $request) {
    $authHeader = $request->header('Authorization');

    if (!$authHeader) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);

    // Ambil data dari request body frontend
    // Pastikan `id_donasi`, `status_validasi`, `catatan_validasi`, dan `validator` ada di request body
    $validatedData = $request->validate([
        'id_donasi' => 'required|integer',
        'status_validasi' => 'required|string|in:accepted,rejected',
        'catatan_validasi' => 'nullable|string',
        'validator' => 'required|string', // Sesuaikan dengan kebutuhan Anda, mungkin dari user JWT
    ]);

    // Lakukan proxy ke endpoint backend sesuai dokumentasi
    $response = Http::withToken($token)
        ->put("https://api-mdonasi-core.vercel.app/api/validasi-donasi/admin/validasibyadmin", $validatedData);

    return response()->json($response->json(), $response->status());
});



//Web Routes

Route::get('/user', function () {
    return view('user.index');
});


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

// Show New Forgotten Password
Route::get('forgot-password/submit/new-password/{verify_code}', [Authentication::class, 'showNewPasswordForm'])->name('password.reset.form');

// Confirm New Password
Route::patch('forgot-password/submit/confirm-new-password/{token}', [Authentication::class, 'submitNewPassword'])->name('confirm-password.reset.form');

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
