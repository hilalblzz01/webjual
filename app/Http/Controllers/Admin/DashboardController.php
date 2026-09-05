<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders'    => Order::count(),
            'total_products'  => Product::count(),
            'total_users'     => User::where('role', 'customer')->count(),
            'total_revenue'   => Order::whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->sum('total_price'),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'completed_orders'=> Order::where('status', 'completed')->count(),
        ];

        $recentOrders = Order::with('user')->latest()->limit(10)->get();

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $monthlyRevenue = Order::select(
                DB::raw("cast(strftime('%m', created_at) as integer) as month"),
                DB::raw("cast(strftime('%Y', created_at) as integer) as year"),
                DB::raw("SUM(total_price) as revenue")
            )
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();
        } else {
            $monthlyRevenue = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();
        }

        $topProducts = Product::with('category')->orderBy('sold_count', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'monthlyRevenue', 'topProducts'));
    }
}
