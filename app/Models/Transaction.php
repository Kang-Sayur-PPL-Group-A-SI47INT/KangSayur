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
        'snap_token',
        'midtrans_order_id',
        'user_user_id',
        'cart_cart_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_cart_id', 'cart_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get items through cart.
     */
    public function items()
    {
        return $this->cart ? $this->cart->items : collect();
    }

    /**
     * Get grand total (items + delivery).
     */
    public function grandTotal(): int
    {
        return (int) $this->total_price + (int) $this->delivery_fee;
    }
}
