<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        \App\Models\Order::cancelExpiredOrders();
        $query = Product::with('category', 'reviews')->active();

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) $query->where('category_id', $category->id);
        }

        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);
        if ($request->filled('search')) $query->search($request->search);

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular'    => $query->orderBy('sold_count', 'desc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->appends($request->all());
        $categories = Category::where('is_active', true)->whereNull('parent_id')->get();

        return view('user.products.index', compact('products', 'categories', 'sort'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['category', 'reviews.user'])
            ->where('slug', $slug)->active()->firstOrFail();

        $relatedProducts = Product::with('reviews')->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(6)->get();

        $isWishlisted = auth()->check()
            ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
            : false;

        return view('user.products.show', compact('product', 'relatedProducts', 'isWishlisted'));
    }

    public function category(string $slug)
    {
        $category   = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products   = Product::with('category', 'reviews')->active()->where('category_id', $category->id)->paginate(12);
        $categories = Category::where('is_active', true)->whereNull('parent_id')->get();

        return view('user.products.category', compact('category', 'products', 'categories'));
    }
}
