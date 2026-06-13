<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedIdentifier extends Model
{
    protected $table = 'banned_identifiers';

    protected $fillable = [
        'type',
        'value',
        'user_user_id',
        'banned_by',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function bannedByAdmin()
    {
        return $this->belongsTo(User::class, 'banned_by', 'user_id');
    }
}
