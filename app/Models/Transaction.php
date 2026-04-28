<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stub model for Transaction — will be fully implemented in PBI #9.
 */
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
}
