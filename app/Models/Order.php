<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'subtotal',
        'shipping_cost',
        'total_price',
        'status',
        'shipping_name',
        'shipping_address',
        'shipping_phone',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'payment_method',
        'payment_proof',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Status labels in Indonesian.
     */
    public static array $statusLabels = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Sudah Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    /**
     * Status badge colors.
     */
    public static array $statusColors = [
        'pending' => 'yellow',
        'paid' => 'blue',
        'processing' => 'orange',
        'shipped' => 'purple',
        'completed' => 'green',
        'cancelled' => 'red',
    ];

    /**
     * Payment method labels.
     */
    public static array $paymentLabels = [
        'transfer_bank' => 'Transfer Bank',
        'e_wallet' => 'E-Wallet',
        'cod' => 'Bayar di Tempat (COD)',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'gray';
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::$paymentLabels[$this->payment_method] ?? $this->payment_method;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function chats()
    {
        return $this->hasMany(OrderChat::class);
    }

    public function digitalItems()
    {
        return $this->hasMany(DigitalItem::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Auto cancel pending orders older than 1 hour & release stock back.
     */
    public static function cancelExpiredOrders(): void
    {
        $expiredOrders = self::where('status', 'pending')
            ->where('created_at', '<=', now()->subHour())
            ->get();

        foreach ($expiredOrders as $order) {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                Product::where('id', $item->product_id)->decrement('sold_count', $item->quantity);
            }
            $order->update(['status' => 'cancelled']);
        }
    }
}
