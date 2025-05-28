<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sistem Donasi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen">

    @include('admin.layouts.navbar')
    <div class="flex overflow-hidden bg-gray-500 dark:bg-gray-900">
        @include('admin.layouts.sidebar')
        <div class="lg:max-w-[85vw] lg:ml-auto">
            {{-- content --}}
            @yield('content')
        </div>
    </div>
    {{-- flowbite script js --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

    {{-- Sidebar toggle script --}}
    <script>
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
