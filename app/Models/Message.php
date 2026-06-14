<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $primaryKey = 'message_id';

    protected $fillable = [
        'content',
        'sender_user_id',
        'receiver_user_id',
        'user_user_id',
        'offer_offer_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id', 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_user_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_offer_id', 'offer_id');
    }
}
