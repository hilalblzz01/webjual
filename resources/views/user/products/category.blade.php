@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary-500">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('products.index') }}" class="hover:text-primary-500">Produk</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">{{ $category->name }}</span>
    </nav>

    <div class="flex gap-6">
        <!-- Sidebar -->
        <aside class="hidden md:block w-60 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20">
                <h3 class="font-bold text-gray-800 mb-4">Kategori</h3>
                <div class="space-y-1.5">
                    <a href="{{ route('products.index') }}" class="block text-sm text-gray-600 py-1.5 px-3 rounded-xl hover:bg-primary-50 hover:text-primary-500 transition-colors">
                        Semua Produk
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('categories.show', $cat->slug) }}"
                       class="block text-sm py-1.5 px-3 rounded-xl transition-colors {{ $cat->id == $category->id ? 'bg-primary-50 text-primary-500 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- Products -->
        <div class="flex-1">
            <div class="flex items-center justify-between mb-5 bg-white p-4 rounded-2xl border border-gray-100">
                <div>
                    <h1 class="text-lg font-bold text-gray-800">{{ $category->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $products->total() }} produk ditemukan</p>
                </div>
            </div>

            @if($products->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($products as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links('vendor.pagination.tailwind') }}</div>
            @else
            <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
                <i class="fas fa-box-open text-gray-200 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-600">Belum ada produk di kategori ini</h3>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
