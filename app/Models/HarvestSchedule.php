<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestSchedule extends Model
{
    protected $fillable = [
        'listing_id',
        'availability_date',
        'estimated_stock',
    ];

    protected function casts(): array
    {
        return [
            'availability_date' => 'date',
        ];
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id', 'listing_id');
    }

    /**
     * Check if this harvest schedule is in the past.
     */
    public function isPast(): bool
    {
        return $this->availability_date->lt(today());
    }
}
