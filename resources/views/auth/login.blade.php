@include('auth.partials.head')


<body>
    @include('partials.navbar')

    {{-- alert --}}
    @include('partials.autentication-alert')

    @include('auth.partials.form_login')

    {{-- flowbite script js --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

@include('partials.footer')
