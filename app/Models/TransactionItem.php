<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $primaryKey = 'transaction_item_id';

    protected $fillable = [
        'quantity',
        'unit_price',
        'subtotal',
        'transaction_transaction_id',
        'listing_listing_id',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_transaction_id', 'transaction_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_listing_id', 'listing_id');
    }
}
