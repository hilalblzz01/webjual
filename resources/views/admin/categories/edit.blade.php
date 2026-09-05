@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('content')
<div class="max-w-xl">
    <div class="mb-5">
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-500 hover:text-primary-500 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
            <h3 class="font-bold text-gray-800">Edit Kategori: {{ $category->name }}</h3>

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1.5 block">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                       class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1.5 block">Kategori Parent</label>
                <select name="parent_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                    <option value="">Tidak ada (Kategori utama)</option>
                    @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1.5 block">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 resize-none">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1.5 block">Status</label>
                    <label class="flex items-center gap-3 mt-3">
                        <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="w-4 h-4 text-primary-500">
                        <span class="text-sm text-gray-600">Aktif</span>
                    </label>
                </div>
            </div>

            @if($category->image)
            <div>
                <p class="text-xs text-gray-500 mb-2">Gambar saat ini:</p>
                <img src="{{ asset('storage/'.$category->image) }}" class="w-20 h-20 rounded-xl object-cover">
            </div>
            @endif

            <div>
                <label class="text-sm font-medium text-gray-700 mb-1.5 block">Ganti Gambar</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-500 hover:file:bg-primary-100">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 gradient-primary text-white font-bold py-3 rounded-xl hover:opacity-90">
                    <i class="fas fa-save mr-2"></i>Perbarui
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-3 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
