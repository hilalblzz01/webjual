@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content')

<!-- Toolbar -->
<div class="flex flex-col sm:flex-row gap-3 mb-5">
    <form action="{{ route('admin.products.index') }}" method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari produk..."
               class="flex-1 px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
        <select name="category_id" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Habis</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm hover:bg-gray-200 transition-colors">
            <i class="fas fa-search"></i>
        </button>
    </form>

    <a href="{{ route('admin.products.create') }}"
       class="inline-flex items-center gap-2 gradient-primary text-white font-medium px-4 py-2 rounded-xl hover:opacity-90 transition-opacity text-sm whitespace-nowrap">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-500">Produk</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Kategori</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Harga</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Stok</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Status</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Terjual</th>
                    <th class="text-right p-4 font-semibold text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->image_url }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                            <div>
                                <p class="font-medium text-gray-800 line-clamp-1">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">SKU: {{ $product->sku ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-gray-600">{{ $product->category->name }}</td>
                    <td class="p-4">
                        <p class="font-semibold text-gray-800">{{ $product->formatted_effective_price }}</p>
                        @if($product->sale_price)
                        <p class="text-xs text-gray-400 line-through">{{ $product->formatted_price }}</p>
                        @endif
                    </td>
                    <td class="p-4">
                        @php
                            $readyAccounts = \App\Models\DigitalItem::where('product_id', $product->id)->where('is_used', false)->count();
                        @endphp
                        <div class="flex flex-col items-start" x-data="{ openRestock: false }">
                            <span class="font-bold {{ $product->stock <= 0 ? 'text-red-500' : 'text-gray-800' }}">
                                {{ $product->stock }} Item
                            </span>
                            <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full w-fit mt-0.5 border border-emerald-200">
                                🟢 {{ $readyAccounts }} Akun Ready
                            </span>

                            <!-- Quick Restock Button -->
                            <button @click="openRestock = true" type="button" class="mt-2 inline-flex items-center gap-1 text-[11px] font-extrabold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 px-2.5 py-1 rounded-lg transition-colors border border-emerald-300 shadow-sm">
                                <i class="fas fa-plus-circle text-xs"></i> Tambah Stok Akun
                            </button>

                            <!-- Restock Quick Modal -->
                            <template x-teleport="body">
                                <div x-show="openRestock" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[9999] flex items-center justify-center p-4" x-cloak>
                                    <div @click.away="openRestock = false" class="bg-white rounded-2xl p-6 max-w-md w-full text-gray-800 shadow-2xl relative border-2 border-emerald-500 text-left">
                                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-box-open text-emerald-600 text-lg"></i>
                                                <h3 class="font-extrabold text-base text-gray-900">Tambah Stok Akun Digital</h3>
                                            </div>
                                            <button @click="openRestock = false" class="text-gray-400 hover:text-gray-600 text-sm"><i class="fas fa-times"></i></button>
                                        </div>

                                        <form action="{{ route('admin.products.restock', $product->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-4">
                                                <p class="text-xs text-gray-500 mb-2">Produk: <strong class="text-gray-900">{{ $product->name }}</strong> (Kategori: {{ $product->category->name }})</p>
                                                <label class="text-xs font-bold text-gray-700 mb-1 block">Paste Daftar Akun (Format: <code class="bg-gray-100 px-1 rounded">email:password</code> per baris)</label>
                                                <textarea name="digital_credentials" rows="6" required placeholder="email1@example.com:password1&#10;email2@example.com:password2&#10;email3@example.com:password3"
                                                          class="w-full px-3 py-2 text-xs font-mono border border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 leading-relaxed"></textarea>
                                            </div>

                                            <div class="flex gap-2">
                                                <button @click="openRestock = false" type="button" class="flex-1 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl text-xs">
                                                    Batal
                                                </button>
                                                <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs flex items-center justify-center gap-1 shadow">
                                                    <i class="fas fa-plus-circle"></i> Tambah ke Stok
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </td>
                    <td class="p-4">
                        @php $statusMap = ['active'=>['bg-green-100','text-green-700','Aktif'],'inactive'=>['bg-gray-100','text-gray-600','Nonaktif'],'out_of_stock'=>['bg-red-100','text-red-600','Habis']]; $s = $statusMap[$product->status] ?? ['bg-gray-100','text-gray-600',$product->status]; @endphp
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $s[0] }} {{ $s[1] }}">{{ $s[2] }}</span>
                    </td>
                    <td class="p-4 text-gray-600">{{ number_format($product->sold_count) }}</td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-3 block"></i>
                        Belum ada produk. <a href="{{ route('admin.products.create') }}" class="text-primary-500 hover:underline">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $products->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
