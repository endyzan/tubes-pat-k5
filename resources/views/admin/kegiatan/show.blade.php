@extends('admin.layouts.app')

@section('content')
<div class="pt-20 px-4 mx-auto max-w-6xl">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('kegiatan.index') }}" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Detail Kegiatan</h2>
            </div>

            @if($isAdmin)
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('kegiatan.edit', $kegiatan['id']) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                    Edit
                </a>
                <form action="{{ route('kegiatan.destroy', $kegiatan['id']) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">
                        Hapus
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Status -->
        @php
            $statusColor = [
                'draft' => 'bg-yellow-100 text-yellow-800',
                'accepted' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800'
            ][$kegiatan['status']] ?? 'bg-gray-100 text-gray-800';
        @endphp
        <div class="mb-4">
            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                Status: {{ ucfirst($kegiatan['status']) }}
            </span>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom Kiri -->
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Informasi Dasar</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Judul Kegiatan</p>
                    <p class="text-gray-900 dark:text-white">{{ $kegiatan['judul'] }}</p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Lokasi</p>
                    <p class="text-gray-900 dark:text-white">{{ $kegiatan['lokasi'] ?? '-' }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Waktu Pelaksanaan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Mulai</p>
                    <p class="text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($kegiatan['tanggal_mulai'])->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}
                    </p>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tanggal Selesai</p>
                    <p class="text-gray-900 dark:text-white">
                        {{ $kegiatan['tanggal_selesai'] ? \Carbon\Carbon::parse($kegiatan['tanggal_selesai'])->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') : '-' }}
                    </p>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Deskripsi</h3>
                    <div class="text-gray-700 dark:text-gray-300 text-sm">
                        {!! nl2br(e($kegiatan['deskripsi'] ?? '-')) !!}
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Informasi Tambahan</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">ID Kegiatan: {{ $kegiatan['id'] }}</p>
                </div>
            </div>
        </div>

        <!-- Galeri -->
        @if(isset($kegiatan['images']) && count($kegiatan['images']) > 0)
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Galeri Foto</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($kegiatan['images'] as $image)
                <div class="overflow-hidden rounded border border-gray-200 dark:border-gray-600">
                    <img src="{{ $image['url'] }}" alt="Gambar Kegiatan" class="w-full h-48 object-cover">
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
