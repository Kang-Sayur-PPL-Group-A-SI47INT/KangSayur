<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'user_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_cart_id', 'cart_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'cart_cart_id', 'cart_id');
    }

    /**
     * Calculate total price of all items in cart.
     */
    public function totalPrice(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->listing->price;
        });
    }
}
