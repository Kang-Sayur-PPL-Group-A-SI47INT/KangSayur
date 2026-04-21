<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $primaryKey = 'listing_id';

    protected $fillable = [
        'title',
        'content',
        'price',
        'quantity',
        'unit',
        'status',
        'image',
        'availability_date',
        'produce_produce_id',
        'user_user_id',
    ];

    protected function casts(): array
    {
        return [
            'availability_date' => 'date',
        ];
    }

    public function produce()
    {
        return $this->belongsTo(Produce::class, 'produce_produce_id', 'produce_id');
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'listing_listing_id', 'listing_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'listing_listing_id', 'listing_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'listing_listing_id', 'listing_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'listing_listing_id', 'listing_id');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('score');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if listing has active (pending/paid) orders.
     */
    public function hasActiveOrders(): bool
    {
        return $this->cartItems()
            ->whereHas('cart.transaction', function ($q) {
                $q->whereIn('status', ['pending', 'paid']);
            })->exists();
    }

    /**
     * Get stock status label.
     */
    public function stockStatus(): string
    {
        $qty = (int) $this->quantity;
        if ($qty <= 0 || $this->status === 'sold_out') return 'Sold Out';
        if ($qty <= 10) return 'Low Stock';
        return 'In Stock';
    }

    /**
     * Get images as array (supports JSON or single path).
     */
    public function getImagesArray(): array
    {
        if (!$this->image) return [];
        $decoded = json_decode($this->image, true);
        return is_array($decoded) ? $decoded : [$this->image];
    }
}
