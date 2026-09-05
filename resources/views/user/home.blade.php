@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="gradient-primary text-white py-10 sm:py-16 px-4">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-6 md:gap-10">
        <div class="flex-1 text-center md:text-left">
            <span class="inline-block bg-white/20 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full mb-3">
                ⚡ Auto Delivery Digital Akun Instant 24/7
            </span>
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-extrabold leading-tight mb-3">
                Belanja Akun Digital <br class="hidden sm:inline">
                <span class="text-yellow-300">Garansi Full & Instant!</span>
            </h1>
            <p class="text-white/90 text-xs sm:text-base mb-6 max-w-xl mx-auto md:mx-0">Dapatkan kredensial akun digital legal, lisensi software, dan langganan premium secara otomatis detik ini juga.</p>
            <div class="flex gap-3 justify-center md:justify-start flex-wrap">
                <a href="{{ route('products.index') }}"
                   class="px-5 py-2.5 sm:px-6 sm:py-3 bg-white text-primary-500 font-bold text-xs sm:text-sm rounded-xl hover:bg-yellow-50 transition-all shadow-md">
                    Lihat Katalog Produk <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @guest
                <a href="{{ route('auth.google') }}"
                   class="px-5 py-2.5 sm:px-6 sm:py-3 border-2 border-white text-white font-semibold text-xs sm:text-sm rounded-xl hover:bg-white/10 transition-all">
                    <i class="fab fa-google mr-1.5"></i>Login Google
                </a>
                @endguest
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-2 sm:gap-6 mt-8 max-w-md mx-auto md:mx-0 pt-6 border-t border-white/20 text-center">
                <div>
                    <p class="text-lg sm:text-2xl font-bold">100%</p>
                    <p class="text-white/80 text-[10px] sm:text-xs">Garansi Produk</p>
                </div>
                <div>
                    <p class="text-lg sm:text-2xl font-bold">Instant</p>
                    <p class="text-white/80 text-[10px] sm:text-xs">Kirim Otomatis</p>
                </div>
                <div>
                    <p class="text-lg sm:text-2xl font-bold">4.9 ⭐</p>
                    <p class="text-white/80 text-[10px] sm:text-xs">Rating Toko</p>
                </div>
            </div>
        </div>

        <div class="flex-1 flex justify-center mt-4 md:mt-0">
            <div class="relative w-52 h-52 sm:w-72 sm:h-72">
                <div class="absolute inset-0 bg-white/20 rounded-full animate-pulse"></div>
                <div class="absolute inset-6 bg-white/30 rounded-full flex items-center justify-center shadow-2xl">
                    <i class="fas fa-bolt text-yellow-300 text-6xl sm:text-8xl drop-shadow-md"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Bar -->
<section class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-3 sm:py-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="flex items-center gap-2.5 text-xs sm:text-sm">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-bolt text-primary-500 text-xs sm:text-sm"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800">Auto Delivery</p>
                    <p class="text-gray-500 text-[10px] sm:text-xs">Langsung di halaman order</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 text-xs sm:text-sm">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-qrcode text-blue-500 text-xs sm:text-sm"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800">QRIS All E-Wallet</p>
                    <p class="text-gray-500 text-[10px] sm:text-xs">DANA, GoPay, OVO, Bank</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 text-xs sm:text-sm">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-shield-alt text-green-500 text-xs sm:text-sm"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800">100% Garansi</p>
                    <p class="text-gray-500 text-[10px] sm:text-xs">Full support jika terkendala</p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 text-xs sm:text-sm">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-comments text-purple-500 text-xs sm:text-sm"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800">Live Chat Admin</p>
                    <p class="text-gray-500 text-[10px] sm:text-xs">Respon cepat via web</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
@if($categories->count() > 0)
<section class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Kategori Populer</h2>
            <a href="{{ route('products.index') }}" class="text-primary-500 text-sm font-medium hover:underline">Lihat Semua</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
               class="flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-14 h-14 gradient-primary rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-tags text-white text-xl"></i>
                </div>
                <p class="text-sm font-medium text-gray-700 text-center leading-tight">{{ $category->name }}</p>
                <p class="text-xs text-gray-400">{{ $category->products_count }} produk</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-10 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">🔥 Produk Terlaris</h2>
                <p class="text-gray-500 text-sm">Paling banyak dibeli minggu ini</p>
            </div>
            <a href="{{ route('products.index') }}?sort=popular" class="text-primary-500 text-sm font-medium hover:underline">Lihat Semua</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- New Products -->
@if($newProducts->count() > 0)
<section class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">✨ Produk Terbaru</h2>
                <p class="text-gray-500 text-sm">Baru saja hadir untuk Anda</p>
            </div>
            <a href="{{ route('products.index') }}?sort=latest" class="text-primary-500 text-sm font-medium hover:underline">Lihat Semua</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
            @foreach($newProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
