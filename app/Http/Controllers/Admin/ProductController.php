<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search'))      $query->where('name', 'like', '%' . $request->search . '%');
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('status'))      $query->where('status', $request->status);

        $products   = $query->latest()->paginate(15)->appends($request->all());
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'long_description'=> 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'sale_price'      => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'sku'             => 'nullable|string|unique:products',
            'category_id'     => 'required|exists:categories,id',
            'image'           => 'nullable|file|image|mimes:png,jpg,jpeg,webp|max:5120',
            'images.*'        => 'nullable|file|image|mimes:png,jpg,jpeg,webp|max:5120',
            'status'          => 'required|in:active,inactive,out_of_stock',
            'weight'          => 'nullable|numeric|min:0',
        ]);

        $validated['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $validated['image'] = \App\Services\ImageCompressor::compressAndStore($request->file('image'), 'products');
        }

        if ($request->hasFile('images')) {
            $validated['images'] = collect($request->file('images'))
                ->map(fn($img) => \App\Services\ImageCompressor::compressAndStore($img, 'products'))
                ->toArray();
        }

        $product = Product::create($validated);

        if ($request->filled('digital_credentials')) {
            $lines = explode("\n", $request->digital_credentials);
            $addedCount = 0;
            foreach ($lines as $line) {
                $clean = trim($line);
                if ($clean !== '') {
                    \App\Models\DigitalItem::create([
                        'product_id'  => $product->id,
                        'credentials' => $clean,
                        'is_used'     => false,
                    ]);
                    $addedCount++;
                }
            }
            if ($addedCount > 0) {
                $product->update(['stock' => $addedCount]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $availableDigitalItems = \App\Models\DigitalItem::where('product_id', $product->id)
            ->where('is_used', false)
            ->get();
            
        return view('admin.products.edit', compact('product', 'categories', 'availableDigitalItems'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'long_description'=> 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'sale_price'      => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'sku'             => 'nullable|string|unique:products,sku,' . $product->id,
            'category_id'     => 'required|exists:categories,id',
            'image'           => 'nullable|file|image|mimes:png,jpg,jpeg,webp|max:5120',
            'images.*'        => 'nullable|file|image|mimes:png,jpg,jpeg,webp|max:5120',
            'status'          => 'required|in:active,inactive,out_of_stock',
            'weight'          => 'nullable|numeric|min:0',
            'digital_credentials' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $validated['image'] = \App\Services\ImageCompressor::compressAndStore($request->file('image'), 'products');
        }

        if ($request->hasFile('images')) {
            if ($product->images) {
                foreach ($product->images as $old) Storage::disk('public')->delete($old);
            }
            $validated['images'] = collect($request->file('images'))
                ->map(fn($img) => \App\Services\ImageCompressor::compressAndStore($img, 'products'))
                ->toArray();
        }

        $product->update($validated);

        if ($request->filled('digital_credentials')) {
            $lines = explode("\n", $request->digital_credentials);
            $addedCount = 0;
            foreach ($lines as $line) {
                $clean = trim($line);
                if ($clean !== '') {
                    \App\Models\DigitalItem::create([
                        'product_id'  => $product->id,
                        'credentials' => $clean,
                        'is_used'     => false,
                    ]);
                    $addedCount++;
                }
            }
            if ($addedCount > 0) {
                $product->increment('stock', $addedCount);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->image)  Storage::disk('public')->delete($product->image);
        if ($product->images) foreach ($product->images as $img) Storage::disk('public')->delete($img);

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function destroyDigitalItem(int $id)
    {
        $item = \App\Models\DigitalItem::findOrFail($id);
        $productId = $item->product_id;
        $wasUnused = !$item->is_used;
        $item->delete();

        if ($wasUnused) {
            Product::where('id', $productId)->decrement('stock', 1);
        }

        return back()->with('success', 'Akun digital berhasil dihapus dari stok!');
    }

    public function restock(Request $request, Product $product)
    {
        $request->validate([
            'digital_credentials' => 'required|string',
        ], [
            'digital_credentials.required' => 'Masukkan minimal 1 baris akun digital (email:password)!',
        ]);

        $lines = explode("\n", $request->digital_credentials);
        $addedCount = 0;
        foreach ($lines as $line) {
            $clean = trim($line);
            if ($clean !== '') {
                \App\Models\DigitalItem::create([
                    'product_id'  => $product->id,
                    'credentials' => $clean,
                    'is_used'     => false,
                ]);
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            $product->increment('stock', $addedCount);
            if ($product->status === 'out_of_stock') {
                $product->update(['status' => 'active']);
            }
        }

        return back()->with('success', "🎉 Berhasil menambahkan {$addedCount} akun digital ke stok {$product->name}!");
    }

    public function digitalStock(Request $request)
    {
        $products = Product::where('status', 'active')->get();

        $query = \App\Models\DigitalItem::with(['product', 'order.user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'ready') {
                $query->where('is_used', false);
            } elseif ($request->status === 'used') {
                $query->where('is_used', true);
            }
        }

        $digitalItems = $query->latest()->paginate(20)->appends($request->all());

        $readyCount = \App\Models\DigitalItem::where('is_used', false)->count();
        $usedCount  = \App\Models\DigitalItem::where('is_used', true)->count();

        return view('admin.products.digital_stock', compact('products', 'digitalItems', 'readyCount', 'usedCount'));
    }
}
