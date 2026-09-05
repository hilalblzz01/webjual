<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #FF6B00; }
        .brand { font-size: 22px; font-weight: bold; color: #FF6B00; }
        .brand-sub { color: #666; font-size: 12px; }
        .invoice-info { text-align: right; }
        .invoice-num { font-size: 18px; font-weight: bold; color: #333; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; background: #FFF0E6; color: #FF6B00; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .info-box h4 { font-size: 11px; text-transform: uppercase; color: #999; margin-bottom: 8px; letter-spacing: 1px; }
        .info-box p { margin-bottom: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #FF6B00; color: white; padding: 10px 12px; text-align: left; font-size: 12px; }
        td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; }
        tr:nth-child(even) td { background: #FFF8F5; }
        .totals { margin-left: auto; width: 280px; }
        .totals tr td { border: none; padding: 5px 0; }
        .totals tr td:last-child { text-align: right; font-weight: bold; }
        .totals .grand-total td { font-size: 15px; color: #FF6B00; border-top: 2px solid #FF6B00; padding-top: 8px; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 11px; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">🛍 TokoKita</div>
            <div class="brand-sub">Platform Belanja Online Terpercaya</div>
        </div>
        <div class="invoice-info">
            <div class="invoice-num">INVOICE</div>
            <p>{{ $order->invoice_number }}</p>
            <p>Tanggal: {{ $order->created_at->format('d M Y') }}</p>
            <span class="status-badge">{{ $order->status_label }}</span>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h4>Informasi Pelanggan</h4>
            <p><strong>{{ $order->user->name }}</strong></p>
            <p>{{ $order->user->email }}</p>
        </div>
        <div class="info-box">
            <h4>Alamat Pengiriman</h4>
            <p><strong>{{ $order->shipping_name }}</strong></p>
            <p>{{ $order->shipping_phone }}</p>
            <p>{{ $order->shipping_address }}</p>
            <p>{{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Ongkos Kirim</td>
            <td>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>TOTAL</td>
            <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
        </tr>
    </table>

    <p><strong>Metode Pembayaran:</strong> {{ $order->payment_label }}</p>
    @if($order->paid_at)
    <p><strong>Dibayar pada:</strong> {{ $order->paid_at->format('d M Y H:i') }}</p>
    @endif

    @if($order->notes)
    <p style="margin-top: 10px;"><strong>Catatan:</strong> {{ $order->notes }}</p>
    @endif

    <div class="footer">
        <p>Terima kasih telah berbelanja di TokoKita!</p>
        <p>Invoice ini dibuat secara otomatis pada {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
