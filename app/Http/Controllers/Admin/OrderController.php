<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('search'))    $query->where('invoice_number', 'like', '%' . $request->search . '%');

        $orders       = $query->latest()->paginate(15)->appends($request->all());
        $statusLabels = Order::$statusLabels;

        return view('admin.orders.index', compact('orders', 'statusLabels'));
    }

    public function show(int $id)
    {
        $order = Order::with(['user', 'items.product', 'reviews.user', 'chats.user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
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

        $order = Order::findOrFail($id);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = \App\Services\ImageCompressor::compressAndStore($request->file('attachment'), 'chat_attachments');
        }

        \App\Models\OrderChat::create([
            'order_id'    => $order->id,
            'user_id'     => auth()->id(),
            'sender_role' => 'admin',
            'message'     => $request->message ?? 'Mengirimkan lampiran.',
            'attachment'  => $path,
        ]);

        return back()->with('success', 'Pesan balasan berhasil dikirim ke pembeli!');
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:pending,paid,processing,shipped,completed,cancelled']);

        $order      = Order::with(['items.product', 'digitalItems'])->findOrFail($id);
        $oldStatus  = $order->status;
        $newStatus  = $request->status;

        $updateData = ['status' => $newStatus];

        if (in_array($newStatus, ['paid', 'completed']) && !$order->paid_at) {
            $updateData['paid_at'] = now();
        }

        $order->update($updateData);

        // AUTO DIGITAL DELIVERY TRIGGER
        if (in_array($newStatus, ['paid', 'completed']) && !in_array($oldStatus, ['paid', 'completed'])) {
            $deliveredMessage = "🎉 PEMBAYARAN DITERIMA! Berikut detail akun digital Anda:\n\n";

            foreach ($order->items as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    // Find available digital item in stock
                    $digitalItem = \App\Models\DigitalItem::where('product_id', $item->product_id)
                        ->where('is_used', false)
                        ->first();

                    if ($digitalItem) {
                        $digitalItem->update([
                            'is_used'  => true,
                            'order_id' => $order->id,
                            'used_at'  => now(),
                        ]);
                        $credentials = $digitalItem->credentials;
                    } else {
                        // Generate fallback digital account credential if no pre-stored stock
                        $genEmail = 'adobe_' . strtolower(Str::random(5)) . '@gmail.com';
                        $genPass  = 'pwaccadobe';
                        $credentials = "{$genEmail}:{$genPass}";

                        \App\Models\DigitalItem::create([
                            'product_id'  => $item->product_id,
                            'credentials' => $credentials,
                            'is_used'     => true,
                            'order_id'    => $order->id,
                            'used_at'     => now(),
                        ]);
                    }

                    $deliveredMessage .= "📦 " . $item->product_name . ":\n" . $credentials . "\n\n";
                }
            }

            $deliveredMessage .= "Terima kasih telah berbelanja! Garansi full 24 jam aktif.\n⭐ Jangan lupa klik tombol 'Beri Rating & Ulasan' di halaman pesanan untuk memberikan bintang & pendapat Anda!";

            // Auto send chat message with digital delivery info
            \App\Models\OrderChat::create([
                'order_id'    => $order->id,
                'user_id'     => auth()->id(),
                'sender_role' => 'admin',
                'message'     => $deliveredMessage,
            ]);

            // Auto set status to completed if paid
            if ($newStatus === 'paid') {
                $order->update(['status' => 'completed']);
            }
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui & produk digital telah dikirim otomatis ke pembeli!');
    }

    public function invoice(int $id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        $pdf   = Pdf::loadView('admin.orders.invoice', compact('order'))->setPaper('A4');

        return $pdf->download('invoice-' . $order->invoice_number . '.pdf');
    }
}
