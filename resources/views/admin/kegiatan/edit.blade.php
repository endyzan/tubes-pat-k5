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
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Kegiatan Sosial</h2>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('kegiatan.update', $kegiatan['id']) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label for="judul" class="block mb-1 text-sm font-medium text-gray-700 dark:text-white">Judul Kegiatan</label>
                    <input type="text" id="judul" name="judul" value="{{ $kegiatan['judul'] }}" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5">
                </div>

                <!-- Lokasi -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label for="lokasi" class="block mb-1 text-sm font-medium text-gray-700 dark:text-white">Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi" value="{{ $kegiatan['lokasi'] }}" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5">
                </div>

                <!-- Tanggal Mulai -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label for="tanggal_mulai" class="block mb-1 text-sm font-medium text-gray-700 dark:text-white">Tanggal Mulai</label>
                    <input type="datetime-local" id="tanggal_mulai" name="tanggal_mulai"
                        value="{{ \Carbon\Carbon::parse($kegiatan['tanggal_mulai'])->format('Y-m-d\TH:i') }}" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5">
                </div>

                <!-- Tanggal Selesai -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label for="tanggal_selesai" class="block mb-1 text-sm font-medium text-gray-700 dark:text-white">Tanggal Selesai (Opsional)</label>
                    <input type="datetime-local" id="tanggal_selesai" name="tanggal_selesai"
                        value="{{ $kegiatan['tanggal_selesai'] ? \Carbon\Carbon::parse($kegiatan['tanggal_selesai'])->format('Y-m-d\TH:i') : '' }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5">
                </div>

                <!-- Status -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                    <label for="status" class="block mb-1 text-sm font-medium text-gray-700 dark:text-white">Status</label>
                    <select id="status" name="status" required
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5">
                        <option value="draft" {{ $kegiatan['status'] === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="accepted" {{ $kegiatan['status'] === 'accepted' ? 'selected' : '' }}>Selesai</option>
                        <option value="rejected" {{ $kegiatan['status'] === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded">
                <label for="deskripsi" class="block mb-1 text-sm font-medium text-gray-700 dark:text-white">Deskripsi Kegiatan</label>
                <textarea id="deskripsi" name="deskripsi" rows="5"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5">{{ $kegiatan['deskripsi'] }}</textarea>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-2">
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
