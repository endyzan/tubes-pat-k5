@extends('admin.layouts.app')

@section('content')
    @include('partials.login-modal')
    <div class="min-h-screen mt-10 ml-15 p-4">
        @include('admin.layouts.dashboard')
    {{-- </div> --}}
@endsection
