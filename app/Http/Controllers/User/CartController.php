<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();
        $total = $carts->sum(fn($c) => $c->subtotal);

        return view('user.cart.index', compact('carts', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_in_stock) {
            return back()->with('error', 'Produk ini sedang habis stok.');
        }

        if ($request->quantity > $product->stock) {
            return back()->with('error', "Stok tidak mencukupi. Tersedia: {$product->stock} item.");
        }

        $cartItem = Cart::where('user_id', auth()->id())->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Total jumlah melebihi stok yang tersedia.');
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            Cart::create(['user_id' => auth()->id(), 'product_id' => $product->id, 'quantity' => $request->quantity]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang! 🛒');
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) abort(403);

        $request->validate(['quantity' => 'required|integer|min:1|max:100']);

        if ($request->quantity > $cart->product->stock) {
            return response()->json(['success' => false, 'message' => 'Jumlah melebihi stok.'], 422);
        }

        $cart->update(['quantity' => $request->quantity]);

        $total = Cart::with('product')->where('user_id', auth()->id())->get()->sum(fn($i) => $i->subtotal);

        return response()->json([
            'success'  => true,
            'subtotal' => 'Rp ' . number_format($cart->subtotal, 0, ',', '.'),
            'total'    => 'Rp ' . number_format($total, 0, ',', '.'),
        ]);
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) abort(403);
        $cart->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function count()
    {
        return response()->json(['count' => Cart::where('user_id', auth()->id())->sum('quantity')]);
    }
}
