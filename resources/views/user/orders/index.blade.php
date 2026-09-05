@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Pesanan Saya</h1>

    <!-- Status Filter Tabs -->
    <div class="flex gap-2 overflow-x-auto pb-2 mb-6 scrollbar-hide">
        <a href="{{ route('orders.index') }}"
           class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ !request('status') ? 'bg-primary-500 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            📋 Semua Pesanan
        </a>
        <a href="{{ route('orders.index') }}?status=pending"
           class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' }}">
            ⏳ Belum Dibayar (Pending)
        </a>
        <a href="{{ route('orders.index') }}?status=paid"
           class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request('status') == 'paid' ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' }}">
            ⚡ Terkonfirmasi & Kirim (Paid)
        </a>
        <a href="{{ route('orders.index') }}?status=completed"
           class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request('status') == 'completed' ? 'bg-green-600 text-white shadow-md' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' }}">
            ✅ Selesai
        </a>
        <a href="{{ route('orders.index') }}?status=cancelled"
           class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request('status') == 'cancelled' ? 'bg-red-500 text-white shadow-md' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' }}">
            ❌ Dibatalkan
        </a>
    </div>

    @if($orders->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <i class="fas fa-box-open text-gray-200 text-6xl mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-600">Belum ada pesanan</h3>
        <p class="text-gray-400 mt-1 mb-6">Mulai belanja sekarang dan dapatkan akun digital favorit Anda!</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 gradient-primary text-white font-bold px-6 py-3 rounded-xl hover:opacity-90">
            Mulai Belanja
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <!-- Order Header -->
            <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100 flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-gray-800">{{ $order->invoice_number }}</span>
                    <span class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                @php
                $colors = ['pending'=>'yellow','paid'=>'blue','processing'=>'orange','shipped'=>'purple','completed'=>'green','cancelled'=>'red'];
                $color = $colors[$order->status] ?? 'gray';
                @endphp
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $color }}-100 text-{{ $color }}-700">
                    {{ $order->status_label }}
                </span>
            </div>

            <!-- Order Items Preview -->
            <div class="px-5 py-4">
                @foreach($order->items->take(3) as $item)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    @if($item->product_image)
                    <img src="{{ asset('storage/' . $item->product_image) }}" class="w-12 h-12 rounded-xl object-cover">
                    @else
                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-box text-gray-300"></i>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->quantity }}x {{ $item->formatted_price }}</p>
                    </div>
                    <p class="text-sm font-bold text-gray-800">{{ $item->formatted_subtotal }}</p>
                </div>
                @endforeach

                @if($order->items->count() > 3)
                <p class="text-xs text-gray-400 py-2">+ {{ $order->items->count() - 3 }} produk lainnya</p>
                @endif
            </div>

            <!-- Order Footer -->
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 flex-wrap gap-2 bg-gray-50/50">
                <div>
                    <span class="text-xs text-gray-500">Total Pembayaran:</span>
                    <span class="text-base sm:text-lg font-extrabold text-primary-500 ml-1.5">{{ $order->formatted_total }}</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @if(in_array($order->status, ['pending']))
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                          onsubmit="return confirm('Yakin membatalkan pesanan ini?')">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-500 border border-red-200 rounded-xl hover:bg-red-50 transition-colors">
                            Batalkan
                        </button>
                    </form>
                    @endif

                    <!-- Review Button when Order is Confirmed/Delivered (Paid / Completed / Shipped) -->
                    @if(in_array($order->status, ['paid', 'completed', 'shipped']))
                        @php
                            $hasReview = \App\Models\Review::where('user_id', auth()->id())->where('order_id', $order->id)->exists();
                        @endphp
                        @if($hasReview)
                        <a href="{{ route('orders.show', $order->id) }}#review-section"
                           class="px-3.5 py-1.5 text-xs font-bold text-green-700 bg-green-100 border border-green-300 rounded-xl hover:bg-green-200 transition-colors inline-flex items-center gap-1">
                            <i class="fas fa-check-circle text-green-600"></i> Sudah Diulas
                        </a>
                        @else
                        <a href="{{ route('orders.show', $order->id) }}#review-section"
                           class="px-3.5 py-1.5 text-xs font-extrabold text-amber-900 bg-gradient-to-r from-yellow-300 via-amber-400 to-orange-400 rounded-xl shadow hover:brightness-105 transition-all inline-flex items-center gap-1 animate-pulse">
                            <i class="fas fa-star text-white"></i> ⭐ Beri Rating & Ulasan
                        </a>
                        @endif
                    @endif

                    <a href="{{ route('orders.show', $order->id) }}"
                       class="px-4 py-1.5 text-xs font-bold text-white gradient-primary rounded-xl hover:opacity-90 transition-opacity">
                        Detail Pesanan
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">{{ $orders->links('vendor.pagination.tailwind') }}</div>
    @endif
</div>
@endsection
