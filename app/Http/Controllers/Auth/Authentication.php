<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class Authentication extends Controller
{
    private $baseUrl = 'https://donation-api-auth.vercel.app';

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required|string|max:32',
            'confirm-password' => 'required|same:password',
            'nama_lengkap' => 'required|string|max:100',
            'telp' => 'required|string|max:15',
            'role' => 'nullable|in:user,volunteer,admin',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',

            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.max' => 'Password maksimal 32 karakter.',

            'confirm-password.required' => 'Konfirmasi password wajib diisi.',
            'confirm-password.same' => 'Konfirmasi password tidak cocok dengan password.',

            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',

            'telp.required' => 'Nomor telepon wajib diisi.',
            'telp.string' => 'Nomor telepon harus berupa teks.',
            'telp.max' => 'Nomor telepon maksimal 15 karakter.',

            'role.in' => 'Role hanya boleh bernilai user, volunteer, atau admin.',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $role = $request->filled('role') ? $request->role : 'user';
        $role = 'admin'; // Set role volunteer ke user

        $response = Http::post($this->baseUrl . '/auth/register', [
            'email' => $request->email,
            'password' => $request->password,
            'nama_lengkap' => $request->nama_lengkap,
            'telp' => $request->telp,
            'role' => $role,
        ]);

        $redirectPage = $role === 'user' ? 'home' : 'admin-dashboard';

        if ($response->successful()) {
            return redirect()->route($redirectPage)->with('success', 'Registrasi berhasil. Silakan login.');
        } elseif ($response->status() === 400) {
            $message = $response->json('message') ?? 'Registrasi gagal.';
            $errors = $response->json('errors') ?? [];

            return back()->withInput()->withErrors([
                'register' => $message,
                'details' => is_array($errors) ? implode(', ', $errors) : $errors,
            ]);
        } else {
            return back()->withInput()->withErrors([
                'register' => 'Terjadi kesalahan saat menghubungi server.',
            ]);
        }
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',

            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
        ]);

        // Kirim request ke API login
        $response = Http::post($this->baseUrl . '/auth/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->status() === 200 && $response->json('success') === true) {
            // Simpan data user dan token ke session
            $userData = $response->json('data');

            Session::put('user', [
                'id' => $userData['id'],
                'email' => $userData['email'],
                'nama_lengkap' => $userData['nama_lengkap'],
                'telp' => $userData['telp'],
                'role' => $userData['role'],
                'token' => $userData['token'],
            ]);

            $redirectPage = $userData['role'] === 'user' ? 'home' : 'admin-dashboard';

            return redirect()->route($redirectPage)->with('success', 'Login berhasil!');
        } elseif ($response->status() === 400) {
            return back()->withInput()->withErrors([
                'login' => $response->json('message'),
                'details' => implode(', ', $response->json('errors') ?? []),
            ]);
        } elseif ($response->status() === 401) {
            return back()->withInput()->withErrors([
                'login' => $response->json('message'),
            ]);
        } else {
            return back()->withInput()->withErrors([
                'login' => 'Terjadi kesalahan saat menghubungi server.',
            ]);
        }
    }

    public function logout()
    {
        Session::forget('user');
        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}



/* List of API Endpoints

1. POST /auth/register – Registrasi akun baru ✓

2. POST /auth/login – Login akun ✓

3. GET /auth/verify-token – Verifikasi token

4. POST /auth/forgot-password – Lupa password

5. PATCH /auth/forgot-password/verify?code={unique code} – Verifikasi lupa password

6. POST /auth/riwayat-token – Menambah riwayat token baru

*/
