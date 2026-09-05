<div class="card-hover bg-white rounded-xl sm:rounded-2xl border border-gray-100 overflow-hidden group cursor-pointer flex flex-col justify-between h-full">
    <!-- Product Image & Overlay -->
    <div class="relative overflow-hidden aspect-square bg-gray-50 flex-shrink-0">
        <a href="{{ route('products.show', $product->slug) }}" class="block w-full h-full">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}"
                 class="product-img w-full h-full object-cover"
                 loading="lazy">
        </a>

        <!-- Badges -->
        <div class="absolute top-1.5 left-1.5 sm:top-2 sm:left-2 flex flex-col gap-1 z-10 pointer-events-none">
            @if($product->discount_percentage > 0)
            <span class="bg-red-500 text-white text-[10px] sm:text-xs font-bold px-1.5 py-0.5 sm:px-2 sm:py-0.5 rounded-md sm:rounded-lg shadow-sm">
                -{{ $product->discount_percentage }}%
            </span>
            @endif
            @if($product->stock <= 5 && $product->stock > 0)
            <span class="bg-orange-500 text-white text-[10px] sm:text-xs font-bold px-1.5 py-0.5 sm:px-2 sm:py-0.5 rounded-md sm:rounded-lg shadow-sm">
                Sisa {{ $product->stock }}
            </span>
            @endif
            @if(!$product->is_in_stock)
            <span class="bg-gray-600 text-white text-[10px] sm:text-xs font-bold px-1.5 py-0.5 sm:px-2 sm:py-0.5 rounded-md sm:rounded-lg shadow-sm">
                Habis
            </span>
            @endif
        </div>

        <!-- Wishlist Button -->
        @auth
        <button onclick="toggleWishlist({{ $product->id }}, this)"
                class="absolute top-1.5 right-1.5 sm:top-2 sm:right-2 w-7 h-7 sm:w-8 sm:h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-transform wishlist-btn z-10"
                data-product-id="{{ $product->id }}">
            <i class="{{ auth()->user()->wishlistedProducts->contains($product->id) ? 'fas text-red-500' : 'far text-gray-400' }} fa-heart text-xs sm:text-sm"></i>
        </button>
        @endauth

        <!-- Quick Add to Cart (Hover on Desktop) -->
        @if($product->is_in_stock)
        <div class="hidden sm:block absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-2.5 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
            <form action="{{ route('cart.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                @auth
                <button type="submit" class="w-full bg-white text-primary-500 text-xs font-bold py-1.5 rounded-lg hover:bg-primary-500 hover:text-white transition-colors">
                    <i class="fas fa-cart-plus mr-1"></i> Tambah ke Keranjang
                </button>
                @else
                <a href="{{ route('auth.google') }}" class="block w-full bg-white text-primary-500 text-xs font-bold py-1.5 rounded-lg text-center hover:bg-primary-50 transition-colors">
                    Login untuk Beli
                </a>
                @endauth
            </form>
        </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="p-2.5 sm:p-3.5 flex flex-col justify-between flex-1">
        <div>
            <a href="{{ route('products.show', $product->slug) }}" class="block">
                <p class="text-gray-800 text-xs sm:text-sm font-semibold leading-snug line-clamp-2 hover:text-primary-500 transition-colors mb-1.5">
                    {{ $product->name }}
                </p>
            </a>

            <!-- Rating -->
            <div class="flex items-center gap-1 mb-2">
                @php $rating = $product->average_rating; @endphp
                <div class="flex text-[10px] sm:text-xs">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                    @endfor
                </div>
                <span class="text-[10px] sm:text-xs text-gray-400">({{ $product->reviews->count() }})</span>
            </div>
        </div>

        <!-- Price & Sold -->
        <div class="mt-auto pt-1">
            <div class="flex items-baseline justify-between gap-1 flex-wrap">
                <p class="text-primary-500 font-bold text-xs sm:text-sm">{{ $product->formatted_effective_price }}</p>
                @if($product->sale_price)
                <p class="text-gray-400 text-[10px] sm:text-xs line-through">{{ $product->formatted_price }}</p>
                @endif
            </div>
            <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5"><i class="fas fa-check-circle text-green-500 text-[9px] mr-0.5"></i>{{ number_format($product->sold_count) }} terjual</p>
        </div>
    </div>
</div>

@once
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
        if (data.wishlisted) {
            icon.className = 'fas fa-heart text-red-500 text-sm';
        } else {
            icon.className = 'far fa-heart text-gray-400 text-sm';
        }
    });
}
</script>
@endpush
@endonce
