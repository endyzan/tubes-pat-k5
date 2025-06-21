@extends('admin.layouts.app')

@section('content')
    <div class="container">
        @include('admin.recipients.components.alert')
        <!-- Header & Ringkasan -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    Distribusi Donasi <span class="text-base font-normal text-gray-500">(ID: {{ $donasi_id }})</span>
                </h2>
                <a href="{{ route('dist.donasi.index') }}"
                    class="text-sm text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                    &larr; Kembali
                </a>
            </div>
            <div class="flex gap-2 text-sm">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded dark:bg-blue-900 dark:text-blue-300">
                    Total Donasi: {{ number_format($distributions['total_donasi'] ?? 0) }}
                </span>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded dark:bg-green-900 dark:text-green-300">
                    Terdistribusi: {{ number_format($distributions['total_distributed'] ?? 0) }}
                </span>
                @php
                    $remaining = $distributions['remaining'] ?? 0;
                @endphp
                <span
                    class="px-3 py-1 rounded {{ $remaining < 0 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                    Sisa: {{ number_format($remaining) }}
                </span>
            </div>
        </div>
        <!-- Tombol buka modal -->
        <button data-modal-target="distribute-modal" data-modal-toggle="distribute-modal"
            class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 my-2 mb-10"
            type="button">
            Distribusikan Donasi
        </button>
        @include('admin.distribusi.components.modal-form-distribusi-donasi')

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Penerima</th>
                        <th scope="col" class="px-6 py-3">Jumlah</th>
                        <th scope="col" class="px-6 py-3">Satuan</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($distributions['distributions']) && count($distributions['distributions']) > 0)
                        @foreach ($distributions['distributions'] as $d)
                            <tr
                                class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $d['nama'] }}
                                </td>
                                <td class="px-6 py-4">{{ $d['amount_received'] }}</td>
                                <td class="px-6 py-4">{{ $d['unit'] }}</td>
                                <td class="px-6 py-4 capitalize">{{ $d['status'] }}</td>
                                <td class="px-6 py-4">
                                    @if ($d['status'] !== 'diterima')
                                        @include('admin.distribusi.components.modal-edit-status-dist', [
                                            'd' => $d,
                                        ])
                                    @else
                                        <span class="text-green-600 font-semibold">✓ Diterima</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="bg-white dark:bg-gray-900 border-b dark:border-gray-700">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Data distribusi belum tersedia.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>
@endsection
