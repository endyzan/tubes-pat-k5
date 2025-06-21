<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestingPurpose\Crypto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\Authentication;
use App\Http\Controllers\KegiatanController;


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
