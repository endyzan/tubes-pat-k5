<?php

namespace App\Http\Controllers\ModulTiga;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Http;

class ManajemenPenerimaDonasi extends Controller
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_MODULTIGA_URL');
    }
    public function index(Request $request)
    {
        $token = session('user.token');

        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $statusFilter = $request->query('status');

        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->get($this->baseUrl . 'recipients.php');

        $allRecipients = $response->json();

        // Filter berdasarkan status jika ada
        if ($statusFilter === 'aktif') {
            $allRecipients = array_filter($allRecipients, fn($r) => $r['is_active'] == true);
        } elseif ($statusFilter === 'nonaktif') {
            $allRecipients = array_filter($allRecipients, fn($r) => $r['is_active'] == false);
        }

        $totalItems = count($allRecipients);
        $totalPages = ceil($totalItems / $limit);
        $offset = ($page - 1) * $limit;
        $recipients = array_slice($allRecipients, $offset, $limit);

        $pagination = [
            'current_page' => (int) $page,
            'total_pages' => $totalPages,
            'total_items' => $totalItems,
            'limit' => $limit,
            'status' => $statusFilter,
        ];

        return view('admin.recipients.index', compact('recipients', 'pagination'));
    }



    public function store(Request $request)
    {

        // 1. Menambahkan penerima donasi
        // POST /recipients.php

        $token = session('user.token');
        $data = $request->validate([
            'nama' => 'required',
            'alamat' => 'required'
        ]);

        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->post($this->baseUrl . 'recipients.php', $data);

        if ($response->successful()) {
            return redirect()->route('recipients.index')->with('success', 'Penerima berhasil ditambahkan');
        }
        $errorMessage = $response->json('error') ?? 'Gagal menambahkan penerima';

        return back()
            ->withInput()
            ->with('error', $errorMessage);
    }

    public function update(Request $request, $id)
    {
        // 3. Mengupdate daftar penerima donasi
        // PUT /recipients.php?id=id

        $token = session('user.token');

        // Validasi input lokal (Laravel)
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string'
        ]);

        // Kirim ke API eksternal
        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->put($this->baseUrl . "recipients.php?id={$id}", $data);

        // Jika berhasil
        if ($response->successful()) {
            return redirect()->route('recipients.index')
                ->with('success', 'Penerima berhasil diperbarui');
        }

        // Jika gagal, ambil pesan error dari response JSON
        $errorMessage = $response->json('error') ?? 'Gagal memperbarui penerima';

        return back()
            ->withInput()
            ->with('error', $errorMessage);
    }

    public function destroy($id)
    {
        // 4. Menonaktifkan penerima donasi
        // DELETE /recipients.php?id=id 

        $token = session('user.token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])
            ->withoutVerifying()
            ->delete($this->baseUrl . 'recipients.php?id=' . $id);

        if ($response->successful()) {
            return redirect()->route('recipients.index')->with('success', 'Penerima berhasil dinonaktifkan');
        }

        // Coba ambil pesan error jika ada dari API
        $errorMessage = $response->json('error') ?? 'Gagal menonaktifkan penerima';

        return redirect()->route('recipients.index')->with('error', $errorMessage);
    }
    public function activate($id)
    {
        // 5. Mengaktifkan kembali penerima donasi
        // PATCH /recipients.php?id=id      ?? How To Get The List ??

        $token = session('user.token');

        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->patch($this->baseUrl . "recipients.php?id=" . $id);

        if ($response->successful()) {
            return redirect()->route('recipients.index')->with('success', 'Penerima berhasil diaktifkan kembali');
        }

        $errorMsg = $response->json('error') ?? 'Gagal mengaktifkan penerima';
        return back()->with('error', $errorMsg);
    }
}
