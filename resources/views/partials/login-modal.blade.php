@if (Session::has('user') && Session::has('login_success'))
    @php
        $userRole = Session::get('user.role');
        $greeting = match ($userRole) {
            'admin' => 'Admin',
            'volunteer' => 'Relawan',
            'user' => 'Donatur',
            default => 'Pengguna',
        };
        $dashboardRoute = match ($userRole) {
            'admin' => route('admin-dashboard'),
            'volunteer' => route('admin-dashboard'),
            'user' => route('user-dashboard'),
            default => route('home'),
        };
    @endphp

    <!-- Login Modal -->
    <div id="login-overlay" class="fixed inset-0 z-50 flex justify-center items-center bg-black bg-opacity-50">
        <!-- Modal box -->
        <div id="login-modal" tabindex="-1" aria-hidden="false"
            class="relative bg-white rounded-lg shadow-md dark:bg-gray-700 w-full max-w-md p-6">

            <div class="flex justify-between">
                <!-- ??? -->
                <div></div>

                <!-- Modal header -->
                <div class="flex w-full text-center items-center justify-center border-b pb-4 mb-4 dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Selamat Datang {{ $greeting }}!
                    </h3>
                </div>

                <!-- Close button -->
                <button id="closeModal"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-2xl font-bold focus:outline-none mb-10 mt-0">
                    &times;
                </button>
            </div>

            <!-- Modal body -->
            <div class="space-y-4 mb-4">
                <p class="text-base text-gray-500 dark:text-gray-400">
                    Halo "{{ Session::get('user.nama_lengkap') }}" !
                </p>
                @if (in_array($userRole, ['admin', 'volunteer']))
                    <div style="text-align: justify; max-width: 800px; margin: 0 auto;"
                        class="text-base text-gray-500 dark:text-gray-400">
                        <p>Terima kasih atas dedikasi Anda sebagai <strong>{{ $greeting }}</strong> di
                            <strong>Sistem Donasi</strong> kami.
                            Peran Anda sangat penting dalam mengelola dan menyalurkan bantuan kepada yang membutuhkan.
                            Mari terus bekerja sama untuk menciptakan dampak positif dan memperluas manfaat kebaikan.
                        </p>
                    </div>
                @else
                    <div style="text-align: justify; max-width: 800px; margin: 0 auto;"
                        class="text-base text-gray-500 dark:text-gray-400">
                        <p>Terima kasih telah mengunjungi website <strong>Sistem Donasi</strong> kami.
                            Mari bersama-sama berbagi kebaikan dan membantu mereka yang membutuhkan.
                            Setiap donasi Anda sangat berarti dan membawa harapan baru.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Modal footer -->
            <div class="flex justify-between space-x-3 border-t pt-4 dark:border-gray-600">
                @if (Session::get('user.role') == 'user')
                    <button id="closeModal2"
                        class="bg-gray-200 text-gray-700 dark:text-gray-300 hover:bg-gray-300 rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 w-1/2 text-center">
                        Tutup
                    </button>
                @else
                    <a href="{{ route('home') }}"
                        class="bg-purple-700 text-white hover:bg-purple-800 rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 w-1/2 text-center">
                        Ke Halaman Utama
                    </a>
                @endif
                @if (Session::get('user.role') != 'user')
                    <button id="closeModal2"
                        class="bg-gray-200 text-gray-700 dark:text-gray-300 hover:bg-gray-300 rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 w-1/2 text-center">
                        Tutup
                    </button>
                @else
                    <a href="{{ $dashboardRoute }}"
                        class="bg-purple-700 text-white hover:bg-purple-800 rounded-lg text-sm px-5 py-2.5 dark:bg-purple-600 dark:hover:bg-purple-700 w-1/2 text-center">
                        Pergi ke Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>
    @php
        Session::forget('login_success');
    @endphp

    <!-- Modal Close Script -->
    <script>
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('login-overlay').style.display = 'none';
        });
    </script>
    <script>
        document.getElementById('closeModal2').addEventListener('click', function() {
            document.getElementById('login-overlay').style.display = 'none';
        });
    </script>
@endif
