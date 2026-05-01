<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'city',
        'latitude',
        'longitude',
        'profile_photo',
        'farm_description',
        'is_public_profile',
        'social_provider',
        'social_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_public_profile' => 'boolean',
        ];
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // Relationships
    public function listings()
    {
        return $this->hasMany(Listing::class, 'user_user_id', 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_user_id', 'user_id');
    }



    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'user_user_id', 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'user_user_id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_user_id', 'user_id');
    }

    // Farmer-specific helpers
    public function averageRating()
    {
        return Rating::whereHas('listing', function ($q) {
            $q->where('user_user_id', $this->user_id);
        })->avg('score');
    }

    public function calculateScore()
    {
        // average rating 
        $avgRating = $this->averageRating() ?? 0;

        // total sales 
        $totalSales = $this->transactions()->count();

        // simple weighted formula
        $score = ($avgRating * 0.7) + ($totalSales * 0.3);

        return round($score, 2);
    }

    public function totalListings()
    {
        return $this->listings()->count();
    }

    public function getOrCreateCart()
    {
        return $this->cart ?? Cart::create(['user_user_id' => $this->user_id]);
    }

}
