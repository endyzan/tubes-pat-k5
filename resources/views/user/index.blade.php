@extends('user.layouts.app')

@section('content')
    <div class="min-h-screen ml-47 mt-13 p-4">
        @include('user.layouts.templates')

        {{-- Script untuk set sessionStorage --}}
        @if (session('userid'))
        <script>
            sessionStorage.setItem("userid", "{{ session('userid') }}");
        </script>
        @endif
    </div>
@endsection
