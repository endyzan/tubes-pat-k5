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

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        try {
            $response = Http::post("{$this->baseUrl}/auth/reset-password", [
                'email' => $request->input('email'),
            ]);


            if ($response->successful()) {
                return back()->with('success', $response->json('message') ?? 'Link reset password telah dikirim ke email Anda.');
            }

            if ($response->status() === 400) {
                $errorMessage = $response->json('message') ?? 'Terjadi kesalahan validasi.';
                $errorDetails = $response->json('errors') ?? [];

                return back()->withErrors([
                    'forgot_password' => $errorMessage,
                    'details' => implode(', ', $errorDetails),
                ]);
            }

            return back()->withErrors([
                'forgot_password' => 'Gagal mengirim permintaan reset password. Silakan coba lagi nanti.',
            ]);
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors([
                'forgot_password' => 'Terjadi kesalahan saat menghubungi server.',
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:100',
            'nama_lengkap' => 'nullable|string|max:100',
            'telp' => 'nullable|string|max:15',

            // Tidak boleh mengupdate ini
            'password' => 'prohibited',
            'role' => 'prohibited',
        ], [
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
            'telp.string' => 'Nomor telepon harus berupa teks.',
            'telp.max' => 'Nomor telepon maksimal 15 karakter.',
            'password.prohibited' => 'Password tidak boleh diubah dari sini.',
            'role.prohibited' => 'Role tidak boleh diubah.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Ambil token user (asumsi disimpan di session atau melalui Auth::user())
        $token = session('token'); // atau sesuaikan sumber token kamu

        if (!$token) {
            return back()->withErrors(['update' => 'Token akses diperlukan.']);
        }

        try {
            $response = Http::withToken($token)
                ->patch($this->baseUrl . '/auth/profile/update', $validator->validated());

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
            }

            if ($response->status() === 400 || $response->status() === 401) {
                return back()->withErrors([
                    'update' => $response->json('message') ?? 'Gagal memperbarui profil.',
                ])->withInput();
            }

            return back()->withErrors(['update' => 'Terjadi kesalahan saat memperbarui profil.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['update' => 'Kesalahan server: ' . $e->getMessage()])->withInput();
        }
    }
}



/* List of API Endpoints

1. POST /auth/register – Registrasi akun baru ✓

2. POST /auth/login – Login akun ✓

3. GET /auth/verify-token – Verifikasi token ✓ AS MIDDLEWARE

4. POST /auth/reset-password – Lupa password

5. PATCH /auth/reset-password/verify/{Token} – Verifikasi lupa password

6. POST /auth/riwayat-token – Mendapatkan Riwayat penggunaan Token

7. Update Profile User /auth/profile/update
*/
