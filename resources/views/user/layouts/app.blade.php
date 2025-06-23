<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sistem Donasi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


    @include('partials.vite')
    
</head>

{{-- Ubah warna background body dan warna teks --}}
<body class="min-h-screen bg-white text-gray-900">
    @include('user.layouts.navbar')

    {{-- Ubah warna background container utama --}}
    <div class="flex bg-white">
        @include('user.layouts.sidebar')

        {{-- Pastikan ini mengelola padding dan margin yang benar --}}
        <div class="flex-1 lg:ml-64 p-4 pt-20">
        @yield('content')
        </div>
    </div>
    {{-- flowbite script js --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    {{-- Sidebar toggle script --}}
    <script>
        @if (session()->has('user'))
            localStorage.setItem("token", "{{ session('user')['token'] }}");
        @endif
        fetch('/api-proxy/donasi/status/accepted')
            .then(res => res.json())
            .then(result => {
                console.log(result);

                // Jika result adalah array langsung
                if (Array.isArray(result)) {
                if (result.length === 0) {
                    document.getElementById('donasi-list').innerHTML = 'Belum ada donasi.';
                } else {
                    let html = '';
                    result.forEach(donasi => {
                    html += `<li>${donasi.nama_donatur} - Rp${donasi.jumlah}</li>`; // Perbaiki string literal
                    });
                    document.getElementById('donasi-list').innerHTML = html;
                }
                } else {
                document.getElementById('donasi-list').innerHTML = 'Data donasi tidak valid';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('donasi-list').innerHTML = 'Gagal memuat data';
            });


        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleSidebarMobile');
            const sidebar = document.getElementById('sidebar');
            const hamburger = document.getElementById('toggleSidebarMobileHamburger');
            const close = document.getElementById('toggleSidebarMobileClose');

            toggleButton.addEventListener('click', function() {
                const isHidden = sidebar.classList.contains('hidden');

                if (isHidden) {
                    sidebar.classList.remove('hidden');
                    setTimeout(() => {
                        sidebar.classList.remove('-translate-x-full');
                    }, 10); // trigger transition
                } else {
                    sidebar.classList.add('-translate-x-full');
                    setTimeout(() => {
                        sidebar.classList.add('hidden');
                    }, 300); // match with transition duration
                }

                hamburger.classList.toggle('hidden');
                close.classList.toggle('hidden');
            });
        });
    </script>


</body>

</html>