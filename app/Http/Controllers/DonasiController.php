<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Untuk komunikasi API
use Illuminate\Support\Facades\Session; // Mengakses session

class DonasiController extends Controller
{
    protected $apiUrl; // Variabel untuk menyimpan alamat API
    
    public function __construct()
    {
        $this->apiUrl = 'https://api-laporan-dan-kegiatan-donasi.vercel.app'; // Set alamat API
    }

    // Fungsi untuk mendapatkan token dari session
    protected function getAuthToken()
    {
        return Session::get('user.token'); // Ambil token dari session
    }

    // Menampilkan daftar donasi
    public function index()
    {
        try {
            // Mengirim request ke API
            $response = Http::withToken($this->getAuthToken())
                ->get($this->apiUrl.'/data-laporan/donasi/laporan');
            
            // Jika berhasil
            if ($response->successful()) {
                $donasi = $response->json('data'); // Ambil data donasi
                return view('admin.donasi.index', ['donasi' => $donasi]);
            }
            
            // Jika gagal
            return back()->with('error', 'Gagal mengambil data');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Sesi habis, silakan login lagi');
        }
    }
}