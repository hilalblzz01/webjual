@extends('layouts.admin')

@section('title', 'Detail & Chat Pesanan #' . $order->invoice_number)

@section('content')
<div class="max-w-5xl">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-primary-500 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Pesanan
        </a>
        <a href="{{ route('admin.orders.invoice', $order->id) }}" class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 text-sm font-medium rounded-xl hover:bg-green-100 transition-colors">
            <i class="fas fa-file-pdf"></i> Download Invoice PDF
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Left: Order Info & Admin Chat Center -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Status Control -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3">Update Status Pesanan</h3>
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="flex gap-3">
                    @csrf
                    @method('PUT')
                    <select name="status" class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                        @foreach(\App\Models\Order::$statusLabels as $key => $label)
                        <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="gradient-primary text-white font-medium px-5 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm">
                        Simpan Status
                    </button>
                </form>
            </div>

            <!-- Items Purchased -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3">Produk Dipesan</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex gap-3 py-2 border-b border-gray-50 last:border-0">
                        @if($item->product_image)
                        <img src="{{ asset('storage/'.$item->product_image) }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                        @else
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-box text-gray-300"></i>
                        </div>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }} x {{ $item->formatted_price }}</p>
                        </div>
                        <p class="text-sm font-bold text-gray-800">{{ $item->formatted_subtotal }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- FAST ACTION BANNER: PEMBAYARAN DITERIMA & KIRIM PRODUK (2x CONFIRMATION) -->
            <div x-data="{ confirmStep: 0 }" class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-5 text-white shadow-lg border border-emerald-500/30">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 bg-white/20 backdrop-blur-md text-white rounded-full text-[10px] font-extrabold uppercase tracking-wide">
                                <i class="fas fa-bolt mr-1"></i> Aksi Cepat Admin
                            </span>
                            <span class="text-xs text-emerald-100 font-medium">Status: <strong class="text-white">{{ $order->status_label }}</strong></span>
                        </div>
                        <h4 class="font-black text-base text-white">Konfirmasi Pembayaran & Kirim Produk</h4>
                        <p class="text-xs text-emerald-100 leading-snug">Verifikasi pembayaran QRIS DANA & kirim lisensi digital otomatis ke pembeli ini.</p>
                    </div>

                    @if(in_array($order->status, ['completed', 'paid']))
                    <button type="button" disabled class="px-5 py-3 bg-emerald-800/80 text-emerald-200 font-bold text-xs rounded-xl shadow cursor-not-allowed flex items-center gap-2 border border-emerald-700/50">
                        <i class="fas fa-check-circle text-emerald-300 text-sm"></i> Pembayaran Diterima & Produk Terkirim
                    </button>
                    @else
                    <button @click="confirmStep = 1" type="button" class="px-5 py-3 bg-white text-emerald-700 font-black text-xs rounded-xl shadow-lg hover:bg-emerald-50 transition-all flex items-center gap-2 flex-shrink-0 transform hover:-translate-y-0.5 active:translate-y-0">
                        <i class="fas fa-check-circle text-emerald-600 text-base"></i> Pembayaran Diterima & Kirim Barang
                    </button>
                    @endif
                </div>

                <!-- 2-STEP CONFIRMATION MODAL OVERLAY -->
                <template x-teleport="body">
                    <div x-show="confirmStep > 0" class="fixed inset-0 bg-black/70 backdrop-blur-md z-[9999] flex items-center justify-center p-4" x-cloak>
                        
                        <!-- STEP 1 MODAL: VALIDASI PEMBAYARAN -->
                        <div x-show="confirmStep === 1" @click.away="confirmStep = 0" class="bg-white rounded-2xl p-6 max-w-md w-full text-gray-800 shadow-2xl relative border-2 border-emerald-500 animate-in fade-in zoom-in duration-200">
                            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-3 text-2xl mx-auto shadow-inner">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h3 class="font-extrabold text-lg text-center text-gray-900 mb-1">Konfirmasi 1 dari 2</h3>
                            <p class="text-xs text-center text-gray-500 mb-4">Verifikasi Masuknya Dana E-Wallet / QRIS DANA</p>

                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-xs text-gray-700 space-y-2 mb-5">
                                <div class="flex justify-between border-b border-emerald-200/60 pb-1.5">
                                    <span class="text-gray-500">Invoice:</span>
                                    <span class="font-bold text-gray-900">{{ $order->invoice_number }}</span>
                                </div>
                                <div class="flex justify-between border-b border-emerald-200/60 pb-1.5">
                                    <span class="text-gray-500">Pembeli:</span>
                                    <span class="font-bold text-gray-900">{{ $order->user->name }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-0.5">
                                    <span class="text-gray-500">Nominal Transfer:</span>
                                    <span class="text-emerald-700 font-extrabold text-base">{{ $order->formatted_total }}</span>
                                </div>
                                <p class="text-[11px] text-emerald-900 pt-2 font-semibold border-t border-emerald-200 leading-relaxed">
                                    ⚠️ Apakah Anda sudah memeriksa mutasi saldo dan memastikan dana sebesar <strong class="underline">{{ $order->formatted_total }}</strong> benar-benar telah MASUK & VALID?
                                </p>
                            </div>

                            <div class="flex gap-3">
                                <button @click="confirmStep = 0" type="button" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors">
                                    Batal
                                </button>
                                <button @click="confirmStep = 2" type="button" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-md transition-all">
                                    <span>Lanjut Konfirmasi 2</span>
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 2 MODAL: FINAL CONFIRMATION & AUTOMATED DELIVERY -->
                        <div x-show="confirmStep === 2" @click.away="confirmStep = 0" class="bg-white rounded-2xl p-6 max-w-md w-full text-gray-800 shadow-2xl relative border-2 border-orange-500 animate-in fade-in zoom-in duration-200">
                            <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-3 text-2xl mx-auto shadow-inner">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <h3 class="font-extrabold text-lg text-center text-gray-900 mb-1">Konfirmasi 2 dari 2 (FINAL)</h3>
                            <p class="text-xs text-center text-gray-500 mb-4">Proses Pengiriman Produk Digital ke Chat Pembeli</p>

                            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-xs text-gray-700 space-y-2 mb-5">
                                <p class="font-extrabold text-orange-900 text-xs">Aksi ini akan menjalankan otomatis:</p>
                                <ul class="list-disc pl-4 space-y-1.5 text-[11px] text-gray-700 font-medium">
                                    <li>Mengubah status pesanan menjadi <span class="bg-green-100 text-green-800 px-1.5 py-0.5 rounded font-bold">SELESAI (COMPLETED)</span>.</li>
                                    <li>Mengambil / generate stok kredensial lisensi akun digital.</li>
                                    <li>Mengirimkan pesan otomatis berisi detail akun digital ke percakapan chat pembeli.</li>
                                </ul>
                            </div>

                            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                
                                <div class="flex gap-3">
                                    <button @click="confirmStep = 0" type="button" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:opacity-90 text-white font-extrabold rounded-xl text-xs flex items-center justify-center gap-1.5 shadow-lg transition-all">
                                        <i class="fas fa-check-circle"></i>
                                        <span>KIRIM PRODUK SEKARANG</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </template>
            </div>

            <!-- ADMIN LIVE CHAT WITH BUYER -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-comments text-primary-500 text-lg"></i>
                        <h3 class="font-bold text-gray-800 text-base">Percakapan / Chat Konfirmasi Pembeli</h3>
                    </div>
                    <span class="text-xs text-gray-400">ID Invoice: {{ $order->invoice_number }}</span>
                </div>

                <!-- Chat Messages History -->
                <div class="space-y-3 max-h-96 overflow-y-auto p-3 bg-gray-50 rounded-xl mb-4">
                    @forelse($order->chats as $chat)
                    <div class="flex flex-col {{ $chat->sender_role === 'admin' ? 'items-end' : 'items-start' }}">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="text-xs font-bold text-gray-700">
                                {{ $chat->sender_role === 'admin' ? 'Saya (Admin)' : $chat->user->name }}
                            </span>
                            <span class="text-[10px] text-gray-400">{{ $chat->created_at->format('d M H:i') }}</span>
                        </div>

                        <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-xs shadow-md font-bold
                            {{ $chat->sender_role === 'admin' ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white rounded-br-none' : 'bg-white border-2 border-gray-300 text-gray-900 rounded-bl-none' }}">
                            <p class="leading-relaxed whitespace-pre-wrap {{ $chat->sender_role === 'admin' ? 'text-white' : 'text-gray-900' }}">{{ $chat->message }}</p>

                            @if($chat->attachment)
                            <div class="mt-2 pt-2 border-t {{ $chat->sender_role === 'admin' ? 'border-white/30' : 'border-gray-200' }}">
                                <a href="{{ asset('storage/' . $chat->attachment) }}" target="_blank" class="block">
                                    <img src="{{ asset('storage/' . $chat->attachment) }}" class="max-h-48 rounded-lg object-cover border border-black/20">
                                    <span class="text-[10px] underline mt-1 block font-bold {{ $chat->sender_role === 'admin' ? 'text-white' : 'text-blue-600' }}"><i class="fas fa-external-link-alt mr-1"></i>Buka Bukti Transfer Full</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-6">Belum ada chat dari pembeli ini.</p>
                    @endforelse
                </div>

                <!-- Reply Form -->
                <form action="{{ route('admin.orders.chat', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-center gap-2">
                        <label class="p-2.5 text-gray-500 hover:text-primary-500 cursor-pointer rounded-xl hover:bg-gray-100 transition-colors" title="Lampirkan File / Akun">
                            <i class="fas fa-paperclip text-base"></i>
                            <input type="file" name="attachment" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </label>
                        <input type="text" name="message" placeholder="Kirim balasan / lisensi akun Adobe ke pembeli..."
                               class="flex-1 px-4 py-2.5 text-xs text-gray-900 bg-white border border-gray-300 rounded-xl focus:outline-none focus:border-primary-500 font-medium placeholder-gray-400 shadow-sm">
                        <button type="submit" class="gradient-primary text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:opacity-90 shadow-md">
                            Kirim <i class="fas fa-paper-plane ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Sidebar Info -->
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">Info Pembeli</h3>
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ $order->user->avatar_url }}" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $order->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-700 font-semibold"><i class="fab fa-whatsapp text-green-500 mr-1"></i> {{ $order->shipping_phone }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">Pembayaran</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-semibold text-gray-800">{{ $order->payment_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Pembayaran</span>
                        <span class="font-bold text-primary-500 text-sm">{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            @if($order->payment_proof)
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">Bukti Transfer Terbaru</h3>
                <a href="{{ asset('storage/'.$order->payment_proof) }}" target="_blank">
                    <img src="{{ asset('storage/'.$order->payment_proof) }}" class="w-full rounded-xl object-cover border border-gray-200">
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
