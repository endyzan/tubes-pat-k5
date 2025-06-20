<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class KegiatanController extends Controller
{
    protected $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = config('services.donation_api.url');
    }

    /**
     * Get authenticated user token from session
     */
    protected function getAuthToken()
    {
        return Session::get('user.token');
    }

    /**
     * Check if current user is admin
     */
    protected function isAdmin()
    {
        return Session::get('user.role') === 'admin';
    }

    /**
     * Menampilkan daftar kegiatan
     */
    public function index(Request $request)
    {
        try {
            $token = $this->getAuthToken();
            
            $response = Http::withToken($token)
                ->get($this->apiUrl.'/data-kegiatan-sosial', [
                    'page' => $request->get('page', 1),
                    'limit_per_page' => $request->get('limit', 10)
                ]);

            if ($response->successful()) {
                $kegiatan = $response->json('data');
                $pagination = [
                    'current_page' => $response->json('page', 1),
                    'total' => $response->json('total', count($kegiatan))
                ];
            } else {
                $errorMessage = $response->json('message', 'Gagal mengambil data kegiatan');
                return redirect()->route('login')->with('error', $errorMessage);
            }

            return view('admin.kegiatan.index', [
                'kegiatan' => $kegiatan,
                'pagination' => $pagination,
                'isAdmin' => $this->isAdmin()
            ]);

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Session expired, please login again');
        }
    }

    /**
     * Menampilkan form tambah kegiatan
     */
    public function create()
    {
        if (!$this->isAdmin()) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Hanya admin yang bisa menambah kegiatan');
        }
        
        return view('admin.kegiatan.create');
    }

    /**
     * Menyimpan kegiatan baru
     */
    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Hanya admin yang bisa menambah kegiatan');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
        ]);

        try {
            $response = Http::withToken($this->getAuthToken())
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post($this->apiUrl.'/data-kegiatan-sosial', [
                    'judul' => $request->judul,
                    'deskripsi' => $request->deskripsi,
                    'lokasi' => $request->lokasi,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'status' => 'draft'
                ]);

            if ($response->successful()) {
                return redirect()->route('kegiatan.index')
                    ->with('success', 'Kegiatan berhasil ditambahkan');
            } else {
                $errorMessage = $response->json('message', 'Gagal menambahkan kegiatan');
                return back()->withInput()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan detail kegiatan
     */
    public function show($id)
    {
        try {
            $response = Http::withToken($this->getAuthToken())
                ->get($this->apiUrl.'/data-kegiatan-sosial?id='.$id);

            if ($response->successful()) {
                $kegiatan = $response->json('data');
                return view('admin.kegiatan.show', [
                    'kegiatan' => $kegiatan,
                    'isAdmin' => $this->isAdmin()
                ]);
            } else {
                $errorMessage = $response->json('message', 'Data tidak ditemukan');
                return redirect()->route('kegiatan.index')->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan form edit
     */
    public function edit($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Hanya admin yang bisa mengedit kegiatan');
        }

        try {
            $response = Http::withToken($this->getAuthToken())
                ->get($this->apiUrl.'/data-kegiatan-sosial?id='.$id);

            if ($response->successful()) {
                $kegiatan = $response->json('data');
                return view('admin.kegiatan.edit', compact('kegiatan'));
            } else {
                $errorMessage = $response->json('message', 'Data tidak ditemukan');
                return redirect()->route('kegiatan.index')->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Update kegiatan
    */
    public function update(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Hanya admin yang bisa mengupdate kegiatan');
        }

        $request->validate([
            'judul' => 'required|string|max:191',
            'deskripsi' => 'nullable|string|max:191',
            'lokasi' => 'nullable|string|max:191',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'status' => 'required|in:draft,accepted,rejected'
        ]);

        try {
            $data = [
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'lokasi' => $request->lokasi,
                'tanggal_mulai' => $this->formatDateOnly($request->tanggal_mulai),
                'tanggal_selesai' => $request->tanggal_selesai ? $this->formatDateOnly($request->tanggal_selesai) : null,
                'status' => $request->status
            ];

            $response = Http::withToken($this->getAuthToken())
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->put($this->apiUrl.'/data-kegiatan-sosial?id='.$id, $data);

            if ($response->successful()) {
                return redirect()->route('kegiatan.index')
                    ->with('success', 'Kegiatan berhasil diperbarui');
            } else {
                $errorMessage = $response->json('message', 'Gagal memperbarui kegiatan');
                $errors = $response->json('errors', []);
                return back()->withInput()
                    ->with('error', $errorMessage)
                    ->with('errors', $errors);
            }

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
    protected function formatDateOnly($dateString)
    {
        return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
    }

    /**
     * Hapus kegiatan
     */
    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Hanya admin yang bisa menghapus kegiatan');
        }

        try {
            $response = Http::withToken($this->getAuthToken())
                ->delete($this->apiUrl.'/data-kegiatan-sosial?id='.$id);

            if ($response->successful()) {
                return redirect()->route('kegiatan.index')
                    ->with('success', 'Kegiatan berhasil dihapus');
            } else {
                $errorMessage = $response->json('message', 'Gagal menghapus kegiatan');
                return back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}