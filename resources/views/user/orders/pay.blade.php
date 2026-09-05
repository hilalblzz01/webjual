@extends('layouts.app')

@section('title', 'Bayar QRIS - Step 2 #' . $order->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <!-- Step Progress Indicator -->
    <div class="flex items-center justify-center gap-2 sm:gap-4 mb-8 text-xs sm:text-sm font-semibold">
        <div class="flex items-center gap-2 text-green-600">
            <span class="w-7 h-7 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs"><i class="fas fa-check text-xs"></i></span>
            <span>Checkout</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5 bg-primary-500"></div>
        <div class="flex items-center gap-2 text-primary-500">
            <span class="w-7 h-7 rounded-full gradient-primary text-white flex items-center justify-center font-bold text-xs">2</span>
            <span>Pembayaran QRIS</span>
        </div>
        <div class="w-8 sm:w-12 h-0.5 bg-gray-200"></div>
        <div class="flex items-center gap-2 text-gray-400">
            <span class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center font-bold text-xs text-gray-400">3</span>
            <span>Chat Admin & Akses</span>
        </div>
    </div>

    <!-- QRIS Card -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 shadow-sm text-center mb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary-50 text-primary-600 rounded-full text-xs font-bold mb-3">
            <i class="fas fa-bolt"></i> PEMBAYARAN DANA QRIS OTOMATIS
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-1">Scan QRIS DANA Bisnis</h1>
        <p class="text-xs text-gray-500 mb-6">Silakan scan kode QRIS di bawah menggunakan DANA, GoPay, OVO, ShopeePay, atau Mobile Banking</p>

        @php
        $dynamicQris = \App\Services\QrisService::generateDynamic((int) $order->total_price);
        $qrImageUrl  = \App\Services\QrisService::getQrImageUrl($dynamicQris);
        @endphp

        <!-- QR Code Container -->
        <div class="flex flex-col items-center justify-center mb-6">
            <div class="bg-white p-4 rounded-2xl border-2 border-primary-500 shadow-lg inline-block">
                <p class="text-xs font-bold text-gray-700 mb-2">🏪 {{ \App\Services\QrisService::getMerchantName() }}</p>
                <div class="w-64 h-64 mx-auto">
                    <img src="{{ $qrImageUrl }}" alt="QRIS DANA {{ $order->invoice_number }}" class="w-full h-full object-contain">
                </div>
                <div class="mt-3 bg-primary-50 p-2.5 rounded-xl border border-primary-100">
                    <p class="text-xs text-gray-500">Total Nominal Pembayaran:</p>
                    <p class="text-2xl font-black text-primary-500">{{ $order->formatted_total }}</p>
                    <p class="text-[10px] text-green-600 font-semibold mt-1">✓ Nominal otomatis terisi saat di-scan</p>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="max-w-md mx-auto bg-gray-50 rounded-2xl p-5 border border-gray-200 text-left">
            <h3 class="font-bold text-gray-800 text-sm mb-2 flex items-center gap-2">
                <i class="fas fa-cloud-upload-alt text-primary-500"></i>
                Upload Bukti Transfer Pembayaran
            </h3>
            <p class="text-xs text-gray-500 mb-4">Setelah melakukan pembayaran via DANA/QRIS, upload bukti transfer di bawah ini untuk terhubung langsung ke Chat Admin:</p>

            <form action="{{ route('orders.proof', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Pilih Foto Screenshot Bukti Transfer *</label>
                    <input type="file" name="payment_proof" accept="image/*" required
                           class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-500 file:text-white hover:file:opacity-90 cursor-pointer">
                    @error('payment_proof')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full gradient-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity flex items-center justify-center gap-2 text-sm shadow-md">
                    Kirim Bukti & Buka Chat Admin <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
