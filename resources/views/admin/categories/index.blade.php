@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')

<div class="flex justify-between items-center mb-5">
    <p class="text-sm text-gray-500">Total: <strong>{{ $categories->total() }}</strong> kategori</p>
    <a href="{{ route('admin.categories.create') }}"
       class="inline-flex items-center gap-2 gradient-primary text-white font-medium px-4 py-2 rounded-xl hover:opacity-90 transition-opacity text-sm">
        <i class="fas fa-plus"></i> Tambah Kategori
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left p-4 font-semibold text-gray-500">Kategori</th>
                <th class="text-left p-4 font-semibold text-gray-500">Parent</th>
                <th class="text-left p-4 font-semibold text-gray-500">Produk</th>
                <th class="text-left p-4 font-semibold text-gray-500">Status</th>
                <th class="text-right p-4 font-semibold text-gray-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($categories as $category)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="p-4">
                    <div class="flex items-center gap-3">
                        @if($category->image)
                        <img src="{{ asset('storage/'.$category->image) }}" class="w-10 h-10 rounded-xl object-cover">
                        @else
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tags text-primary-500 text-sm"></i>
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $category->name }}</p>
                            <p class="text-xs text-gray-400">{{ $category->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="p-4 text-gray-600">{{ $category->parent->name ?? '-' }}</td>
                <td class="p-4 text-gray-600">{{ $category->products_count }}</td>
                <td class="p-4">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="p-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                              onsubmit="return confirm('Hapus kategori ini?')">
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
                <td colspan="5" class="p-10 text-center text-gray-400">
                    Belum ada kategori. <a href="{{ route('admin.categories.create') }}" class="text-primary-500 hover:underline">Tambah sekarang</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($categories->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $categories->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
