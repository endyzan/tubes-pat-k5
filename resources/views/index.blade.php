@extends('main-layout')

@section('content')

    @include('partials.navbar')

    {{-- alert --}}
    @include('partials.autentication-alert')

    @include('partials.login-modal')

    <!-- Start Donation Hero Section -->
    @include('partials.beranda.banner')
    <!-- End Donation Hero Section -->

    <!-- Sponsorship Section -->
    {{-- @include('partials.beranda.sponsorship') --}}

    <!-- Donation Features Section -->
    @include('partials.beranda.donation-features')

    <!-- About Us Section -->
    @include('partials.beranda.aboutus')

    <!-- Statistic and Testimonial Section -->
    @include('partials.beranda.testimonial-statistic')

    <!-- Quotes Section -->
    @include('partials.beranda.quotes')




    <!-- Start block -->
    <section class="bg-white dark:bg-gray-900 pb-10">
        <div class="max-w-screen-xl px-4 py-8 mx-auto lg:py-16 lg:px-6">
            <div class="max-w-screen-sm mx-auto text-center">
                <img src="{{ asset('image/logo/kardus_donasi.png') }}" class="h-60 mx-auto my-0" alt="Donation Box" />
                <h2 class="mb-2 text-3xl font-extrabold leading-tight tracking-tight text-gray-900 dark:text-white">
                    Mari Berdonasi Hari Ini
                </h2>
                <p class="mb-6 font-light text-gray-500 dark:text-gray-400 md:text-lg">
                    Satu langkah kecil darimu bisa menjadi perubahan besar bagi mereka yang membutuhkan.
                </p>
                <a href="{{ route('user-dashboard') }}"
                    class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-5 dark:bg-purple-600 dark:hover:bg-purple-700 focus:outline-none dark:focus:ring-purple-800">
                    Donasi Sekarang
                </a>
            </div>
        </div>
    </section>


    <!-- Contact Us Section -->
    {{-- @include('partials.beranda.contactus') --}}
