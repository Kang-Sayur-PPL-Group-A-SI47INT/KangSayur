<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'total_price',
        'delivery_fee',
        'delivery_name',
        'delivery_phone',
        'delivery_address',
        'status',
        'midtrans_order_id',
        'snap_token',
        'payment_type',
        'paid_at',
        'user_user_id',
        'cart_cart_id',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_cart_id', 'cart_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_transaction_id', 'transaction_id');
    }

    /**
     * Check if the transaction is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'processing', 'shipped', 'delivered']);
    }

    /**
     * Get a human-readable status label.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Awaiting Payment',
            'paid' => 'Paid',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge CSS classes.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'paid' => 'bg-blue-100 text-blue-700',
            'processing' => 'bg-indigo-100 text-indigo-700',
            'shipped' => 'bg-purple-100 text-purple-700',
            'delivered' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get the grand total (total_price + delivery_fee).
     */
    public function grandTotal(): float
    {
        return $this->total_price + $this->delivery_fee;
    }
}
