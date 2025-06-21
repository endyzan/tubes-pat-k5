@extends('admin.layouts.app')

@section('content')
    <div class="container">
        @include('admin.recipients.components.alert')
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">Daftar Penerima Donasi</h2>

        <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
            class="my-4 block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">
            Tambah Penerima Donasi
        </button>
        @include('admin.recipients.components.modal-create-recipt')

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Nama</th>
                        <th scope="col" class="px-6 py-3">Alamat</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recipients as $r)
                        <tr
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $r['id'] }}</td>
                            <td class="px-6 py-4">{{ $r['nama'] }}</td>
                            <td class="px-6 py-4">{{ $r['alamat'] }}</td>
                            <td class="px-6 py-4">{{ $r['is_active'] ? 'Aktif' : 'Nonaktif' }}</td>
                            <td class="flex gap-4 pt-3">
                                <button data-modal-target="edit-modal-{{ $r['id'] }}"
                                    data-modal-toggle="edit-modal-{{ $r['id'] }}"
                                    class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-3 py-1.5 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                                    Edit
                                </button>
                                <button data-modal-target="confirm-delete-{{ $r['id'] }}"
                                    data-modal-toggle="confirm-delete-{{ $r['id'] }}"
                                    class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800"
                                    type="button">
                                    <svg class="w-[20px] h-[20px] text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                </button>
                                @include('admin.recipients.components.modal-edit-recipt', [
                                    'recipient' => $r,
                                ])
                                @include('admin.recipients.components.modal-delete-recipt', [
                                    'recipient' => $r,
                                ])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection
