@extends('admin.layouts.app')

@section('content')
    <div class="container">
        @include('admin.recipients.components.alert')
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">Daftar Donasi</h2>
        <form method="GET" class="mb-4">
            <select name="status" onchange="this.form.submit()"
                class="border rounded px-3 py-1 text-sm dark:bg-gray-700 dark:text-white">
                <option value="">-- Semua Status --</option>
                @foreach (['need_validation', 'pending', 'success', 'failed', 'taken'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Jenis</th>
                        <th scope="col" class="px-6 py-3">Jumlah</th>
                        <th scope="col" class="px-6 py-3">Keterangan</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Waktu</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donasi as $d)
                        <tr
                            class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ ucfirst($d['type']) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $d['qty'] }} {{ $d['unit'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $d['keterangan'] }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $status = $d['status_validasi'] ?? 'None';
                                    $statusClass = match ($status) {
                                        'success' => 'bg-green-600',
                                        'pending' => 'bg-yellow-500',
                                        'need_validation' => 'bg-blue-500',
                                        'failed' => 'bg-red-600',
                                        'taken' => 'bg-gray-500',
                                        default => 'bg-slate-400',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-white text-xs {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($d['created_at'])->format('d M Y H:i') }}
                            </td>
                            <td>
                                <a href="{{ route('dist.donasi.getDistribution', ['id' => $d['id']]) }}"
                                    class="sm:text-[10px] text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-3 py-1.5 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                                    Detail Distribusi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-300">
                                Tidak ada data donasi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


        </div>
        @if (!empty($pagination))
            <div class="mt-4 flex justify-center">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Halaman {{ $pagination['current_page'] }} dari {{ $pagination['total_pages'] }} | Total:
                    {{ $pagination['total_items'] }}
                </p>
            </div>
            <div class="mt-2 flex justify-center">
                <nav>
                    <ul class="inline-flex -space-x-px">
                        @for ($i = 1; $i <= $pagination['total_pages']; $i++)
                            <li>
                                <a href="{{ route('dist.donasi.index', ['page' => $i, 'status' => request('status')]) }}"
                                    class="px-3 py-2 border border-gray-300 text-gray-500 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 {{ $pagination['current_page'] == $i ? 'bg-blue-500 text-white' : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            </div>
        @endif


    </div>
@endsection
