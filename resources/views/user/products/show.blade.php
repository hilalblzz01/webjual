@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-primary-500">Beranda</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('products.index') }}" class="hover:text-primary-500">Produk</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-primary-500">{{ $product->category->name }}</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-800 font-medium truncate">{{ $product->name }}</span>
    </nav>

    <!-- Product Detail -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6" x-data="{ selectedImage: '{{ $product->image_url }}', qty: 1 }">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Images -->
            <div>
                <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 mb-3">
                    <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>

                <!-- Thumbnails -->
                @if($product->images && count($product->images) > 0)
                <div class="flex gap-2 overflow-x-auto">
                    <button @click="selectedImage = '{{ $product->image_url }}'"
                            class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden border-2 hover:border-primary-500 transition-colors"
                            :class="selectedImage === '{{ $product->image_url }}' ? 'border-primary-500' : 'border-gray-200'">
                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                    </button>
                    @foreach($product->images as $img)
                    <button @click="selectedImage = '{{ asset('storage/'.$img) }}'"
                            class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden border-2 hover:border-primary-500 transition-colors"
                            :class="selectedImage === '{{ asset('storage/'.$img) }}' ? 'border-primary-500' : 'border-gray-200'">
                        <img src="{{ asset('storage/'.$img) }}" class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <p class="text-xs text-primary-500 font-medium bg-primary-50 inline-block px-3 py-1 rounded-full mb-3">
                    {{ $product->category->name }}
                </p>

                <h1 class="text-2xl font-bold text-gray-800 mb-3">{{ $product->name }}</h1>

                <!-- Rating -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center gap-1">
                        @php $avgRating = $product->average_rating; @endphp
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $avgRating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                        @endfor
                        <span class="font-semibold text-gray-800 ml-1">{{ $avgRating }}</span>
                    </div>
                    <span class="text-gray-400 text-sm">{{ $product->reviews->count() }} ulasan</span>
                    <span class="text-gray-400 text-sm">{{ number_format($product->sold_count) }} terjual</span>
                </div>

                <!-- Price -->
                <div class="mb-6">
                    <div class="text-3xl font-bold text-primary-500">{{ $product->formatted_effective_price }}</div>
                    @if($product->sale_price)
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-lg text-gray-400 line-through">{{ $product->formatted_price }}</span>
                        <span class="bg-red-100 text-red-600 text-sm font-bold px-2 py-0.5 rounded-lg">
                            Hemat {{ $product->discount_percentage }}%
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Stock Info -->
                <div class="flex items-center gap-2 mb-6">
                    @if($product->is_in_stock)
                    <span class="flex items-center gap-1.5 text-sm text-green-600">
                        <i class="fas fa-check-circle"></i>
                        Stok Ready ({{ $product->stock }} Akun)
                    </span>
                    @else
                    <span class="flex items-center gap-1.5 text-sm text-red-500">
                        <i class="fas fa-times-circle"></i>
                        Stok Habis
                    </span>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $product->description }}</p>
                </div>

                @if($product->is_in_stock)
                <!-- Quantity Selector -->
                <div class="flex items-center gap-4 mb-6">
                    <span class="text-sm font-medium text-gray-700">Jumlah:</span>
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="qty = Math.max(1, qty - 1)" class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 text-gray-600 transition-colors">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <input x-model="qty" type="number" min="1" max="{{ $product->stock }}"
                               class="w-14 h-10 text-center text-sm font-semibold border-x border-gray-200 focus:outline-none">
                        <button @click="qty = Math.min({{ $product->stock }}, qty + 1)" class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 text-gray-600 transition-colors">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                @auth
                <div class="flex gap-3">
                    <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" :value="qty">
                        <button type="submit" class="w-full border-2 border-primary-500 text-primary-500 font-bold py-3 rounded-xl hover:bg-primary-50 transition-colors">
                            <i class="fas fa-cart-plus mr-2"></i>Tambah ke Keranjang
                        </button>
                    </form>
                    <a href="{{ route('checkout.index') }}" class="flex-1 gradient-primary text-white font-bold py-3 rounded-xl text-center hover:opacity-90 transition-opacity">
                        Beli Sekarang <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                @else
                <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-2 w-full gradient-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity">
                    <i class="fab fa-google"></i>
                    Login dengan Google untuk Beli
                </a>
                @endauth
                @endif

                <!-- Wishlist -->
                @auth
                <div class="mt-3 text-center">
                    <button onclick="toggleWishlist({{ $product->id }}, this)"
                            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-red-500 transition-colors wishlist-btn">
                        <i class="{{ $isWishlisted ? 'fas text-red-500' : 'far' }} fa-heart"></i>
                        <span>{{ $isWishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}</span>
                    </button>
                </div>
                @endauth

                <!-- Meta Info -->
                @if($product->sku || $product->weight)
                <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-2 gap-3 text-sm">
                    @if($product->sku)
                    <div>
                        <span class="text-gray-500">SKU:</span>
                        <span class="ml-1 font-medium text-gray-800">{{ $product->sku }}</span>
                    </div>
                    @endif
                    @if($product->weight)
                    <div>
                        <span class="text-gray-500">Berat:</span>
                        <span class="ml-1 font-medium text-gray-800">{{ $product->weight }} kg</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Description Tab -->
    @if($product->long_description)
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Deskripsi Produk</h2>
        <div class="prose max-w-none text-gray-600 text-sm leading-relaxed">
            {!! nl2br(e($product->long_description)) !!}
        </div>
    </div>
    @endif

    <!-- Reviews Section -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-800">Ulasan Pembeli ({{ $product->reviews->count() }})</h2>
            <div class="flex items-center gap-2 bg-primary-50 px-4 py-2 rounded-xl">
                <span class="text-2xl font-bold text-primary-500">{{ $product->average_rating }}</span>
                <div>
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-xs {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500">{{ $product->reviews->count() }} ulasan</p>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        @auth
        @php
        $userCompletedOrder = auth()->user()->orders()
            ->whereIn('status', ['paid', 'completed'])
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first();

        $userReview = $userCompletedOrder
            ? \App\Models\Review::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->where('order_id', $userCompletedOrder->id)
                ->first()
            : null;
        @endphp

        @if($userCompletedOrder && !$userReview)
        <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-5 mb-6">
            <h3 class="text-sm font-bold text-gray-800 mb-1">⭐ Tulis Ulasan & Rating Anda</h3>
            <p class="text-xs text-gray-600 mb-3">Pesanan Anda telah berhasil diterima. Silakan beri nilai produk ini:</p>
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="order_id" value="{{ $userCompletedOrder->id }}">

                <!-- Star Rating Input -->
                <div class="flex items-center gap-1.5 mb-3" x-data="{ rating: 5, hoverRating: 0 }">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            @click="rating = {{ $i }}"
                            @mouseenter="hoverRating = {{ $i }}"
                            @mouseleave="hoverRating = 0"
                            class="text-3xl transition-transform hover:scale-125 focus:outline-none"
                            :class="(hoverRating ? hoverRating >= {{ $i }} : rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300'">★</button>
                    @endfor
                    <input type="hidden" name="rating" :value="rating">
                    <span class="text-xs font-bold text-gray-700 ml-2" x-text="rating + ' Bintang'"></span>
                </div>

                <textarea name="comment" rows="3"
                          placeholder="Bagikan pengalaman & kepuasan Anda tentang produk digital ini..."
                          class="w-full px-4 py-3 text-xs sm:text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500 resize-none mb-3 bg-white"></textarea>

                <button type="submit" class="gradient-primary text-white text-xs sm:text-sm font-bold px-6 py-2.5 rounded-xl hover:opacity-90 transition-opacity shadow-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Ulasan Sekarang
                </button>
            </form>
        </div>
        @endif
        @endauth

        <!-- Reviews List -->
        @forelse($product->reviews()->where('is_approved', true)->with('user')->latest()->get() as $review)
        <div class="flex gap-3 sm:gap-4 py-4 border-b border-gray-100 last:border-0">
            <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}"
                 class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover flex-shrink-0 border border-gray-200">
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1 flex-wrap gap-1">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <p class="text-xs sm:text-sm font-bold text-gray-800 truncate">{{ $review->user->name }}</p>
                        <span class="inline-flex items-center gap-0.5 text-[10px] bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded-full">
                            <i class="fas fa-check-circle text-[9px]"></i> Pembeli Terverifikasi
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-1 mb-1.5">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                    @endfor
                    <span class="text-xs font-semibold text-gray-600 ml-1">{{ $review->rating }}/5</span>
                </div>
                @if($review->comment)
                <p class="text-xs sm:text-sm text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-100">"{{ $review->comment }}"</p>
                @endif
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 py-8">Belum ada ulasan. Jadilah yang pertama!</p>
        @endforelse
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-5">Produk Terkait</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($relatedProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function toggleWishlist(productId, btn) {
    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        const icon = btn.querySelector('i');
        const span = btn.querySelector('span');
        if (data.wishlisted) {
            icon.className = 'fas fa-heart text-red-500';
            if (span) span.textContent = 'Hapus dari Wishlist';
        } else {
            icon.className = 'far fa-heart';
            if (span) span.textContent = 'Tambah ke Wishlist';
        }
    });
}
</script>
@endpush
