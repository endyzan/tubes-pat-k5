<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleChecker
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Session::get('user');

        // Jika tidak ada user atau token di session
        if (!$user || !isset($user['token'])) {
            return redirect()->route('login')->withErrors(['login' => 'Anda Belum Login. Silakan Login Terlebih Dahulu.']);
        }

        // Verifikasi token ke API
        try {
            $response = Http::withToken($user['token'])
                ->get(config('services.donation_api.url').'/test-clorinde-mode');

            if (!$response->successful()) {
                Session::forget('user');
                return redirect()->route('login')->withErrors(['login' => 'Session expired, please login again']);
            }
        } catch (\Exception $e) {
            Session::forget('user');
            return redirect()->route('login')->withErrors(['login' => 'Error verifying token']);
        }

        // Cek role
        if ($user['role'] != 'admin' && $user['role'] != 'volunteer') {
            abort(403, 'Unauthorized access. You do not have permission to view this page.');
        }

        return $next($request);
    }
}