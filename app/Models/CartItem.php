<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $primaryKey = 'cart_item_id';

    protected $fillable = [
        'quantity',
        'cart_cart_id',
        'listing_listing_id',
        'offer_offer_id',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_cart_id', 'cart_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_listing_id', 'listing_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_offer_id', 'offer_id');
    }

    /**
     * Get effective unit price (offer price if accepted, else listing price).
     */
    public function unitPrice(): float
    {
        if ($this->offer && $this->offer->status === 'accepted') {
            return $this->offer->offered_price;
        }

        return $this->listing->price;
    }

    /**
     * Alias for unitPrice() — used by checkout view.
     */
    public function effectivePrice(): float
    {
        return $this->unitPrice();
    }

    /**
     * Get subtotal for this cart item.
     */
    public function subtotal(): float
    {
        return $this->quantity * $this->unitPrice();
    }
}
