<?php

namespace App\Http\Controllers\ModulTiga;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Http;

class ManajemenPenerimaDonasi extends Controller
{
    private $baseUrl = 'https://kuliah2025.my.id/modul.3_distributions/';

    public function index()
    {
        // 2.1. Mendapatkan Daftar penerima donasi
        // GET /recipients.php

        $token = session('user.token');
        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->get($this->baseUrl . 'recipients.php');

        $recipients = $response->json();
        return view('admin.recipients.index', compact('recipients'));
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
