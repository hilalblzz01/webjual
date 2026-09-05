<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    /**
     * Store buyer product review and rating.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['paid', 'completed'])
            ->firstOrFail();

        if (!$order->items()->where('product_id', $request->product_id)->exists()) {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan untuk produk yang sudah dibeli.');
        }

        $existing = Review::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('order_id', $request->order_id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini pada pesanan ini.');
        }

        Review::create([
            'user_id'     => auth()->id(),
            'product_id'  => $request->product_id,
            'order_id'    => $request->order_id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
            'is_approved' => true,
        ]);

        // Forget cached products so ratings update in real-time
        Cache::forget('featured_products');
        Cache::forget('new_products');

        return back()->with('success', 'Terima kasih! Ulasan & rating ⭐' . $request->rating . ' Anda berhasil dikirim.');
    }
}
