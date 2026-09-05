@extends('layouts.app')

@section('title', 'Checkout Digital - Step 1')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <!-- Step Progress Indicator -->
    <div class="flex items-center justify-center gap-2 sm:gap-4 mb-8 text-xs sm:text-sm font-semibold">
        <div class="flex items-center gap-2 text-primary-500">
            <span class="w-7 h-7 rounded-full gradient-primary text-white flex items-center justify-center font-bold text-xs">1</span>
            <span>Checkout</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5 bg-gray-200"></div>
        <div class="flex items-center gap-2 text-gray-400">
            <span class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center font-bold text-xs text-gray-400">2</span>
            <span>Pembayaran QRIS</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5 bg-gray-200"></div>
        <div class="flex items-center gap-2 text-gray-400">
            <span class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center font-bold text-xs text-gray-400">3</span>
            <span>Chat Admin & Akses</span>
        </div>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Checkout Produk Digital ⚡</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left: Contact & Payment Selection -->
            <div class="lg:col-span-2 space-y-5">

                <!-- WhatsApp Contact Info -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                            <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-800 text-base">Kontak WhatsApp Tujuan</h2>
                            <p class="text-xs text-gray-500">Lisensi / akun produk digital akan dikirimkan ke nomor WhatsApp ini</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-700 mb-1 block">Nama Pemesan</label>
                            <input type="text" name="shipping_name"
                                   value="{{ old('shipping_name', $user->name) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors"
                                   placeholder="Nama pemesan">
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-700 mb-1 block">Nomor WhatsApp Aktif *</label>
                            <input type="text" name="shipping_phone"
                                   value="{{ old('shipping_phone', $user->phone) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors @error('shipping_phone') border-red-400 @enderror"
                                   placeholder="08xxxxxxxxxx (WhatsApp)" required>
                            @error('shipping_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-credit-card text-primary-500"></i>
                        Pilih Metode Pembayaran
                    </h2>

                    <div class="space-y-3">
                        <!-- QRIS DANA Bisnis (ACTIVE & DEFAULT) -->
                        <label class="flex items-center gap-4 p-4 border-2 border-primary-500 bg-primary-50 rounded-xl cursor-pointer">
                            <input type="radio" name="payment_method" value="e_wallet" checked class="text-primary-500">
                            <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center text-white font-bold flex-shrink-0">
                                <i class="fas fa-qrcode text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-gray-800">QRIS DANA Bisnis (LALCLOUD STORE)</p>
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-green-500 text-white rounded-full">AKTIF & OTOMATIS</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">DANA, GoPay, OVO, ShopeePay, BCA, Mandiri, BRI, dll. (Auto Nominal)</p>
                            </div>
                        </label>

                        <!-- Bank Transfer (MAINTENANCE) -->
                        <div class="flex items-center gap-4 p-4 border border-gray-200 bg-gray-50 rounded-xl opacity-60 cursor-not-allowed">
                            <input type="radio" disabled class="text-gray-300">
                            <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center text-gray-500 font-bold flex-shrink-0">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-500">Transfer Bank Manual</p>
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700 rounded-full">MAINTENANCE</span>
                                </div>
                                <p class="text-xs text-gray-400">Gunakan QRIS DANA untuk proses otomatis</p>
                            </div>
                        </div>

                        <!-- E-Wallet Manual (MAINTENANCE) -->
                        <div class="flex items-center gap-4 p-4 border border-gray-200 bg-gray-50 rounded-xl opacity-60 cursor-not-allowed">
                            <input type="radio" disabled class="text-gray-300">
                            <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center text-gray-500 font-bold flex-shrink-0">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-500">Virtual Account / Lainnya</p>
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-700 rounded-full">MAINTENANCE</span>
                                </div>
                                <p class="text-xs text-gray-400">Gunakan QRIS DANA untuk proses otomatis</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-800 mb-3 text-sm flex items-center gap-2">
                        <i class="fas fa-sticky-note text-primary-500"></i>
                        Catatan Tambahan (Opsional)
                    </h2>
                    <textarea name="notes" rows="2"
                              class="w-full px-4 py-2.5 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 resize-none"
                              placeholder="Contoh: Kirimkan lisensi ke Email: mhilal044@gmail.com">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Right: Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20">
                    <h2 class="font-bold text-gray-800 mb-4 text-base">Ringkasan Produk</h2>

                    <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                        @foreach($carts as $cart)
                        <div class="flex gap-3 text-sm">
                            <img src="{{ $cart->product->image_url }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-800 font-medium line-clamp-2 text-xs">{{ $cart->product->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $cart->quantity }}x {{ $cart->product->formatted_effective_price }}</p>
                            </div>
                            <p class="font-semibold text-gray-800 text-xs whitespace-nowrap">{{ $cart->formatted_subtotal }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pengiriman Digital</span>
                            <span class="text-green-600 font-bold">GRATIS (Rp 0)</span>
                        </div>
                        <div class="border-t border-gray-100 pt-2.5 flex justify-between">
                            <span class="font-bold text-gray-800">Total Pembayaran</span>
                            <span class="font-bold text-xl text-primary-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full gradient-primary text-white font-bold py-3 rounded-xl mt-5 hover:opacity-90 transition-opacity flex items-center justify-center gap-2 text-sm shadow-md">
                        Lanjut ke Pembayaran QRIS <i class="fas fa-arrow-right"></i>
                    </button>

                    <p class="text-xs text-gray-400 text-center mt-3 flex items-center justify-center gap-1">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        Aman, Instan & Garansi Terjamin
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
