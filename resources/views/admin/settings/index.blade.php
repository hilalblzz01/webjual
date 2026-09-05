@extends('layouts.admin')

@section('title', 'Pengaturan QRIS Pembayaran')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl p-6 text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <i class="fas fa-qrcode text-2xl"></i>
                <h1 class="text-xl font-extrabold">Pengaturan QRIS Pembayaran</h1>
            </div>
            <p class="text-xs text-white/90">Ubah string QRIS DANA/Merchant & Nama Toko tanpa perlu edit kodingan lagi.</p>
        </div>
        <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">
            ⚡ Dynamic QRIS Auto-Nominal
        </div>
    </div>

    <!-- Form Setting QRIS -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Merchant Name -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    <i class="fas fa-store text-primary-500 mr-1"></i> Nama Merchant / Toko QRIS:
                </label>
                <input type="text" name="qris_merchant_name" value="{{ old('qris_merchant_name', $qrisMerchantName) }}"
                       placeholder="Contoh: LALCLOUD STORE" required
                       class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 bg-gray-50 font-semibold">
                <p class="text-[11px] text-gray-400 mt-1">Nama ini akan tampil di atas gambar QRIS yang di-scan oleh pembeli.</p>
            </div>

            <!-- QRIS String -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    <i class="fas fa-key text-primary-500 mr-1"></i> String QRIS Payload (Static String / DANA Bisnis):
                </label>
                <textarea name="qris_string" rows="5" required
                          placeholder="Masukkan string QRIS lengkap diawali 000201..."
                          class="w-full p-4 text-xs font-mono border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 bg-gray-50 leading-relaxed resize-y select-all">{{ old('qris_string', $qrisString) }}</textarea>
                <div class="mt-2 text-xs text-gray-500 bg-amber-50 p-3 rounded-xl border border-amber-200 space-y-1">
                    <p class="font-bold text-amber-800"><i class="fas fa-info-circle mr-1"></i> Cara Mendapatkan String QRIS:</p>
                    <p>1. Buka aplikasi DANA Bisnis / E-Wallet / Mobile Banking Anda.</p>
                    <p>2. Salin teks string QRIS (diawali <code class="bg-white px-1.5 py-0.5 rounded border text-amber-900 font-bold">000201...</code>).</p>
                    <p>3. Paste di kolom di atas lalu klik **Simpan Pengaturan**.</p>
                </div>
            </div>

            <!-- Preview Live Test -->
            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200">
                <p class="text-xs font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                    <i class="fas fa-eye text-primary-500"></i> Preview Hasil QRIS Live (Tes Auto Nominal Rp 20.000):
                </p>
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="w-36 h-36 bg-white p-2 border-2 border-primary-500 rounded-xl shadow-sm flex items-center justify-center">
                        @php
                        $previewDynamic = \App\Services\QrisService::generateDynamic(20000, $qrisString);
                        $previewUrl = \App\Services\QrisService::getQrImageUrl($previewDynamic);
                        @endphp
                        <img src="{{ $previewUrl }}" class="w-full h-full object-contain">
                    </div>
                    <div class="space-y-1 text-xs text-gray-600">
                        <p class="font-bold text-gray-800">🏪 {{ $qrisMerchantName }}</p>
                        <p class="text-primary-500 font-bold">Nominal Tes: Rp 20.000</p>
                        <p class="text-[10px] text-green-600 font-semibold"><i class="fas fa-check-circle mr-1"></i>CRC16 Auto-Calculated Valid</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full sm:w-auto px-8 py-3 gradient-primary text-white font-extrabold text-sm rounded-xl hover:opacity-90 transition-opacity shadow-md flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan Pengaturan QRIS
            </button>
        </form>
    </div>

</div>
@endsection
