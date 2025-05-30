<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Donasi - Settings</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="bg-gray-100 dark:bg-gray-900 m-0 pt-4 pl-6">
        @include('partials.settings.navbar')
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
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Email
                            </label>
                            <input type="email" name="email" id="email" disabled
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ Session::get('user.email') }}">
                        </div>

                        <!-- Telepon -->
                        <div class="mb-4">
                            <label for="telp" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nomor Telepon
                            </label>
                            <input type="text" name="telp" id="telp" disabled
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="{{ Session::get('user.telp') }}">
                        </div>
                    </div>

                    <!-- Tombol Edit/Simpan & Batal -->
                    <div class="mt-4 flex gap-2">
                        <button type="button" id="toggleEdit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
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
                <form action="#">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current-password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Current
                                password</label>
                            <input type="text" name="current-password" id="current-password"
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                value="••••••••" required>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">New
                                password</label>
                            <input type="password" id="password"
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="••••••••" required>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="confirm-password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm
                                password</label>
                            <input type="text" name="confirm-password" id="confirm-password"
                                class="w-full p-2.5 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="••••••••" required>
                        </div>

                        <!-- Save Button -->
                        <div>
                            <button type="submit"
                                class="w-full py-2.5 px-5 text-sm font-medium text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 rounded-lg dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                Save New Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        const toggleButton = document.getElementById("toggleEdit");
        const cancelButton = document.getElementById("cancelEdit");
        const inputs = document.querySelectorAll("#userForm input");

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
                // Di sini bisa ditambahkan logika simpan (form.submit atau fetch/AJAX)
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
