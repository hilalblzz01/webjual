@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_orders']) }}</p>
                <p class="text-xs text-yellow-500 mt-1">{{ $stats['pending_orders'] }} pending</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-shopping-bag text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_products']) }}</p>
                <p class="text-xs text-green-500 mt-1">Aktif di toko</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-box text-green-500 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Pengguna</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_users']) }}</p>
                <p class="text-xs text-purple-500 mt-1">Customer aktif</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-users text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                <p class="text-xs text-green-500 mt-1">{{ $stats['completed_orders'] }} selesai</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center">
                <i class="fas fa-chart-line text-orange-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Revenue Wave Line Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Grafik Tren Penjualan</h3>
                <p class="text-xs text-gray-400">Pergerakan naik turun omset & penjualan toko</p>
            </div>
            <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-xs font-bold flex items-center gap-1.5 border border-orange-200">
                <i class="fas fa-chart-line"></i> Line Chart Naik Turun
            </span>
        </div>
        <div class="relative h-72">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <h3 class="font-bold text-gray-800 mb-4">Produk Terlaris</h3>
        <div class="space-y-3">
            @foreach($topProducts as $idx => $product)
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-primary-50 text-primary-500 text-xs font-bold flex items-center justify-center">{{ $idx + 1 }}</span>
                @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="w-10 h-10 rounded-xl object-cover">
                @else
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box text-gray-300 text-sm"></i>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($product->sold_count) }} terjual</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="bg-white rounded-2xl border border-gray-100 p-5 mt-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-gray-800">Pesanan Terbaru</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary-500 hover:underline">Lihat Semua</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left pb-3 font-semibold text-gray-500">Invoice</th>
                    <th class="text-left pb-3 font-semibold text-gray-500">Pelanggan</th>
                    <th class="text-left pb-3 font-semibold text-gray-500">Total</th>
                    <th class="text-left pb-3 font-semibold text-gray-500">Status</th>
                    <th class="text-left pb-3 font-semibold text-gray-500">Tanggal</th>
                    <th class="text-right pb-3 font-semibold text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentOrders as $order)
                <tr>
                    <td class="py-3 font-medium text-gray-800">{{ $order->invoice_number }}</td>
                    <td class="py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $order->user->avatar_url }}" class="w-7 h-7 rounded-full">
                            <span class="text-gray-600">{{ $order->user->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 font-semibold text-gray-800">{{ $order->formatted_total }}</td>
                    <td class="py-3">
                        @php $colors = ['pending'=>'yellow','paid'=>'blue','processing'=>'orange','shipped'=>'purple','completed'=>'green','cancelled'=>'red']; @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $colors[$order->status]??'gray' }}-100 text-{{ $colors[$order->status]??'gray' }}-700">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="py-3 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-primary-500 hover:underline text-xs">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const monthlyData = @json($monthlyRevenue);
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    const labels = monthlyData.map(d => months[d.month - 1] + ' ' + d.year);
    const revenues = monthlyData.map(d => d.revenue);

    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    // Create subtle orange gradient fill
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(255, 107, 0, 0.35)');
    gradient.addColorStop(1, 'rgba(255, 107, 0, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [{
                label: 'Omset Penjualan',
                data: revenues.length ? revenues : [18000, 36000, 54000, 72000, 90000, 108000],
                borderColor: '#FF6B00',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // Smooth naik-turun curve
                pointBackgroundColor: '#FF6B00',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            return ' Omset: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        color: '#64748B',
                        font: { size: 11, family: 'Inter' },
                        callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#64748B',
                        font: { size: 11, family: 'Inter' }
                    }
                }
            }
        }
    });
});
</script>
@endpush
