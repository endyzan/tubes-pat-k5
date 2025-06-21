<!-- Modal distribusi -->
<div id="distribute-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <!-- Konten modal -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Header modal -->
            <div
                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Distribusikan Donasi
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="distribute-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Tutup modal</span>
                </button>
            </div>

            <!-- Form modal -->
            <form method="POST" action="{{ route('dist.donasi.storeDistribution') }}" class="p-4 md:p-5">
                @csrf

                <!-- Donasi ID (hidden) -->
                <input type="hidden" name="donasi_id" value="{{ $donasi_id }}">

                <!-- Daftar penerima (dinamis/manual sesuai kebutuhan) -->
                <div id="recipients-container">
                    <div class="recipient-entry mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Penerima 1</label>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Select dropdown dari API -->
                            <select name="recipients[0][id]"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 w-full dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                required>
                                <option value="">-- Pilih Penerima --</option>
                                @foreach ($recipients as $recipient)
                                    <option value="{{ $recipient['id'] }}">{{ $recipient['nama'] }} - (ID:
                                        {{ $recipient['id'] }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="recipients[0][amount]" placeholder="Jumlah"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 w-full dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                required>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-end items-end">
                        <button type="button" id="add-recipient" class="text-sm text-green-600 hover:underline mb-4">+
                            Tambah Penerima
                        </button>
                    </div>
                    <!-- Tombol submit -->
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="me-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Distribusikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('recipients-container');
        const addBtn = document.getElementById('add-recipient');
        let index = 1;

        const recipientsData = @json($recipients);

        addBtn.addEventListener('click', function() {
            const entry = document.createElement('div');
            entry.classList.add('recipient-entry', 'mb-4');
            entry.innerHTML = `
                <div class="grid grid-cols-2 gap-2">
                    <select name="recipients[${index}][id]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 w-full dark:bg-gray-600 dark:border-gray-500 dark:text-white" required>
                        <option value="">-- Pilih Penerima --</option>
                        ${recipientsData.map(rec => `<option value="${rec.id}">${rec.nama} - ID ${rec.id}</option>`).join('')}
                    </select>
                    <input type="number" name="recipients[${index}][amount]" placeholder="Jumlah"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 w-full dark:bg-gray-600 dark:border-gray-500 dark:text-white" required>
                </div>
            `;
            container.appendChild(entry);
            index++;
        });
    });
</script>
