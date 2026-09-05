@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">

        <!-- 500 Visual Illustration -->
        <div class="relative mb-6">
            <div class="text-8xl sm:text-9xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 tracking-widest select-none">
                500
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-red-100 flex items-center justify-center">
                    <i class="fas fa-tools text-red-500 text-3xl animate-spin" style="animation-duration: 4s;"></i>
                </div>
            </div>
        </div>

        <!-- Heading & Message -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-2">
            Terjadi Kesalahan Server!
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto mb-8 leading-relaxed">
            Sistem kami sedang mengalami kendala teknis sementara. Tim kami akan segera menanganinya.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.location.reload()"
                    class="px-6 py-3 gradient-primary text-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                <i class="fas fa-redo"></i> Muat Ulang Halaman
            </button>
            <a href="{{ route('home') }}"
               class="px-6 py-3 bg-white border-2 border-primary-500 text-primary-500 font-bold text-xs sm:text-sm rounded-xl hover:bg-primary-50 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
        </div>

    </div>
</div>
@endsection
