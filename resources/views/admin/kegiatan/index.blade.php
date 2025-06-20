@extends('admin.layouts.app')

@section('content')
<div class="pt-20 px-4 mx-auto max-w-6xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Daftar Kegiatan Sosial</h2>
            @if($isAdmin)
            <a href="{{ route('kegiatan.create') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                Tambah Kegiatan
            </a>
            @endif
        </div>

        <!-- Notifikasi -->
        @if(session('success'))
            <div class="p-4 mb-6 text-sm text-green-800 bg-green-50 border border-green-200 rounded dark:bg-gray-700 dark:border-green-500 dark:text-green-400" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..."
                    class="text-sm rounded-md border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white px-3 py-2 focus:ring focus:ring-blue-300 focus:outline-none">
                <select name="status"
                    class="text-sm rounded-md border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white px-3 py-2 focus:ring focus:ring-blue-300 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <button type="submit"
                    class="bg-blue-600 text-white text-sm px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Filter
                </button>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Judul</th>
                        <th class="px-6 py-3">Lokasi</th>
                        <th class="px-6 py-3">Tanggal Mulai</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatan as $item)
                    @php
                        $statusColor = [
                            'draft' => 'bg-yellow-100 text-yellow-800',
                            'accepted' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800'
                        ][$item['status']] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $item['judul'] }}
                        </td>
                        <td class="px-6 py-4">{{ $item['lokasi'] ?? '-' }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item['tanggal_mulai'])->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                {{ ucfirst($item['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-2 whitespace-nowrap">
                            <a href="{{ route('kegiatan.show', $item['id']) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Detail</a>
                            <a href="{{ route('kegiatan.edit', $item['id']) }}" class="text-green-600 dark:text-green-400 hover:underline text-sm">Edit</a>
                            @if($isAdmin)
                            <form action="{{ route('kegiatan.destroy', $item['id']) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">Tidak ada data kegiatan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($pagination) && $pagination['total'] > 0)
        <div class="mt-6 text-sm text-gray-600 dark:text-gray-300 text-center">
            Menampilkan <span class="font-semibold">{{ count($kegiatan) }}</span> dari <span class="font-semibold">{{ $pagination['total'] }}</span> data
        </div>
        @endif

    </div>
</div>
@endsection
