<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $primaryKey = 'rating_id';

    protected $fillable = [
        'score',
        'comment',
        'listing_listing_id',
        'user_user_id',
    ];

    /**
     * Get the listing this rating belongs to.
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_listing_id', 'listing_id');
    }

    /**
     * Get the user who made this rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'id');
    }
}
