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
     * Uses offer price if an accepted offer is linked, otherwise listing price.
     */
    public function totalPrice(): float
    {
        return $this->items->sum(function ($item) {
            $price = $item->offer && $item->offer->status === 'accepted'
                ? $item->offer->offered_price
                : $item->listing->price;

            return $item->quantity * $price;
        });
    }
}
