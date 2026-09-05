@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">

        <!-- 403 Visual Illustration -->
        <div class="relative mb-6">
            <div class="text-8xl sm:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-amber-500 tracking-widest select-none">
                403
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-red-100 flex items-center justify-center transform rotate-6 hover:rotate-0 transition-transform">
                    <i class="fas fa-user-lock text-red-500 text-3xl animate-pulse"></i>
                </div>
            </div>
        </div>

        <!-- Heading & Message -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-2">
            Akses Ditolak!
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto mb-8 leading-relaxed">
            {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin atau akses untuk membuka halaman ini.' }}
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}"
               class="px-6 py-3 gradient-primary text-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            @guest
            <a href="{{ route('login') }}"
               class="px-6 py-3 bg-white border-2 border-primary-500 text-primary-500 font-bold text-xs sm:text-sm rounded-xl hover:bg-primary-50 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Login Akun
            </a>
            @endguest
        </div>

    </div>
</div>
@endsection
