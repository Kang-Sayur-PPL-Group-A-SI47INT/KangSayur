<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingStockLog extends Model
{
    protected $fillable = [
        'listing_id',
        'quantity',
        'source',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id', 'listing_id');
    }
}
