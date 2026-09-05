@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-3xl">

    <div class="mb-5">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-primary-500 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Produk
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-5">

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Informasi Dasar</h3>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Nama Produk *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Deskripsi Singkat</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 resize-none">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Deskripsi Lengkap</label>
                        <textarea name="long_description" rows="5"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 resize-none">{{ old('long_description', $product->long_description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">Kategori *</label>
                            <select name="category_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">Status</label>
                            <select name="status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="out_of_stock" {{ old('status', $product->status) == 'out_of_stock' ? 'selected' : '' }}>Habis Stok</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">Berat (kg)</label>
                            <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" step="0.01"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Harga & Stok</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Harga Normal *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Harga Diskon</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                            <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Stok *</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                    </div>
                </div>
            </div>

            <!-- Stok Akun Digital (Auto-Delivery Credentials) -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-gray-800">Input Stok Akun Digital</h3>
                        <p class="text-xs text-gray-500">Format: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 font-mono">email:password</code> (1 akun per baris)</p>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 font-bold text-xs rounded-full border border-emerald-200">
                        {{ count($availableDigitalItems ?? []) }} Akun Siap Kirim
                    </span>
                </div>

                @php
                    $allDigitalItems = \App\Models\DigitalItem::with('order')->where('product_id', $product->id)->latest()->get();
                @endphp

                @if($allDigitalItems->count() > 0)
                <div class="mb-4 bg-gray-50 rounded-xl p-3.5 border border-gray-200 text-xs">
                    <p class="font-bold text-gray-800 mb-2">Daftar Akun Digital di Database ({{ $allDigitalItems->where('is_used', false)->count() }} Ready / {{ $allDigitalItems->count() }} Total):</p>
                    <div class="max-h-48 overflow-y-auto space-y-1.5 pr-1">
                        @foreach($allDigitalItems as $dItem)
                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-gray-200 font-mono">
                            <span class="text-gray-800 text-[11px] font-bold truncate flex-1 mr-2">{{ $dItem->credentials }}</span>
                            <div class="flex items-center gap-2">
                                @if($dItem->is_used)
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-sans font-bold rounded-full border border-red-200">
                                    🔴 Terpakai
                                </span>
                                @else
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-sans font-bold rounded-full border border-emerald-200">
                                    🟢 Ready
                                </span>
                                <form action="{{ route('admin.digital-items.destroy', $dItem->id) }}" method="POST" onsubmit="return confirm('Hapus akun ini dari stok?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs px-1.5 py-0.5 hover:bg-red-50 rounded" title="Hapus Akun Ini">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Tambah Akun Baru (Paste per baris)</label>
                    <textarea name="digital_credentials" rows="4" placeholder="account1@gmail.com:pwaccadobe1&#10;account2@gmail.com:pwaccadobe2"
                              class="w-full px-4 py-2.5 text-xs font-mono border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 leading-relaxed"></textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Setiap baris yang ditambahkan akan otomatis menambah stok produk dan siap dikirim otomatis ke pembeli.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Foto Produk</h3>

                @if($product->image)
                <div class="mb-4">
                    <p class="text-xs text-gray-500 mb-2">Foto saat ini:</p>
                    <img src="{{ $product->image_url }}" class="w-24 h-24 rounded-xl object-cover">
                </div>
                @endif

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Ganti Foto Utama</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-500 hover:file:bg-primary-100">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 gradient-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-2"></i>Perbarui Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 border border-gray-200 text-gray-600 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
