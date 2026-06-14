<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $primaryKey = 'offer_id';

    protected $fillable = [
        'offered_price',
        'counter_price',
        'status',
        'listing_listing_id',
        'user_user_id',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_listing_id', 'listing_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'offer_offer_id', 'offer_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isCountered(): bool
    {
        return $this->status === 'countered';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the current active price (counter or original offer).
     */
    public function currentPrice()
    {
        return $this->counter_price ?? $this->offered_price;
    }
}
