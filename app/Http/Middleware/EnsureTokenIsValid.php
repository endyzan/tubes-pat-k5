<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    protected $baseUrl = 'https://donation-api-auth.vercel.app';

    public function handle(Request $request, Closure $next): Response
    {
        $user = Session::get('user');

        if (!$user || !isset($user['token'])) {
            return redirect()->route('login')->withErrors(['login' => 'Anda Belum Login. Silakan Login Terlebih Dahulu.']);
        }

        $token = $user['token'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . '/auth/verify-token');

        if ($response->successful() && $response->json('success') === true) {
            return $next($request);
        } elseif ($response->status() === 401) {
            Session::forget('user');
            return redirect()->route('login')->withErrors(['login' => 'Login anda sudah tidak valid atau sudah kedaluwarsa. Silakan Login Lagi Terlebih Dahulu.']);
        } else {
            abort(500, 'Internal Server Error.');
        }
    }
}
