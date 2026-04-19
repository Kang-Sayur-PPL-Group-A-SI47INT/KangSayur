<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $primaryKey = 'listing_id';

    protected $fillable = [
        'title',
        'description',
        'price',
        'unit',
        'status',
        'image',
        'user_user_id',
        'produce_produce_id',
    ];

    /**
     * Get the farmer (user) who owns this listing.
     */
    public function farmer()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'id');
    }

    /**
     * Get the produce type for this listing.
     */
    public function produce()
    {
        return $this->belongsTo(Produce::class, 'produce_produce_id', 'produce_id');
    }

    /**
     * Get all ratings for this listing.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'listing_listing_id', 'listing_id');
    }

    /**
     * Get the average rating for this listing.
     */
    public function getAvgRatingAttribute()
    {
        return $this->ratings->avg('score') ?? 0;
    }
}
