<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stub model for Offer — will be fully implemented in PBI #9.
 */
class Offer extends Model
{
    protected $primaryKey = 'offer_id';

    protected $fillable = [
        'offered_price',
        'status',
        'counter_price',
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
}
