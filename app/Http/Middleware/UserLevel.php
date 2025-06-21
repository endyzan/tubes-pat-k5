<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class UserLevel
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Session::get('user');

        // Jika tidak ada user atau token di session
        if (!$user || !isset($user['token'])) {
            return redirect()->route('login')->withErrors(['login' => 'Anda Belum Login. Silakan Login Terlebih Dahulu.']);
        }
        // Cek role
        if ($user['role'] == 'admin' || $user['role'] == 'volunteer' || $user['role'] == 'user') {
            return $next($request);
        }
        abort(403, 'Unauthorized access. You do not have permission to view this page.');
    }
}
