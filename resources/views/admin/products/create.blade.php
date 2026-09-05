@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-3xl">

    <div class="mb-5">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-primary-500 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Produk
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-5">

            <!-- Basic Info -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Informasi Dasar</h3>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Nama Produk *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 @error('name') border-red-400 @enderror"
                               placeholder="Nama produk">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Deskripsi Singkat</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 resize-none"
                                  placeholder="Deskripsi singkat produk...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Deskripsi Lengkap</label>
                        <textarea name="long_description" rows="5"
                                  class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 resize-none"
                                  placeholder="Deskripsi lengkap, spesifikasi, cara penggunaan...">{{ old('long_description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">Kategori *</label>
                            <select name="category_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 @error('category_id') border-red-400 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">SKU (Opsional)</label>
                            <input type="text" name="sku" value="{{ old('sku') }}"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500"
                                   placeholder="PROD-001">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">Status *</label>
                            <select name="status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Habis Stok</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1.5 block">Berat (kg)</label>
                            <input type="number" name="weight" value="{{ old('weight') }}" step="0.01"
                                   class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500"
                                   placeholder="0.5">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Harga & Stok</h3>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Harga Normal *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                            <input type="number" name="price" value="{{ old('price') }}" min="0"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 @error('price') border-red-400 @enderror"
                                   placeholder="100000">
                        </div>
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Harga Diskon</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                            <input type="number" name="sale_price" value="{{ old('sale_price') }}" min="0"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500"
                                   placeholder="80000">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Stok *</label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"
                               class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 @error('stock') border-red-400 @enderror"
                               placeholder="100">
                        @error('stock')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Stok Akun Digital (Auto-Delivery Credentials) -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="mb-3">
                    <h3 class="font-bold text-gray-800">Input Stok Akun Digital (Format Langsung)</h3>
                    <p class="text-xs text-gray-500">Paste kredensial akun digital 1 baris per akun dengan format: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 font-mono">email:password</code></p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Daftar Akun Digital (Paste per baris)</label>
                    <textarea name="digital_credentials" rows="5" placeholder="email1@example.com:password1&#10;email2@example.com:password2&#10;email3@example.com:password3&#10;email4@example.com:password4"
                              class="w-full px-4 py-2.5 text-xs font-mono border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 leading-relaxed">{{ old('digital_credentials') }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Sistem akan otomatis menghitung stok awal berdasarkan total baris akun yang Anda masukkan di atas.</p>
                </div>
            </div>

            <!-- Images -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Foto Produk</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Foto Utama</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary-300 transition-colors cursor-pointer" onclick="document.getElementById('mainImage').click()">
                            <img id="mainImagePreview" src="" alt="" class="hidden w-32 h-32 object-cover rounded-xl mx-auto mb-2">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2 block" id="mainImageIcon"></i>
                            <p class="text-sm text-gray-500">Klik untuk upload</p>
                            <p class="text-xs text-gray-400">JPG, PNG, WebP max 2MB</p>
                            <input type="file" id="mainImage" name="image" accept="image/*" class="hidden"
                                   onchange="previewImage(this, 'mainImagePreview', 'mainImageIcon')">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Foto Tambahan</label>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary-300 transition-colors cursor-pointer" onclick="document.getElementById('moreImages').click()">
                            <i class="fas fa-images text-3xl text-gray-300 mb-2 block"></i>
                            <p class="text-sm text-gray-500">Upload beberapa foto</p>
                            <p class="text-xs text-gray-400">Maks 5 foto</p>
                            <input type="file" id="moreImages" name="images[]" accept="image/*" multiple class="hidden"
                                   onchange="previewMultiple(this)">
                        </div>
                        <div id="moreImagesPreview" class="flex gap-2 mt-2 flex-wrap"></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="flex-1 gradient-primary text-white font-bold py-3 rounded-xl hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-2"></i>Simpan Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 border border-gray-200 text-gray-600 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input, previewId, iconId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById(previewId);
            const icon = document.getElementById(iconId);
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewMultiple(input) {
    const container = document.getElementById('moreImagesPreview');
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-16 h-16 rounded-xl object-cover';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
