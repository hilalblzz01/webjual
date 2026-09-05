@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full text-center">

        <!-- 404 Visual Illustration -->
        <div class="relative mb-6">
            <div class="text-8xl sm:text-9xl font-black text-transparent bg-clip-text gradient-primary tracking-widest select-none">
                404
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl border border-gray-100 flex items-center justify-center transform -rotate-12 hover:rotate-0 transition-transform">
                    <i class="fas fa-ghost text-primary-500 text-3xl animate-bounce"></i>
                </div>
            </div>
        </div>

        <!-- Heading & Message -->
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-2">
            Waduh! Halaman Tidak Ditemukan
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto mb-8 leading-relaxed">
            Halaman yang Anda cari mungkin sudah dihapus, diubah namanya, atau tidak tersedia untuk sementara waktu.
        </p>

        <!-- Quick Search Bar -->
        <form action="{{ route('products.index') }}" method="GET" class="max-w-md mx-auto mb-8">
            <div class="flex rounded-2xl overflow-hidden border border-gray-200 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-50 shadow-sm transition-all bg-white">
                <input type="text" name="search" placeholder="Cari produk digital favorit Anda..."
                       class="flex-1 px-4 py-3 text-xs sm:text-sm outline-none text-gray-700">
                <button type="submit" class="px-5 gradient-primary text-white font-bold text-xs sm:text-sm hover:opacity-90 transition-opacity">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}"
               class="px-6 py-3 gradient-primary text-white font-bold text-xs sm:text-sm rounded-xl shadow-md hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            <a href="{{ route('products.index') }}"
               class="px-6 py-3 bg-white border-2 border-primary-500 text-primary-500 font-bold text-xs sm:text-sm rounded-xl hover:bg-primary-50 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-th-large"></i> Lihat Katalog Produk
            </a>
        </div>

    </div>
</div>
@endsection
