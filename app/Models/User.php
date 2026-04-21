<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
<<<<<<< Updated upstream
=======

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

    public function offers()
    {
        return $this->hasMany(Offer::class, 'user_user_id', 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_user_id', 'user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_user_id', 'user_id');
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
        })->avg('rating');
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
>>>>>>> Stashed changes
}
