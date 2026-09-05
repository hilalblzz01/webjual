@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary-500">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('orders.index') }}" class="hover:text-primary-500">Pesanan Saya</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">{{ $order->invoice_number }}</span>
    </nav>

    <!-- Order Status & Timeline -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
            <div>
                <p class="text-xs text-gray-400">Nomor Invoice</p>
                <p class="text-lg font-bold text-gray-800">{{ $order->invoice_number }}</p>
            </div>
            <div>
                @php $colors = ['pending'=>'yellow','paid'=>'blue','processing'=>'orange','shipped'=>'purple','completed'=>'green','cancelled'=>'red']; @endphp
                <span class="px-4 py-1.5 text-xs font-bold rounded-full bg-{{ $colors[$order->status]??'gray' }}-100 text-{{ $colors[$order->status]??'gray' }}-700">
                    Status: {{ $order->status_label }}
                </span>
            </div>
        </div>

        <!-- Payment Info Banner for QRIS -->
        @if($order->status === 'pending')
        <div class="bg-primary-50 border border-primary-100 rounded-xl p-5 mb-5" x-data="{ openQr: true }">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-qrcode text-primary-500 text-xl"></i>
                    <h3 class="font-bold text-gray-800 text-base">Pembayaran QRIS DANA Bisnis</h3>
                </div>
                <button @click="openQr = !openQr" class="text-xs text-primary-500 hover:underline">
                    <span x-text="openQr ? 'Sembunyikan QRIS' : 'Tampilkan QRIS'"></span>
                </button>
            </div>

            @php
            $dynamicQris = \App\Services\QrisService::generateDynamic((int) $order->total_price);
            $qrImageUrl  = \App\Services\QrisService::getQrImageUrl($dynamicQris);
            @endphp

            <div x-show="openQr" class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-center pt-2">
                <!-- QR Code Display -->
                <div class="flex flex-col items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 mb-1">Scan QRIS DANA Bisnis (Auto Nominal):</p>
                    <p class="text-xs font-bold text-gray-800 mb-2">🏪 {{ \App\Services\QrisService::getMerchantName() }}</p>
                    
                    <div class="w-52 h-52 bg-white rounded-xl flex items-center justify-center p-2 border-2 border-primary-500 shadow-md">
                        <img src="{{ $qrImageUrl }}" alt="QRIS {{ $order->invoice_number }}" class="w-full h-full object-contain">
                    </div>
                    
                    <p class="text-[11px] text-gray-500 font-semibold mt-2 text-center">
                        <i class="fas fa-check-circle text-green-500 mr-1"></i>Nominal Otomatis Terisi: <span class="text-primary-500 font-bold">{{ $order->formatted_total }}</span>
                    </p>
                    <p class="text-[10px] text-gray-400 text-center">Dukungan: DANA, GoPay, OVO, ShopeePay, BCA, Mandiri, BRI, dll.</p>
                </div>

                <!-- Instructions -->
                <div class="space-y-3">
                    <div class="bg-white p-3 rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500">Total Harus Ditransfer:</p>
                        <p class="text-2xl font-bold text-primary-500">{{ $order->formatted_total }}</p>
                    </div>

                    <ol class="text-xs text-gray-600 space-y-1.5 list-decimal list-inside">
                        <li>Scan QRIS di samping menggunakan aplikasi DANA / E-Wallet Anda.</li>
                        <li>Masukkan nominal **{{ $order->formatted_total }}**.</li>
                        <li>Selesaikan pembayaran di aplikasi DANA.</li>
                        <li>Upload screenshot **Bukti Transfer** atau chat Admin di bawah ini.</li>
                    </ol>

                    <!-- Form Upload Bukti TF -->
                    <form action="{{ route('orders.proof', $order->id) }}" method="POST" enctype="multipart/form-data" class="pt-2">
                        @csrf
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Upload Bukti Transfer DANA:</label>
                        <div class="flex gap-2">
                            <input type="file" name="payment_proof" accept="image/*" required
                                   class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-500 file:text-white hover:file:opacity-90">
                            <button type="submit" class="gradient-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:opacity-90">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        <!-- Digital Items Delivered Card -->
        @if(in_array($order->status, ['paid', 'completed']) && $order->digitalItems->count() > 0)
        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-5 mb-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                    <i class="fas fa-key text-xs"></i>
                </div>
                <div>
                    <h3 class="font-bold text-green-800 text-base">🎁 AKUN / LISENSI DIGITAL ANDA</h3>
                    <p class="text-xs text-green-600">Pembayaran Diterima! Berikut data akses produk digital Anda:</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($order->digitalItems as $digital)
                <div class="bg-white p-4 rounded-xl border border-green-200 shadow-sm" x-data="{ copied: false, text: '{{ e($digital->credentials) }}' }">
                    <p class="text-xs font-semibold text-gray-500 mb-1">Produk: <span class="text-gray-800 font-bold">{{ $digital->product->name ?? 'Digital Item' }}</span></p>
                    <div class="p-3 bg-gray-900 text-green-400 rounded-xl font-mono text-xs overflow-x-auto whitespace-pre-wrap select-all mb-2">{{ $digital->credentials }}</div>
                    <button @click="navigator.clipboard.writeText(text); copied = true; setTimeout(() => copied = false, 2000)"
                            class="text-xs font-bold px-3 py-1.5 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors inline-flex items-center gap-1.5">
                        <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                        <span x-text="copied ? 'Tersalin!' : 'Salin Data Akses'"></span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Left: Products & LIVE CHAT WITH ADMIN -->
        <div class="md:col-span-2 space-y-5">

            <!-- Products -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4">Item Pesanan</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex gap-3">
                        @if($item->product_image)
                        <img src="{{ asset('storage/' . $item->product_image) }}" class="w-14 h-14 rounded-xl object-cover">
                        @else
                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-box text-gray-300 text-lg"></i>
                        </div>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }}x {{ $item->formatted_price }}</p>
                        </div>
                        <p class="text-sm font-bold text-gray-800">{{ $item->formatted_subtotal }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- ⭐ Ulas Produk & Rating (Untuk Pesanan Paid / Completed) -->
            @if(in_array($order->status, ['paid', 'completed']))
            <div id="review-section" class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border-2 border-amber-200 p-5 scroll-mt-24">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-md">
                        ⭐
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Berikan Rating & Ulasan Produk</h3>
                        <p class="text-xs text-gray-600">Pendapat Anda sangat berharga bagi pembeli lainnya!</p>
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
                            <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                <i class="fas fa-check-circle mr-0.5"></i> Sudah Diulas
                            </span>
                            @else
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                                Belum Diulas
                            </span>
                            @endif
                        </div>

                        @if($existingReview)
                        <!-- Display Submitted Review -->
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-sm {{ $i <= $existingReview->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                @endfor
                                <span class="text-xs font-bold text-gray-700 ml-1">({{ $existingReview->rating }}/5)</span>
                            </div>
                            @if($existingReview->comment)
                            <p class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-lg italic border border-gray-100">"{{ $existingReview->comment }}"</p>
                            @endif
                            <p class="text-[10px] text-gray-400">Dikirim: {{ $existingReview->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @else
                        <!-- Form Submit Review -->
                        <form action="{{ route('reviews.store') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="rating" :value="rating">

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Rating Bintang:</label>
                                <div class="flex items-center gap-1.5">
                                    @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            @click="rating = {{ $i }}"
                                            @mouseenter="hoverRating = {{ $i }}"
                                            @mouseleave="hoverRating = 0"
                                            class="text-2xl transition-transform hover:scale-125 focus:outline-none"
                                            :class="(hoverRating ? hoverRating >= {{ $i }} : rating >= {{ $i }}) ? 'text-yellow-400' : 'text-gray-300'">
                                        ★
                                    </button>
                                    @endfor
                                    <span class="text-xs font-bold text-gray-700 ml-2" x-text="rating + ' Bintang'"></span>
                                </div>
                            </div>

                            <div>
                                <textarea name="comment" rows="2"
                                          placeholder="Tulis ulasan/pengalaman Anda tentang produk digital ini..."
                                          class="w-full p-2.5 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500 resize-none bg-gray-50"></textarea>
                            </div>

                            <button type="submit" class="gradient-primary text-white text-xs font-bold px-4 py-2 rounded-xl hover:opacity-90 transition-opacity shadow-sm flex items-center gap-1.5">
                                <i class="fas fa-paper-plane"></i> Kirim Ulasan & Rating
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- LIVE CHAT WITH ADMIN -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                        <h3 class="font-bold text-gray-800 text-base">Chat Konfirmasi Admin</h3>
                    </div>
                    <span class="text-xs text-gray-400">Respon Cepat</span>
                </div>

                <!-- Chat Messages History -->
                <div class="space-y-3 max-h-80 overflow-y-auto p-3 bg-gray-50 rounded-xl mb-4" id="chatContainer">
                    <div class="text-center py-2">
                        <span class="text-xs text-gray-400 bg-white px-3 py-1 rounded-full border border-gray-200">
                            Tulis pesan atau kirim ID Transaksi DANA di bawah untuk konfirmasi
                        </span>
                    </div>

                    @forelse($order->chats as $chat)
                    <div class="flex flex-col {{ $chat->sender_role === 'customer' ? 'items-end' : 'items-start' }}">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="text-xs font-semibold text-gray-600">
                                {{ $chat->sender_role === 'customer' ? 'Saya' : 'Admin Toko' }}
                            </span>
                            <span class="text-[10px] text-gray-400">{{ $chat->created_at->format('H:i') }}</span>
                        </div>

                        <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-xs shadow-sm font-bold
                            {{ $chat->sender_role === 'customer' ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-br-none' : 'bg-white border-2 border-gray-300 text-gray-900 rounded-bl-none' }}">
                            <p class="leading-relaxed whitespace-pre-wrap">{{ $chat->message }}</p>

                            @if($chat->attachment)
                            <div class="mt-2 pt-2 border-t {{ $chat->sender_role === 'customer' ? 'border-white/20' : 'border-gray-100' }}">
                                <a href="{{ asset('storage/' . $chat->attachment) }}" target="_blank" class="block">
                                    <img src="{{ asset('storage/' . $chat->attachment) }}" class="max-h-36 rounded-lg object-cover">
                                    <span class="text-[10px] underline mt-1 block">Lihat Gambar</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada percakapan. Kirim pesan ke admin sekarang!</p>
                    @endforelse
                </div>

                <!-- Chat Input Form -->
                <form action="{{ route('orders.chat', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-center gap-2">
                        <label class="p-2 text-gray-400 hover:text-primary-500 cursor-pointer rounded-xl hover:bg-gray-100 transition-colors" title="Kirim Foto Bukti">
                            <i class="fas fa-paperclip text-base"></i>
                            <input type="file" name="attachment" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </label>
                        <input type="text" name="message" placeholder="Tulis ID Transaksi DANA / Pesan konfirmasi..."
                               class="flex-1 px-4 py-2.5 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                        <button type="submit" class="gradient-primary text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:opacity-90">
                            Kirim <i class="fas fa-paper-plane ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Sidebar Order Details -->
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">Ringkasan Pembayaran</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-semibold text-gray-800">{{ $order->payment_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pengiriman</span>
                        <span class="text-green-600 font-semibold">Digital (Rp 0)</span>
                    </div>
                    <div class="border-t border-gray-100 pt-2 flex justify-between text-sm font-bold">
                        <span>Total</span>
                        <span class="text-primary-500">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">Kontak WhatsApp Tujuan</h3>
                <p class="text-xs text-gray-700 font-medium"><i class="fab fa-whatsapp text-green-500 mr-1.5"></i>{{ $order->shipping_phone }}</p>
                <p class="text-xs text-gray-400 mt-1">Sertakan nomor WA yang aktif di percakapan jika ada perubahan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
