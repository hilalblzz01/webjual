@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')

<div class="flex gap-3 mb-5">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama atau email..."
               class="flex-1 px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
        <select name="role" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
            <option value="">Semua Role</option>
            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="px-4 py-2 gradient-primary text-white rounded-xl text-sm hover:opacity-90">Filter</button>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-500">Pengguna</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Role</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Pesanan</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Status</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Bergabung</th>
                    <th class="text-right p-4 font-semibold text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="role" onchange="this.form.submit()"
                                    class="text-xs border border-gray-200 rounded-lg px-2 py-1 focus:outline-none focus:border-primary-500">
                                <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </form>
                    </td>
                    <td class="p-4 text-gray-600">{{ $user->orders_count }} pesanan</td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="p-4 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
                                Detail
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg {{ $user->is_active ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-green-600 bg-green-50 hover:bg-green-100' }}">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-gray-400">Belum ada pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $users->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
