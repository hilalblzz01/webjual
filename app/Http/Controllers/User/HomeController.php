<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Cache::remember('featured_products', 1800, function () {
            return Product::with('category', 'reviews')->active()->orderBy('sold_count', 'desc')->limit(8)->get();
        });

        $newProducts = Cache::remember('new_products', 1800, function () {
            return Product::with('category', 'reviews')->active()->latest()->limit(8)->get();
        });

        $categories = Cache::remember('active_categories', 3600, function () {
            return Category::where('is_active', true)->whereNull('parent_id')->withCount('products')->orderBy('sort_order')->get();
        });

        return view('user.home', compact('featuredProducts', 'newProducts', 'categories'));
    }
}
