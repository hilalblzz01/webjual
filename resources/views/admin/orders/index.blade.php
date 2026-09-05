@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')

<!-- Status Quick Tabs -->
<div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
    <a href="{{ route('admin.orders.index') }}"
       class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ !request('status') ? 'bg-primary-500 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
        📋 Semua Pesanan
    </a>
    <a href="{{ route('admin.orders.index') }}?status=pending"
       class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' }}">
        ⏳ Menunggu Pembayaran (Pending)
    </a>
    <a href="{{ route('admin.orders.index') }}?status=completed"
       class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request('status') == 'completed' ? 'bg-green-600 text-white shadow-md' : 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100' }}">
        ✅ Sukses & Selesai
    </a>
    <a href="{{ route('admin.orders.index') }}?status=cancelled"
       class="flex-shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request('status') == 'cancelled' ? 'bg-red-500 text-white shadow-md' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' }}">
        ❌ Dibatalkan
    </a>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-2xl border border-gray-100 p-4 mb-5">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari invoice..."
               class="px-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 flex-1 min-w-40">
        <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
            <option value="">Semua Status</option>
            @foreach($statusLabels as $key => $label)
            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500">
        <button type="submit" class="px-4 py-2 gradient-primary text-white rounded-xl text-sm hover:opacity-90">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm hover:bg-gray-50">Reset</a>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left p-4 font-semibold text-gray-500">Invoice</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Pelanggan</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Total</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Pembayaran</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Status</th>
                    <th class="text-left p-4 font-semibold text-gray-500">Tanggal</th>
                    <th class="text-right p-4 font-semibold text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="p-4 font-medium text-gray-800">{{ $order->invoice_number }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ $order->user->avatar_url }}" class="w-8 h-8 rounded-full">
                            <div>
                                <p class="font-medium text-gray-800 text-xs">{{ $order->user->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $order->items->count() }} item</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 font-semibold text-gray-800">{{ $order->formatted_total }}</td>
                    <td class="p-4 text-gray-600 text-xs">{{ $order->payment_label }}</td>
                    <td class="p-4">
                        @php $colors = ['pending'=>'yellow','paid'=>'blue','processing'=>'orange','shipped'=>'purple','completed'=>'green','cancelled'=>'red']; @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $colors[$order->status]??'gray' }}-100 text-{{ $colors[$order->status]??'gray' }}-700">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="p-4 text-gray-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.orders.show', $order->id) }}"
                               class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                Detail
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}"
                               class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-gray-400">Belum ada pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $orders->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection
