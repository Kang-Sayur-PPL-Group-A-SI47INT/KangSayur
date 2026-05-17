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
        'verification_status',
        'rejection_note',
        'doc_skp',
        'doc_nib',
        'doc_ktp',
        'doc_skt',
        'doc_land_cert',
        'verified_at',
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
            'verified_at' => 'datetime',
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
    // Verification helpers
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
    public function isPendingVerification(): bool
    {
        return $this->verification_status === 'pending';
    }
    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }
    public function isUnverified(): bool
    {
        return $this->verification_status === 'unverified';
    }
    /**
     * Check if all required verification documents have been uploaded.
     */
    public function hasAllDocuments(): bool
    {
        return $this->doc_skp
            && $this->doc_nib
            && $this->doc_ktp
            && $this->doc_skt
            && $this->doc_land_cert;
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

    public function totalListings()
    {
        return $this->listings()->count();
    }
    public function getOrCreateCart()
    {
        return $this->cart ?? Cart::create(['user_user_id' => $this->user_id]);
    }
}