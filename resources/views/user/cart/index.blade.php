@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary-500">Beranda</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Keranjang Belanja</span>
    </nav>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Keranjang Belanja <span class="text-primary-500">({{ $carts->count() }} item)</span></h1>

    @if($carts->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <i class="fas fa-shopping-cart text-gray-200 text-8xl mb-6"></i>
        <h2 class="text-xl font-semibold text-gray-600 mb-2">Keranjang Anda Kosong</h2>
        <p class="text-gray-400 mb-6">Yuk mulai belanja dan temukan produk favorit Anda!</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 gradient-primary text-white font-bold px-6 py-3 rounded-xl hover:opacity-90 transition-opacity">
            <i class="fas fa-arrow-left"></i>
            Mulai Belanja
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Cart Items -->
        <div class="lg:col-span-2 space-y-3">
            @foreach($carts as $cart)
            <div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-4" x-data="{ qty: {{ $cart->quantity }} }">
                <!-- Image -->
                <a href="{{ route('products.show', $cart->product->slug) }}" class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-gray-50">
                    <img src="{{ $cart->product->image_url }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover">
                </a>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <a href="{{ route('products.show', $cart->product->slug) }}" class="text-sm font-semibold text-gray-800 hover:text-primary-500 transition-colors line-clamp-2">
                        {{ $cart->product->name }}
                    </a>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $cart->product->category->name }}</p>

                    <div class="flex items-center justify-between mt-3">
                        <!-- Quantity Controls -->
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                            <button @click="qty = Math.max(1, qty - 1); updateCart({{ $cart->id }}, qty)"
                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-50 text-gray-600 transition-colors">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span x-text="qty" class="w-10 text-center text-sm font-semibold border-x border-gray-200"></span>
                            <button @click="qty = Math.min({{ $cart->product->stock }}, qty + 1); updateCart({{ $cart->id }}, qty)"
                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-50 text-gray-600 transition-colors">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>

                        <!-- Subtotal -->
                        <div class="text-right">
                            <p class="text-sm font-bold text-primary-500" id="subtotal-{{ $cart->id }}">{{ $cart->formatted_subtotal }}</p>
                            <p class="text-xs text-gray-400">@ {{ $cart->product->formatted_effective_price }}</p>
                        </div>
                    </div>
                </div>

                <!-- Delete -->
                <form action="{{ route('cart.destroy', $cart->id) }}" method="POST" class="flex-shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-gray-300 hover:text-red-400 transition-colors p-1"
                            onclick="return confirm('Hapus produk ini dari keranjang?')">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </form>
            </div>
            @endforeach
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20">
                <h2 class="font-bold text-gray-800 mb-4">Ringkasan Pesanan</h2>

                <div class="space-y-3 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal ({{ $carts->sum('quantity') }} item)</span>
                        <span class="font-semibold text-gray-800" id="total-price">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pengiriman Digital</span>
                        <span class="text-green-600 font-medium">GRATIS (Rp 0)</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-3 mb-5">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="font-bold text-xl text-primary-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('checkout.index') }}"
                   class="block w-full gradient-primary text-white font-bold py-3 rounded-xl text-center hover:opacity-90 transition-opacity">
                    Checkout <i class="fas fa-arrow-right ml-2"></i>
                </a>

                <a href="{{ route('products.index') }}" class="block text-center mt-3 text-sm text-gray-500 hover:text-primary-500 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function updateCart(cartId, quantity) {
    fetch(`/cart/${cartId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ quantity })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const subtotalEl = document.getElementById(`subtotal-${cartId}`);
            if (subtotalEl) subtotalEl.textContent = data.subtotal;
            const totalEl = document.getElementById('total-price');
            if (totalEl) totalEl.textContent = data.total;
        }
    });
}
</script>
@endpush
