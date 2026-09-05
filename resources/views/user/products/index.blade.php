@extends('layouts.app')

@section('title', 'Semua Produk Digital')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-8" x-data="{ mobileFilterOpen: false }">

    <!-- Breadcrumb -->
    <nav class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary-500">Beranda</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Semua Produk Digital</span>
    </nav>

    <div class="flex flex-col md:flex-row gap-4 sm:gap-6">

        <!-- Sidebar Filter (Desktop) -->
        <aside class="hidden md:block w-60 flex-shrink-0">
            <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20 shadow-sm">
                    <h3 class="font-bold text-gray-800 mb-4 text-sm"><i class="fas fa-filter text-primary-500 mr-2"></i>Filter Produk</h3>

                    <!-- Category -->
                    <div class="mb-5">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kategori</p>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="text-primary-500" onchange="this.form.submit()">
                                <span class="text-gray-700">Semua Kategori</span>
                            </label>
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer text-xs sm:text-sm">
                                <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} class="text-primary-500" onchange="this.form.submit()">
                                <span class="text-gray-700">{{ $cat->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-5">
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Range Harga (Rp)</p>
                        <div class="space-y-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="Harga Min"
                                   class="w-full px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="Harga Max"
                                   class="w-full px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                        </div>
                    </div>

                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <button type="submit" class="w-full gradient-primary text-white py-2.5 rounded-xl text-xs font-bold hover:opacity-90 transition-opacity shadow-sm">
                        Terapkan Filter
                    </button>

                    @if(request()->hasAny(['category', 'min_price', 'max_price']))
                    <a href="{{ route('products.index') }}" class="block mt-2 text-center text-xs text-red-500 font-medium hover:underline">
                        Reset Filter
                    </a>
                    @endif
                </div>
            </form>
        </aside>

        <!-- Mobile Filter Modal -->
        <div x-show="mobileFilterOpen" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 md:hidden" style="display: none;">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="mobileFilterOpen = false"></div>
            <div class="relative bg-white w-full max-w-lg rounded-t-2xl sm:rounded-2xl p-5 z-10 max-h-[85vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-800 text-base"><i class="fas fa-filter text-primary-500 mr-2"></i>Filter Produk</h3>
                    <button @click="mobileFilterOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
                </div>
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kategori</p>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-xl cursor-pointer text-xs">
                                    <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} class="text-primary-500">
                                    <span>Semua</span>
                                </label>
                                @foreach($categories as $cat)
                                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-xl cursor-pointer text-xs">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} class="text-primary-500">
                                    <span class="truncate">{{ $cat->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Range Harga (Rp)</p>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full p-2.5 text-xs border border-gray-200 rounded-xl">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full p-2.5 text-xs border border-gray-200 rounded-xl">
                            </div>
                        </div>

                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 gradient-primary text-white py-3 rounded-xl text-xs font-bold shadow-md">
                                Terapkan
                            </button>
                            <a href="{{ route('products.index') }}" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl text-xs font-bold">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid & Toolbar -->
        <div class="flex-1">

            <!-- Toolbar -->
            <div class="flex items-center justify-between mb-4 bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-gray-100 gap-2">
                <p class="text-xs sm:text-sm text-gray-600">
                    Total: <span class="font-bold text-gray-800">{{ $products->total() }}</span> produk
                </p>

                <div class="flex items-center gap-2">
                    <button @click="mobileFilterOpen = true" class="md:hidden px-3 py-1.5 bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold flex items-center gap-1.5">
                        <i class="fas fa-filter text-primary-500"></i> Filter
                    </button>

                    <div class="flex items-center gap-1">
                        <label class="hidden sm:inline text-xs text-gray-500">Urutkan:</label>
                        <select onchange="window.location.href='{{ route('products.index') }}?sort=' + this.value + '&category={{ request('category') }}&search={{ request('search') }}'"
                                class="text-xs border border-gray-200 rounded-xl px-2.5 py-1.5 focus:outline-none focus:border-primary-500 bg-white">
                            <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>Termurah</option>
                            <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>Termahal</option>
                            <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            @if($products->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4">
                @foreach($products as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links('vendor.pagination.tailwind') }}
            </div>
            @else
            <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center my-6">
                <i class="fas fa-box-open text-gray-300 text-5xl mb-3"></i>
                <h3 class="font-bold text-gray-800 text-lg mb-1">Produk Tidak Ditemukan</h3>
                <p class="text-xs text-gray-500 mb-4">Coba cari dengan kata kunci lain atau reset filter yang terpasang.</p>
                <a href="{{ route('products.index') }}" class="inline-block px-4 py-2 gradient-primary text-white font-bold text-xs rounded-xl">
                    Lihat Semua Produk
                </a>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
