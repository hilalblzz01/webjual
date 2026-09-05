@extends('layouts.admin')

@section('title', 'Detail User: ' . $user->name)

@section('content')
<div class="max-w-4xl">

    <div class="mb-5">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-primary-500 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar User
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- User Info -->
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3">
                <h2 class="font-bold text-gray-800 text-lg">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->role == 'admin' ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-3">Bergabung: {{ $user->created_at->format('d M Y') }}</p>
            </div>

            @if($user->phone || $user->address)
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3">Info Kontak</h3>
                @if($user->phone)
                <p class="text-sm text-gray-600"><i class="fas fa-phone text-gray-400 w-4 mr-2"></i>{{ $user->phone }}</p>
                @endif
                @if($user->address)
                <p class="text-sm text-gray-600 mt-2"><i class="fas fa-map-marker-alt text-gray-400 w-4 mr-2"></i>{{ $user->address }}</p>
                @endif
            </div>
            @endif

            @if($user->id !== auth()->id())
            <div class="space-y-2">
                <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full py-2.5 text-sm font-medium rounded-xl {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} transition-colors">
                        {{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Orders History -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Riwayat Pesanan ({{ $user->orders()->count() }})</h3>
                </div>

                @if($orders->isEmpty())
                <div class="p-10 text-center text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-3 block"></i>
                    Belum ada pesanan.
                </div>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $order->invoice_number }}</p>
                            <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y') }} · {{ $order->items->count() }} item</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-800 text-sm">{{ $order->formatted_total }}</p>
                            @php $colors = ['pending'=>'yellow','paid'=>'blue','processing'=>'orange','shipped'=>'purple','completed'=>'green','cancelled'=>'red']; @endphp
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $colors[$order->status]??'gray' }}-100 text-{{ $colors[$order->status]??'gray' }}-700">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="ml-4 text-xs text-primary-500 hover:underline">Detail</a>
                    </div>
                    @endforeach
                </div>

                @if($orders->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $orders->links('vendor.pagination.tailwind') }}
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
