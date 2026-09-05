@extends('layouts.admin')

@section('title', 'Stok Akun Digital')

@section('content')
<div class="space-y-6">

    <!-- Top Stats & Quick Add Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Left 2 Cols: Quick Add Digital Accounts -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-lg">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base">Input & Restok Akun Digital Instan</h3>
                    <p class="text-xs text-gray-500">Paste daftar akun 1 baris per akun dengan format: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 font-mono">email:password</code></p>
                </div>
            </div>

            <form action="{{ route('admin.products.restock', $products->first()->id ?? 1) }}" method="POST" id="quickRestockForm">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-xs font-bold text-gray-700 mb-1.5 block">Pilih Produk *</label>
                        <select name="product_id" id="productSelect" onchange="document.getElementById('quickRestockForm').action = '/admin/products/' + this.value + '/restock'"
                                class="w-full px-4 py-2.5 text-xs font-semibold border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 bg-gray-50/50">
                            @foreach($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }} (Sisa Stok: {{ $prod->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-xs font-bold text-gray-700 mb-1.5 block">Daftar Akun Baru (Paste per baris)</label>
                        <textarea name="digital_credentials" rows="5" required
                                  placeholder="email1@example.com:password1&#10;email2@example.com:password2&#10;email3@example.com:password3&#10;email4@example.com:password4"
                                  class="w-full px-4 py-2.5 text-xs font-mono border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 leading-relaxed"></textarea>
                    </div>
                </div>

                <button type="submit" class="gradient-primary text-white font-extrabold px-6 py-3 rounded-xl text-xs hover:opacity-90 transition-opacity flex items-center gap-2 shadow-md">
                    <i class="fas fa-box-open text-sm"></i> Simpan & Tambahkan ke Stok Produk
                </button>
            </form>
        </div>

        <!-- Right Col: Stock Summary Stats -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Akun Ready Siap Kirim</p>
                        <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($readyCount) }}</p>
                        <p class="text-[11px] text-gray-500 mt-1">Stok bersih di toko</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Akun Terpakai / Terkirim</p>
                        <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($usedCount) }}</p>
                        <p class="text-[11px] text-gray-500 mt-1">Terkirim ke pembeli</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-2xl flex items-center justify-center text-xl">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Digital Items List Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        
        <!-- Table Header & Filter -->
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-extrabold text-gray-900 text-base">Riwayat & Daftar Akun Digital</h3>
                <p class="text-xs text-gray-400">Daftar kredensial akun digital yang terdaftar di database</p>
            </div>

            <form action="{{ route('admin.digital-stock.index') }}" method="GET" class="flex items-center gap-2">
                <select name="product_id" class="px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                    <option value="">Semua Produk</option>
                    @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>🟢 Ready</option>
                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>🔴 Terpakai</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl text-xs hover:bg-gray-200">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left p-4 font-bold text-gray-500">No</th>
                        <th class="text-left p-4 font-bold text-gray-500">Produk</th>
                        <th class="text-left p-4 font-bold text-gray-500">Kredensial Akun (Email:Password)</th>
                        <th class="text-left p-4 font-bold text-gray-500">Status</th>
                        <th class="text-left p-4 font-bold text-gray-500">Info Invoice</th>
                        <th class="text-right p-4 font-bold text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-sans">
                    @forelse($digitalItems as $idx => $dItem)
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 text-gray-400 font-semibold">{{ $digitalItems->firstItem() + $idx }}</td>
                        <td class="p-4 font-bold text-gray-800">{{ $dItem->product->name }}</td>
                        <td class="p-4 font-mono font-bold text-gray-900 text-xs select-all">{{ $dItem->credentials }}</td>
                        <td class="p-4">
                            @if($dItem->is_used)
                            <span class="px-2.5 py-1 bg-red-50 text-red-600 font-bold text-[10px] rounded-full border border-red-200">
                                ?? Terpakai
                            </span>
                            @else
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-[10px] rounded-full border border-emerald-200">
                                ?? Ready
                            </span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-600">
                            @if($dItem->order)
                            <a href="{{ route('admin.orders.show', $dItem->order->id) }}" class="text-primary-500 hover:underline font-bold">
                                #{{ $dItem->order->invoice_number }} ({{ $dItem->order->user->name }})
                            </a>
                            <span class="block text-[10px] text-gray-400">{{ $dItem->used_at ? $dItem->used_at->format('d M H:i') : '' }}</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            @if(!$dItem->is_used)
                            <form action="{{ route('admin.digital-items.destroy', $dItem->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini dari stok?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg text-[11px] font-bold transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                            @else
                            <span class="text-gray-300 text-[11px]">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            Belum ada data akun digital. Gunakan form di atas untuk menambah stok akun baru!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($digitalItems->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $digitalItems->links() }}
        </div>
        @endif

    </div>

</div>
@endsection

