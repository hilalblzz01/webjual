<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('product.category')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $wishlisted = false;
            $message    = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'product_id' => $request->product_id]);
            $wishlisted = true;
            $message    = 'Produk ditambahkan ke wishlist! ❤️';
        }

        if ($request->ajax()) {
            return response()->json(['wishlisted' => $wishlisted, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function destroy(int $id)
    {
        Wishlist::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('success', 'Produk dihapus dari wishlist.');
    }
}
