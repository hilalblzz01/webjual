<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkout()
    {
        Order::cancelExpiredOrders();
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal     = $carts->sum(fn($c) => $c->subtotal);
        $shippingCost = 0; // Bebas Ongkir untuk Produk Digital
        $total        = $subtotal + $shippingCost;
        $user         = auth()->user();

        return view('user.checkout.index', compact('carts', 'subtotal', 'shippingCost', 'total', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name'  => 'nullable|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:transfer_bank,e_wallet,cod',
            'notes'          => 'nullable|string|max:500',
        ]);

        $carts = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        foreach ($carts as $cart) {
            if ($cart->quantity > $cart->product->stock) {
                return back()->with('error', "Stok {$cart->product->name} tidak mencukupi.");
            }
        }

        DB::beginTransaction();
        try {
            $subtotal     = $carts->sum(fn($c) => $c->subtotal);
            $shippingCost = 0; // Bebas Ongkir Digital
            $total        = $subtotal + $shippingCost;

            $order = Order::create([
                'invoice_number'       => 'INV-' . strtoupper(Str::random(8)) . '-' . date('Ymd'),
                'user_id'              => auth()->id(),
                'subtotal'             => $subtotal,
                'shipping_cost'        => $shippingCost,
                'total_price'          => $total,
                'status'               => 'pending',
                'shipping_name'        => $request->shipping_name ?: auth()->user()->name,
                'shipping_address'     => 'Pengiriman Digital (WhatsApp / Email)',
                'shipping_phone'       => $request->shipping_phone,
                'shipping_city'        => 'Digital',
                'shipping_province'    => 'Online',
                'shipping_postal_code' => '00000',
                'payment_method'       => $request->payment_method,
                'notes'                => $request->notes,
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $cart->product_id,
                    'product_name'  => $cart->product->name,
                    'product_image' => $cart->product->image,
                    'quantity'      => $cart->quantity,
                    'price'         => $cart->product->effective_price,
                    'subtotal'      => $cart->subtotal,
                ]);

                $cart->product->decrement('stock', $cart->quantity);
                $cart->product->increment('sold_count', $cart->quantity);
            }

            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            // STEP 1 COMPLETE: Redirect to Step 2 (QRIS Payment & Proof Upload)
            return redirect()->route('orders.pay', $order->id)
                ->with('success', "Pesanan dibuat! Silakan bayar via QRIS DANA di bawah ini.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function index(Request $request)
    {
        $query = Order::with('items')->where('user_id', auth()->id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders       = $query->paginate(10);
        $statusLabels = Order::$statusLabels;

        return view('user.orders.index', compact('orders', 'statusLabels'));
    }

    // STEP 2: Dedicated Payment & Proof Upload Page
    public function pay(int $id)
    {
        $order = Order::with('items.product')->where('user_id', auth()->id())->findOrFail($id);
        return view('user.orders.pay', compact('order'));
    }

    // STEP 3: Dedicated Live Chat Room with Admin
    public function chatRoom(int $id)
    {
        $order = Order::with(['items.product', 'chats.user', 'digitalItems'])->where('user_id', auth()->id())->findOrFail($id);
        return view('user.orders.chat', compact('order'));
    }

    public function show(int $id)
    {
        $order = Order::with(['items.product', 'reviews', 'chats.user', 'digitalItems'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($order->status === 'pending' && !$order->payment_proof) {
            return redirect()->route('orders.pay', $order->id);
        }

        return redirect()->route('orders.chatroom', $order->id);
    }

    public function uploadProof(Request $request, int $id)
    {
        $request->validate([
            'payment_proof' => 'required|file|image|mimes:png,jpg,jpeg,webp|max:5120',
        ], [
            'payment_proof.max'   => 'Ukuran file bukti pembayaran maksimal 5 MB sebelum dikompresi!',
            'payment_proof.mimes' => 'Format file harus berupa PNG, JPG, JPEG, atau WebP!',
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        // Auto compress 5MB -> ~100KB with ImageCompressor Service
        $path = ImageCompressor::compressAndStore($request->file('payment_proof'), 'payment_proofs');
        $order->update(['payment_proof' => $path]);

        // Auto send chat message with attachment
        \App\Models\OrderChat::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'sender_role' => 'customer',
            'message'     => 'Saya telah mengupload bukti pembayaran via DANA/QRIS.',
            'attachment'  => $path,
        ]);

        // STEP 2 COMPLETE: Immediately redirect to Step 3 (Chat Room with Admin)
        return redirect()->route('orders.chatroom', $order->id)
            ->with('success', 'Bukti pembayaran berhasil diupload & dikompresi otomatis!');
    }

    public function sendChat(Request $request, int $id)
    {
        $request->validate([
            'message'    => 'required_without:attachment|nullable|string|max:1000',
            'attachment' => 'nullable|file|image|mimes:png,jpg,jpeg,webp|max:5120',
        ], [
            'attachment.max'   => 'Ukuran file lampiran maksimal 5 MB!',
            'attachment.mimes' => 'Format file harus berupa PNG, JPG, JPEG, atau WebP!',
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = ImageCompressor::compressAndStore($request->file('attachment'), 'chat_attachments');
        }

        \App\Models\OrderChat::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'sender_role' => 'customer',
            'message'     => $request->message ?? 'Mengirimkan lampiran.',
            'attachment'  => $path,
        ]);

        return back()->with('success', 'Pesan terkirim ke Admin!');
    }

    public function cancel(int $id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        if (!in_array($order->status, ['pending', 'paid'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan pada status ini.');
        }

        foreach ($order->items as $item) {
            Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            Product::where('id', $item->product_id)->decrement('sold_count', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
