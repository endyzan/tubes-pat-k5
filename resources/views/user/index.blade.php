@extends('admin.layouts.app') {{-- Pastikan ini mengarah ke layout admin Anda --}}

@section('content')
    {{-- Hapus div ini. Langsung masukkan konten atau include template Anda. --}}
    {{-- <div class="min-h-screen ml-47 mt-13 p-4"> --}}
        @include('user.layouts.templates') {{-- Asumsikan ini adalah 'admin.layouts.templates' bukan 'user.layouts.templates' --}}

        {{-- Script untuk set sessionStorage --}}
        @if (session('userid'))
        <script>
            sessionStorage.setItem("userid", "{{ session('userid') }}");
        </script>
        @endif
    {{-- </div> --}}
@endsection