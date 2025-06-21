<?php

namespace App\Http\Controllers\ModulTiga;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ManajemenDistribusiDonasi extends Controller
{
    private $baseUrl = 'https://kuliah2025.my.id/modul.3_distributions/';
    private $baseUrl2 = 'https://api-mdonasi-core.vercel.app/api/';

    public function index(Request $request)
    {
        $token = session('user.token');

        // Ambil query parameter jika tersedia
        $query = [
            'page' => $request->get('page', 1),
            'limit' => $request->get('limit', 10),
        ];

        if ($request->filled('status')) {
            $query['status'] = $request->get('status');
        }

        // Request ke API
        $response = Http::withToken($token)
            ->get($this->baseUrl2 . 'donasi', $query);

        if ($response->successful()) {
            $result = $response->json();

            return view('admin.distribusi.index', [
                'donasi' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? [],
                'status_filter' => $query['status'] ?? '',
            ]);
        }

        $errorMsg = $response->json('message') ?? 'Gagal mengambil daftar donasi';
        return back()->with('error', $errorMsg);
    }

    public function getDistribution($id)
    {
        // 7. Mengambil Daftar Distribusi
        // GET /distributions.php?donasi_id=50

        $token = session('user.token');
        $donasi_id = $id;
        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->get($this->baseUrl . 'distributions.php?donasi_id=' . $donasi_id);
        $distributions = $response->json();

        // Mengambil Daftar Penerima
        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->get($this->baseUrl . 'recipients.php');

        $recipients = $response->successful() ? $response->json() ?? [] : [];
        return view('admin.distribusi.distribution', compact('distributions', 'donasi_id', 'recipients'));
    }

    public function store(Request $request)
    {
        // 6. Mendistribusikan Donasi
        // POST /distributions.php

        $token = session('user.token');
        // Validasi input form
        $validated = $request->validate([
            'donasi_id' => 'required|integer',
            'recipients' => 'required|array|min:1',
            'recipients.*.id' => 'required|integer',
            'recipients.*.amount' => 'required|numeric|min:1',
        ]);

        // Siapkan payload sesuai format API
        $payload = [
            'donasi_id' => $validated['donasi_id'],
            'recipients' => $validated['recipients']
        ];

        // Kirim request ke API distribusi
        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->post($this->baseUrl . 'distributions.php', $payload);

        // Jika sukses
        if ($response->successful()) {
            $result = $response->json();
            return redirect()->back()->with('success', 'Donasi berhasil didistribusikan. Sisa: ' . $result['remaining']);
        }

        // Tangani error dari API
        $errorMessage = $response->json('error') ?? 'Gagal mendistribusikan donasi';
        return redirect()->back()->withInput()->with('error', $errorMessage);
    }

    public function updateStatusDistribusi(Request $request)
    {
        // 8. Mengupdate Status Distribusi
        // PUT /distributions.php

        $token = session('user.token');

        // Validasi input
        $validated = $request->validate([
            'distribution_id' => 'required|integer',
            'status' => 'required|string|in:diterima,diproses,dibatalkan', // sesuaikan dengan valid status
        ]);

        // Siapkan payload untuk API
        $payload = [
            'distribution_id' => $validated['distribution_id'],
            'status' => $validated['status'],
        ];

        // Kirim request ke API
        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->put($this->baseUrl . 'distributions.php', $payload);

        // Jika berhasil
        if ($response->successful()) {
            return redirect()->back()->with('success', 'Status distribusi berhasil diubah.');
        }

        // Jika gagal
        $errorMessage = $response->json('error') ?? 'Gagal mengubah status distribusi';
        return redirect()->back()->withInput()->with('error', $errorMessage);
    }
}
