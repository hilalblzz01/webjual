@extends('layouts.app')

@section('title', 'Chat Admin - Step 3 #' . $order->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">

    <!-- Step Progress Indicator -->
    <div class="flex items-center justify-center gap-2 sm:gap-4 mb-6 text-xs sm:text-sm font-semibold">
        <div class="flex items-center gap-2 text-green-600">
            <span class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs"><i class="fas fa-check text-xs"></i></span>
            <span>Checkout</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5 bg-green-500"></div>
        <div class="flex items-center gap-2 text-green-600">
            <span class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs"><i class="fas fa-check text-xs"></i></span>
            <span>Pembayaran QRIS</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5 bg-primary-500"></div>
        <div class="flex items-center gap-2 text-primary-500">
            <span class="w-7 h-7 rounded-full gradient-primary text-white flex items-center justify-center font-bold text-xs">3</span>
            <span>Chat Admin & Akses</span>
        </div>
    </div>

    <!-- Digital Items Delivered Card (If Paid / Completed) -->
    @if(in_array($order->status, ['paid', 'completed']) && $order->digitalItems->count() > 0)
    <div class="bg-green-50 border-2 border-green-300 rounded-2xl p-6 mb-6 shadow-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-green-500 text-white rounded-2xl flex items-center justify-center font-bold text-lg">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <h2 class="font-bold text-green-800 text-lg">🎁 AKUN / LISENSI DIGITAL ANDA TERSEDIA!</h2>
                <p class="text-xs text-green-600">Pembayaran Diterima! Berikut data login / lisensi produk digital Anda:</p>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($order->digitalItems as $digital)
            <div class="bg-white p-4 rounded-xl border border-green-200 shadow-sm" x-data="{ copied: false, text: '{{ e($digital->credentials) }}' }">
                <p class="text-xs font-semibold text-gray-500 mb-1">Produk: <span class="text-gray-800 font-bold">{{ $digital->product->name ?? 'Produk Digital' }}</span></p>
                <div class="p-3 bg-gray-900 text-green-400 rounded-xl font-mono text-xs overflow-x-auto whitespace-pre-wrap select-all mb-3">{{ $digital->credentials }}</div>
                <button @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                        class="text-xs font-bold px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 transition-colors inline-flex items-center gap-2 shadow-sm">
                    <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                    <span x-text="copied ? 'Berhasil Disalin!' : 'Salin Data Akses'"></span>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ⭐ RATING & ULASAN PRODUK CARD (Inside Chatroom / Step 3 Page) -->
    @if(in_array($order->status, ['paid', 'completed']))
    <div id="review-section" class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border-2 border-amber-300 p-5 mb-6 shadow-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-amber-500 text-white rounded-2xl flex items-center justify-center font-bold text-xl shadow-md">
                ⭐
            </div>
            <div>
                <h3 class="font-extrabold text-gray-900 text-base sm:text-lg">Berikan Rating & Ulasan Produk Digital</h3>
                <p class="text-xs text-gray-600">Pesanan telah dikonfirmasi & akun sudah Anda terima! Bagikan ulasan Anda di bawah:</p>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($order->items as $item)
            @php
                $existingReview = \App\Models\Review::where('user_id', auth()->id())
                    ->where('product_id', $item->product_id)
                    ->where('order_id', $order->id)
                    ->first();
            @endphp

            <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-sm" x-data="{ rating: {{ $existingReview ? $existingReview->rating : 5 }}, hoverRating: 0 }">
                <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-3">
                    <p class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                        <i class="fas fa-box text-amber-500"></i> {{ $item->product_name }}
                    </p>
                    @if($existingReview)
                    <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <i class="fas fa-check-circle"></i> Sudah Diulas
                    </span>
                    @else
                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                        Belum Diulas
                    </span>
                    @endif
                </div>

                @if($existingReview)
                <div class="space-y-1.5">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-sm {{ $i <= $existingReview->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                        @endfor
                        <span class="text-xs font-bold text-gray-700 ml-1">({{ $existingReview->rating }}/5 Bintang)</span>
                    </div>
                    @if($existingReview->comment)
                    <p class="text-xs text-gray-700 bg-gray-50 p-2.5 rounded-lg italic border border-gray-100">"{{ $existingReview->comment }}"</p>
                    @endif
                    <p class="text-[10px] text-gray-400">Dikirim pada: {{ $existingReview->created_at->format('d M Y, H:i') }}</p>
                </div>
                @else
                <form action="{{ route('reviews.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="rating" :value="rating">

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Rating Bintang:</label>
                        <div class="flex items-center gap-1.5">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    @click="rating = {{ $i }}"
                                    @mouseenter="hoverRating = {{ $i }}"
                                    @mouseleave="hoverRating = 0"
                                    class="text-3xl transition-transform hover:scale-125 focus:outline-none"
                                    :class="(hoverRating ? hoverRating >= {{ $i }} : rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300'">
                                ★
                            </button>
                            @endfor
                            <span class="text-xs font-extrabold text-amber-700 ml-2" x-text="rating + ' Bintang'"></span>
                        </div>
                    </div>

                    <div>
                        <textarea name="comment" rows="2"
                                  placeholder="Bagikan ulasan/pengalaman Anda tentang barang digital ini..."
                                  class="w-full p-3 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500 bg-gray-50 resize-none"></textarea>
                    </div>

                    <button type="submit" class="gradient-primary text-white text-xs font-bold px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity shadow-md flex items-center gap-1.5">
                        <i class="fas fa-paper-plane"></i> Kirim Ulasan & Rating Sekarang
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- MAIN LIVE CHAT ROOM CONTAINER -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col min-h-[500px]">

        <!-- Chat Header -->
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center text-white font-bold">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-bold text-gray-800 text-base">Chat Admin TokoKita</h2>
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                    </div>
                    <p class="text-xs text-gray-500">Invoice: <span class="font-semibold text-gray-700">{{ $order->invoice_number }}</span> | WA: <span class="font-semibold text-gray-700">{{ $order->shipping_phone }}</span></p>
                </div>
            </div>

            <div>
                @php $colors = ['pending'=>'yellow','paid'=>'blue','processing'=>'orange','shipped'=>'purple','completed'=>'green','cancelled'=>'red']; @endphp
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-{{ $colors[$order->status]??'gray' }}-100 text-{{ $colors[$order->status]??'gray' }}-700">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50/50" id="chatMessages">
            <div class="text-center py-2">
                <span class="text-[11px] text-gray-400 bg-white px-3 py-1 rounded-full border border-gray-200">
                    Ruang Chat Konfirmasi Admin. Admin akan segera memverifikasi pembayaran DANA Anda.
                </span>
            </div>

            @forelse($order->chats as $chat)
            <div class="flex flex-col {{ $chat->sender_role === 'customer' ? 'items-end' : 'items-start' }}">
                <div class="flex items-center gap-1.5 mb-1">
                    <span class="text-xs font-semibold text-gray-600">
                        {{ $chat->sender_role === 'customer' ? 'Saya' : 'Admin TokoKita' }}
                    </span>
                    <span class="text-[10px] text-gray-400">{{ $chat->created_at->format('H:i') }}</span>
                </div>

                <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl px-4 py-3 text-xs shadow-md font-bold
                    {{ $chat->sender_role === 'customer' ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-br-none' : 'bg-slate-900 text-white border border-slate-700 rounded-bl-none' }}">
                    <p class="leading-relaxed whitespace-pre-wrap">{{ $chat->message }}</p>

                    @if($chat->attachment)
                    <div class="mt-2.5 pt-2.5 border-t border-white/20">
                        <a href="{{ asset('storage/' . $chat->attachment) }}" target="_blank" class="block">
                            <img src="{{ asset('storage/' . $chat->attachment) }}" class="max-h-48 rounded-xl object-cover border border-black/20">
                            <span class="text-[10px] underline mt-1 block"><i class="fas fa-image mr-1"></i>Lihat Lampiran</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-400">
                <i class="fas fa-comments text-4xl mb-2 block text-gray-300"></i>
                <p class="text-xs">Belum ada percakapan. Kirim pesan ke admin di bawah ini!</p>
            </div>
            @endforelse
        </div>

        <!-- Chat Input Form -->
        <div class="p-3 bg-white border-t border-gray-100">
            <form action="{{ route('orders.chat', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center gap-2">
                    <label class="p-2.5 text-gray-400 hover:text-primary-500 cursor-pointer rounded-xl hover:bg-gray-100 transition-colors" title="Kirim Foto / Bukti">
                        <i class="fas fa-paperclip text-lg"></i>
                        <input type="file" name="attachment" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </label>
                    <input type="text" name="message" placeholder="Tulis pesan ke admin..."
                           class="flex-1 px-4 py-2.5 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                    <button type="submit" class="gradient-primary text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:opacity-90 transition-opacity flex items-center gap-1.5">
                        <span>Kirim</span>
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
