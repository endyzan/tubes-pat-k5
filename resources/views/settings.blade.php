<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Donasi - Settings</title>
    @include('partials.vite')
</head>

<body>
    <div class="bg-gray-100 dark:bg-gray-900 m-0 pt-4 pl-6">
        @include('partials.settings.navbar')
    </div>
    <div class="bg-gray-100 dark:bg-gray-900 pt-2">
        {{-- alert --}}
        @include('partials.settings.error-alert')
    </div>
    <div class="flex justify-center items-center min-h-screen bg-gray-100 dark:bg-gray-900">
        <div class="w-full max-w-2xl px-5">
            <div
                class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">General information</h3>

                <form id="userForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nama Lengkap -->
                        <div>
                            <label for="nama_lengkap"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nama Lengkap
                            </label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" disabled
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ Session::get('user.nama_lengkap') }}">
                            @error('nama_lengkap')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Email
                            </label>
                            <input type="email" name="email" id="email" disabled
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ Session::get('user.email') }}">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                        </div>

                        <!-- Telepon -->
                        <div class="mb-4">
                            <label for="telp" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nomor Telepon
                            </label>
                            <input type="text" name="telp" id="telp" disabled
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ Session::get('user.telp') }}">
                            @error('telp')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                        </div>
                    </div>

                    <!-- Tombol Edit/Simpan & Batal -->
                    <div class="mt-4 flex gap-2">
                        <button type="button" id="toggleEdit"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-800 hover:bg-purple-600">
                            Edit
                        </button>
                        <button type="button" id="cancelEdit"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded hover:bg-gray-600 hidden">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
            <!-- Form Password -->
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-xl font-semibold dark:text-white">Password information</h3>

                <form method="POST" action="{{ route('profile.update.password') }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-6">

                        <!-- Password Lama -->
                        <div>
                            <label for="old_password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Current password
                            </label>
                            <input type="password" name="old_password" id="old_password"
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="••••" required>
                            @error('old_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password Baru -->
                        <div>
                            <label for="new_password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                New password
                            </label>
                            <input type="password" name="new_password" id="new_password"
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="••••" required>
                            @error('new_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Error Umum -->
                        @error('update_password')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror

                        <!-- Tombol Simpan -->
                        <div>
                            <button type="submit"
                                class="w-full py-2.5 px-5 text-sm font-medium text-white bg-purple-800 hover:bg-purple-600 focus:ring-4 focus:ring-primary-300 rounded-lg dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                Save New Password
                            </button>
                        </div>
                        <div class="flex justify-end items-end p-0 m-0 w-full"> <a
                                href="{{ route('forgot-password') }}"
                                class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500 dark:text-white">Forgot
                                password?</a>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script>
        const toggleButton = document.getElementById("toggleEdit");
        const cancelButton = document.getElementById("cancelEdit");
        const inputs = document.querySelectorAll("#userForm input");
        const form = document.getElementById("userForm");


        // Simpan nilai awal input
        const originalValues = {};
        inputs.forEach(input => {
            originalValues[input.id] = input.value;
        });

        let isEditing = false;

        toggleButton.addEventListener("click", () => {
            isEditing = !isEditing;

            if (isEditing) {
                inputs.forEach(input => input.disabled = false);
                toggleButton.textContent = "Simpan";
                cancelButton.classList.remove("hidden");
            } else {
                form.submit();

                inputs.forEach(input => input.disabled = true);
                toggleButton.textContent = "Edit";
                cancelButton.classList.add("hidden");
            }
        });

        cancelButton.addEventListener("click", () => {
            // Reset ke nilai awal
            inputs.forEach(input => {
                input.value = originalValues[input.id];
                input.disabled = true;
            });

            isEditing = false;
            toggleButton.textContent = "Edit";
            cancelButton.classList.add("hidden");
        });
    </script>
</body>


</html>
