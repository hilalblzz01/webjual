@extends('layouts.app')

@section('title', 'Wishlist Saya')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">❤️ Wishlist Saya</h1>

    @if($wishlists->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <i class="far fa-heart text-gray-200 text-8xl mb-6"></i>
        <h2 class="text-xl font-semibold text-gray-600 mb-2">Wishlist Kosong</h2>
        <p class="text-gray-400 mb-6">Simpan produk favorit Anda di sini!</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 gradient-primary text-white font-bold px-6 py-3 rounded-xl hover:opacity-90">
            Jelajahi Produk
        </a>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($wishlists as $wishlist)
        @if($wishlist->product)
        @include('components.product-card', ['product' => $wishlist->product])
        @endif
        @endforeach
    </div>
    @endif
</div>
@endsection
