<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produce extends Model
{
    use HasFactory;

    protected $table = 'produces';
    protected $primaryKey = 'produce_id';

    protected $fillable = [
        'name',
        'category',
        'image',
        'emoji',
    ];

    /**
     * Get all listings for this produce.
     */
    public function listings()
    {
        return $this->hasMany(Listing::class, 'produce_produce_id', 'produce_id');
    }
}
